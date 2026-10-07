<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendancePunch;
use App\Models\Branch;
use App\Models\User;
use App\Models\Worker;
use App\Support\AttendanceClock;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Admin / Super Admin only: live board, history, corrections and who is tracked.
 */
class AttendanceAdminController extends Controller
{
    public function __construct(protected AttendanceClock $clock)
    {
    }

    /**
     * Who is working right now, and a timeline of every check-in / check-out for the day.
     */
    public function board(Request $request)
    {
        $date = $request->date('date') ?? today();
        $isToday = $date->isToday();
        $branchId = $request->query('branch_id');
        $branches = Branch::orderBy('name')->get();

        $shifts = Attendance::with(['attendable' => $this->withRole()])
            ->whereDate('work_date', $date->toDateString())
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->orderBy('check_in_at')
            ->get()
            ->filter(fn (Attendance $shift) => $shift->attendable);

        $rows = $this->rows($this->clock->trackedPeople($branchId), $shifts);

        $missed = Attendance::with('attendable')->open()
            ->whereDate('work_date', '<', today()->toDateString())
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->orderByDesc('work_date')
            ->get()
            ->filter(fn (Attendance $shift) => $shift->attendable);

        $summary = [
            'labourers_in' => $rows->where('status', 'in')->where('type', 'worker')->count(),
            'labourers' => $rows->where('type', 'worker')->count(),
            'staff_in' => $rows->where('status', 'in')->where('type', 'user')->count(),
            'staff' => $rows->where('type', 'user')->count(),
            'out' => $rows->where('status', 'out')->count(),
            'absent' => $rows->where('status', 'absent')->count(),
            'minutes' => $rows->sum('minutes'),
            'fingerprint' => $shifts->where('check_in_method', 'fingerprint')->count(),
        ];

        // Timeline window: at least 6 AM – 7 PM, stretched to fit early or late shifts.
        $end = $isToday ? now() : $date->copy()->endOfDay();
        $windowStart = $date->copy()->setTime(6, 0);
        $windowEnd = $date->copy()->setTime(19, 0);
        foreach ($shifts as $shift) {
            if ($shift->check_in_at->lt($windowStart)) {
                $windowStart = $shift->check_in_at->copy()->startOfHour();
            }
            $last = $shift->check_out_at ?? ($isToday ? now() : $shift->check_in_at);
            if ($last->gt($windowEnd)) {
                $windowEnd = $last->copy()->addHour()->startOfHour();
            }
        }
        $windowEnd = $windowEnd->min($date->copy()->endOfDay());

        // Head count for every hour of the window, split into labourers and staff.
        $hours = collect();
        for ($hour = $windowStart->copy(); $hour->lt($windowEnd); $hour->addHour()) {
            $slotEnd = $hour->copy()->addHour();
            $inSlot = $shifts->filter(fn (Attendance $shift) => $shift->check_in_at->lt($slotEnd)
                && ($shift->check_out_at ?? ($isToday ? now() : $shift->check_in_at))->gt($hour)
                && $hour->lte($end));
            $hours->push([
                'label' => $hour->format('ga'),
                'workers' => $inSlot->where('attendable_type', 'worker')->unique('attendable_id')->count(),
                'staff' => $inSlot->where('attendable_type', 'user')->unique('attendable_id')->count(),
            ]);
        }

        $events = $shifts->flatMap(fn (Attendance $shift) => array_filter([
            ['shift' => $shift, 'kind' => 'in', 'at' => $shift->check_in_at, 'method' => $shift->check_in_method],
            $shift->check_out_at ? ['shift' => $shift, 'kind' => 'out', 'at' => $shift->check_out_at, 'method' => $shift->check_out_method] : null,
        ]))->sortByDesc('at')->take(20)->values();

        return view('frontend.attendance.board.index', compact(
            'date', 'isToday', 'branchId', 'branches', 'rows', 'summary', 'missed', 'windowStart', 'windowEnd', 'hours', 'events'
        ));
    }

    /**
     * Days and hours per person over a date range, with every shift listed below.
     */
    public function report(Request $request)
    {
        $from = $request->date('from') ?? today()->startOfMonth();
        $to = $request->date('to') ?? today();
        $branchId = $request->query('branch_id');
        $type = in_array($request->query('type'), ['worker', 'user'], true) ? $request->query('type') : null;
        $person = $request->query('person');
        $branches = Branch::orderBy('name')->get();

        $shifts = Attendance::with(['attendable' => $this->withRole(), 'checkedInBy', 'checkedOutBy'])
            ->whereDate('work_date', '>=', $from->toDateString())
            ->whereDate('work_date', '<=', $to->toDateString())
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->when($type, fn ($query) => $query->where('attendable_type', $type))
            ->orderByDesc('work_date')
            ->orderByDesc('check_in_at')
            ->get()
            ->filter(fn (Attendance $shift) => $shift->attendable);

        $summary = $shifts->groupBy(fn (Attendance $shift) => $shift->attendable_type.':'.$shift->attendable_id)
            ->map(function (Collection $personShifts, string $key) {
                $first = $personShifts->first();
                $firstIns = $personShifts->groupBy(fn ($shift) => $shift->work_date->toDateString())
                    ->map(fn ($day) => $day->min(fn ($shift) => $shift->check_in_at->hour * 60 + $shift->check_in_at->minute));
                $avgIn = (int) round($firstIns->avg());

                return [
                    'key' => $key,
                    'person' => $first->attendable,
                    'type' => $first->attendable_type,
                    'days' => $firstIns->count(),
                    'minutes' => $personShifts->sum(fn (Attendance $shift) => $shift->minutes()),
                    'avg_in' => sprintf('%02d:%02d', intdiv($avgIn, 60), $avgIn % 60),
                    'missed' => $personShifts->filter(fn (Attendance $shift) => $shift->missedCheckOut())->count(),
                    'fingerprint' => $personShifts->where('check_in_method', 'fingerprint')->count(),
                ];
            })
            ->sortBy(fn ($row) => [$row['type'] === 'worker' ? 0 : 1, $row['person']->name])
            ->values();

        $log = $shifts->when($person, fn ($shifts) => $shifts->filter(
            fn (Attendance $shift) => $shift->attendable_type.':'.$shift->attendable_id === $person
        ))->take(500)->values();

        return view('frontend.attendance.report.index', compact('from', 'to', 'branchId', 'type', 'person', 'branches', 'summary', 'log'));
    }

