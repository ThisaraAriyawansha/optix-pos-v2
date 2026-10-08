<?php

namespace App\Support;

use App\Models\Attendance;
use App\Models\AttendancePunch;
use App\Models\User;
use App\Models\WorkEntry;
use App\Models\Worker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Check-in / check-out for labourers (Worker) and managing staff (User), whether marked
 * by hand or punched on a fingerprint device. A labourer's check-out also writes their
 * daily work entry, so wages and production keep coming from one place.
 */
class AttendanceClock
{
    /**
     * Open a shift at the branch the person is working at today: the one given (the
     * sheet it was marked on), else the fingerprint device's branch, else their home branch.
     */
    public function checkIn(Worker|User $person, ?Carbon $at = null, string $method = 'manual', ?User $by = null, ?string $deviceSn = null, ?int $branchId = null): Attendance
    {
        $at ??= now();

        return $person->attendances()->create([
            'branch_id' => $branchId ?? config('attendance.device_branches')[$deviceSn] ?? $person->branch_id,
            'work_date' => $at->toDateString(),
            'check_in_at' => $at,
            'check_in_method' => $method,
            'check_in_by' => $by?->id,
            'device_sn' => $deviceSn,
        ]);
    }

    /**
     * Close the shift. For a labourer, $mark and $items (the work done) come from the
     * check-out form; a fingerprint check-out leaves them null and only makes sure the
     * day is on their work sheet.
     */
    public function checkOut(Attendance $attendance, ?Carbon $at = null, string $method = 'manual', ?User $by = null, ?string $mark = null, ?array $items = null): Attendance
    {
        $attendance->update([
            'check_out_at' => $at ?? now(),
            'check_out_method' => $method,
            'check_out_by' => $by?->id,
        ]);

        if ($attendance->isWorker()) {
            $this->recordWorkDay($attendance, $mark, $items, $by);
        }

        return $attendance;
    }

    /** The shift the person is in right now, if any. */
    public function openShift(Worker|User $person, ?Carbon $on = null): ?Attendance
    {
        return $person->attendances()->open()
            ->whereDate('work_date', ($on ?? now())->toDateString())
            ->latest('check_in_at')
            ->first();
    }

    /**
     * "present" once the day's shifts reach the half-day threshold, otherwise "half_day".
     */
    public function suggestedMark(Worker $worker, Carbon $date, ?Attendance $including = null): string
    {
        $minutes = $worker->attendances()->whereDate('work_date', $date->toDateString())->get()
            ->when($including, fn ($shifts) => $shifts->reject(fn ($shift) => $shift->id === $including->id)->push($including))
            ->sum(fn (Attendance $shift) => $shift->minutes());

        return $minutes >= config('attendance.half_day_hours') * 60 ? 'present' : 'half_day';
    }

    /**
     * Put the labourer's day on the daily work sheet. Paid days are never touched.
     * Without a mark (fingerprint check-out) an existing entry is left as it is.
     */
    public function recordWorkDay(Attendance $attendance, ?string $mark = null, ?array $items = null, ?User $by = null): ?WorkEntry
    {
        /** @var Worker $worker */
        $worker = $attendance->attendable;
        $entry = WorkEntry::where('worker_id', $worker->id)->whereDate('work_date', $attendance->work_date)->first();

        if ($entry?->isPaid() || ($entry && $mark === null)) {
            return $entry;
        }

        $branchId = $attendance->branch_id ?? $worker->branch_id ?? $by?->branch_id;
        if (! $branchId) {
            return null;
        }

        $mark ??= $this->suggestedMark($worker, $attendance->work_date);

        if ($entry) {
            $entry->update(['attendance' => $mark]);
        } else {
            $entry = WorkEntry::create([
                'worker_id' => $worker->id,
                'branch_id' => $branchId,
                'work_date' => $attendance->work_date,
                'attendance' => $mark,
                'user_id' => $by?->id,
            ]);
        }

        $entry->syncItems($items ?? $entry->items->map->only(['product_id', 'work_activity_id', 'quantity'])->all());

        return $entry;
    }