    public function edit(Attendance $attendance)
    {
        $attendance->load('attendable');

        return view('frontend.attendance.edit.index', compact('attendance'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'check_in_at' => ['required', 'date', 'before_or_equal:now'],
            'check_out_at' => ['nullable', 'date', 'after:check_in_at', 'before_or_equal:now'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $checkIn = Carbon::parse($validated['check_in_at']);
        $checkOut = isset($validated['check_out_at']) ? Carbon::parse($validated['check_out_at']) : null;

        $attendance->update([
            'work_date' => $checkIn->toDateString(),
            'check_in_at' => $checkIn,
            'check_out_at' => $checkOut,
            'check_out_method' => $checkOut ? ($attendance->check_out_method ?? 'manual') : null,
            'check_out_by' => $checkOut ? ($attendance->check_out_by ?? $request->user()->id) : null,
            'notes' => $validated['notes'] ?? null,
        ]);

        // A fixed missed check-out still needs the labourer's day on the work sheet.
        if ($checkOut && $attendance->isWorker()) {
            $this->clock->recordWorkDay($attendance, null, null, $request->user());
        }

        return redirect()->route('attendance.report', [
            'from' => $attendance->work_date->toDateString(),
            'to' => $attendance->work_date->toDateString(),
        ])->with('success', __('Attendance updated for :name.', ['name' => $attendance->attendable->name]));
    }

    public function destroy(Attendance $attendance)
    {
        $name = $attendance->attendable?->name;
        $attendance->delete();

        return back()->with('success', __('Attendance record deleted for :name.', ['name' => $name]));
    }

    /**
     * Tick who checks in and out. Admins and super admins are never listed.
     */
    public function people()
    {
        $workers = Worker::where('is_active', true)->with('branch')->orderBy('name')->get();
        $staff = User::attendanceStaff()->with(['role', 'branch'])->orderBy('name')->get();
        $punches = AttendancePunch::latest('id')->take(15)->get();

        return view('frontend.attendance.people.index', compact('workers', 'staff', 'punches'));
    }

    public function updatePeople(Request $request)
    {
        $validated = $request->validate([
            'workers' => ['nullable', 'array'],
            'workers.*' => ['integer'],
            'users' => ['nullable', 'array'],
            'users.*' => ['integer'],
        ]);

        $workerIds = $validated['workers'] ?? [];
        $userIds = $validated['users'] ?? [];

        Worker::where('is_active', true)->whereIn('id', $workerIds)->update(['track_attendance' => true]);
        Worker::where('is_active', true)->whereNotIn('id', $workerIds)->update(['track_attendance' => false]);

        $staffIds = User::attendanceStaff()->pluck('id');
        User::whereIn('id', $staffIds->intersect($userIds))->update(['track_attendance' => true]);
        User::whereIn('id', $staffIds->diff($userIds))->update(['track_attendance' => false]);

        return redirect()->route('attendance.people')->with('success', __(':count people will check in and out.', [
            'count' => count($workerIds) + $staffIds->intersect($userIds)->count(),
        ]));
    }

    /** Eager-load the staff member's role (labourers have none). */
    protected function withRole(): \Closure
    {
        return fn (MorphTo $morph) => $morph->morphWith([User::class => ['role']]);
    }

    /**
     * One row per person: their shifts that day, whether they are in now and total time.
     */
    protected function rows(Collection $people, Collection $shifts): Collection
    {
        $byPerson = $shifts->groupBy(fn (Attendance $shift) => $shift->attendable_type.':'.$shift->attendable_id);

        // Also show anyone with a shift that day who is no longer ticked.
        $people = $people->keyBy(fn ($person) => AttendanceClock::typeOf($person).':'.$person->id);
        foreach ($byPerson as $key => $personShifts) {
            $people->put($key, $people->get($key) ?? $personShifts->first()->attendable);
        }

        return $people->map(function ($person, $key) use ($byPerson) {
            $personShifts = $byPerson->get($key, collect());
            $open = $personShifts->first(fn (Attendance $shift) => $shift->isOpen());

            return [
                'person' => $person,
                'type' => AttendanceClock::typeOf($person),
                'shifts' => $personShifts,
                'open' => $open,
                'status' => $open ? ($open->missedCheckOut() ? 'missed' : 'in') : ($personShifts->isNotEmpty() ? 'out' : 'absent'),
                'minutes' => $personShifts->sum(fn (Attendance $shift) => $shift->minutes()),
            ];
        })->sortBy(fn ($row) => [['in' => 0, 'missed' => 1, 'out' => 2, 'absent' => 3][$row['status']], $row['type'] === 'worker' ? 0 : 1, $row['person']->name])->values();
    }
}