    /**
     * A fingerprint punch toggles: checked in → check out, otherwise → check in.
     * A second press within a couple of minutes is ignored.
     *
     * @return array{0: string, 1: ?Attendance} result ('check_in', 'check_out' or 'duplicate') and the shift
     */
    public function punch(Worker|User $person, Carbon $at, ?string $deviceSn = null): array
    {
        $last = $person->attendances()
            ->whereDate('work_date', $at->toDateString())
            ->latest('check_in_at')
            ->first();

        if ($last) {
            $lastEvent = $last->check_out_at ?? $last->check_in_at;

            if (abs($at->diffInMinutes($lastEvent)) < config('attendance.duplicate_minutes') || $at->lt($lastEvent)) {
                return ['duplicate', $last];
            }

            if ($last->isOpen()) {
                return ['check_out', $this->checkOut($last, $at, 'fingerprint')];
            }
        }

        return ['check_in', $this->checkIn($person, $at, 'fingerprint', null, $deviceSn)];
    }

    /**
     * Log one device punch and apply it. Devices re-send their logs, so a punch that is
     * already logged is returned without being applied twice.
     */
    public function recordPunch(string $deviceSn, string $pin, Carbon $at): AttendancePunch
    {
        $at = $at->copy()->startOfSecond();

        $logged = AttendancePunch::where('device_sn', $deviceSn)->where('pin', $pin)->where('punched_at', $at)->first();
        if ($logged) {
            return $logged;
        }

        return DB::transaction(function () use ($deviceSn, $pin, $at) {
            $person = $this->findByPin($pin);
            [$result, $attendance] = $person ? $this->punch($person, $at, $deviceSn) : ['unknown', null];

            return AttendancePunch::create([
                'device_sn' => $deviceSn,
                'pin' => $pin,
                'punched_at' => $at,
                'attendance_id' => $attendance?->id,
                'result' => $result,
            ]);
        });
    }

    /**
     * The tracked person enrolled on the device under this ID. Devices store numbers,
     * so "12" matches EMP-0012; a full code such as "EMP-0012" also matches.
     */
    public function findByPin(string $pin): Worker|User|null
    {
        $pin = trim($pin);
        $codes = array_values(array_filter(array_unique([
            EmployeeCode::normalize($pin),
            ctype_digit($pin) ? EmployeeCode::format((int) $pin) : null,
        ])));

        if (! $codes) {
            return null;
        }

        return Worker::attendanceTracked()->whereIn('code', $codes)->first()
            ?? User::attendanceTracked()->whereIn('employee_code', $codes)->first();
    }

    /** The number to enrol on the fingerprint device for this employee ID. */
    public static function devicePin(?string $code): string
    {
        if ($code && preg_match('/^'.preg_quote(EmployeeCode::PREFIX, '/').'(\d+)$/', $code, $match)) {
            return (string) (int) $match[1];
        }

        return (string) $code;
    }

    /**
     * Everyone who checks in, optionally for one branch and one kind ('worker' or 'user').
     *
     * @return Collection<int, Worker|User>
     */
    public function trackedPeople(?string $branchId = null, ?string $type = null): Collection
    {
        $workers = $type === 'user' ? collect() : Worker::attendanceTracked()
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->orderBy('name')->get();

        $staff = $type === 'worker' ? collect() : User::attendanceTracked()->with('role')
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->orderBy('name')->get();

        return $workers->concat($staff)->values();
    }

    public static function typeOf(Worker|User $person): string
    {
        return $person instanceof Worker ? 'worker' : 'user';
    }

    public static function codeOf(Worker|User $person): ?string
    {
        return $person instanceof Worker ? $person->code : $person->employee_code;
    }

    /** "Worker" or the staff member's role name. */
    public static function roleOf(Worker|User $person): string
    {
        return $person instanceof Worker ? __('Worker') : ($person->role?->name ? __($person->role->name) : __('Staff'));
    }
}
