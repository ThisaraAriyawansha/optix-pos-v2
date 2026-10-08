<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Product;
use App\Models\User;
use App\Models\WorkActivity;
use App\Models\WorkEntry;
use App\Models\Worker;
use App\Support\AttendanceClock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Today's check-in / check-out sheet, used by whoever runs the gate or the office.
 */
class AttendanceController extends Controller
{
    public function __construct(protected AttendanceClock $clock)
    {
    }

    public function index(Request $request)
    {
        $branchId = $request->query('branch_id', $request->user()->branch_id);
        $type = in_array($request->query('type'), ['worker', 'user'], true) ? $request->query('type') : null;
        $branches = Branch::orderBy('name')->get();

        $branchNames = $branches->pluck('name', 'id');
        $key = fn ($person) => AttendanceClock::typeOf($person).':'.$person->id;

        $todayShifts = Attendance::with('attendable')->whereDate('work_date', today())
            ->orderBy('check_in_at')
            ->get()
            ->filter(fn (Attendance $shift) => $shift->attendable);
        $shifts = $todayShifts->groupBy(fn (Attendance $shift) => $shift->attendable_type.':'.$shift->attendable_id);

        // The branch's own people, plus anyone from another branch working here today.
        $people = $this->clock->trackedPeople($branchId, $type);
        if ($branchId) {
            $visitors = $todayShifts->where('branch_id', $branchId)
                ->when($type, fn ($shifts) => $shifts->where('attendable_type', $type))
                ->pluck('attendable');
            $people = $people->concat($visitors)->unique($key)->values();
        }

        // People from other branches who can be checked in here for the day.
        $onSheet = $people->map($key)->flip();
        $otherPeople = $branchId
            ? $this->clock->trackedPeople(null, $type)->reject(fn ($person) => $onSheet->has($key($person)))->values()
            : collect();

        $workEntries = WorkEntry::with('items')->whereDate('work_date', today())
            ->whereIn('worker_id', $people->whereInstanceOf(Worker::class)->pluck('id'))
            ->get()->keyBy('worker_id');

        $rows = $people->map(function ($person) use ($shifts, $workEntries, $branchNames) {
            $type = AttendanceClock::typeOf($person);
            $personShifts = $shifts->get($type.':'.$person->id, collect());
            $open = $personShifts->first(fn (Attendance $shift) => $shift->isOpen());
            $workedAt = ($open ?? $personShifts->last())?->branch_id;

            return [
                // Set when today's work is at a branch other than their home one.
                'workedAt' => $workedAt && (int) $workedAt !== (int) $person->branch_id ? $branchNames->get($workedAt) : null,
                'home' => $branchNames->get($person->branch_id),
                'person' => $person,
                'type' => $type,
                'shifts' => $personShifts,
                'open' => $open,
                'status' => $open ? 'in' : ($personShifts->isNotEmpty() ? 'out' : 'absent'),
                'minutes' => $personShifts->sum(fn (Attendance $shift) => $shift->minutes()),
                'workEntry' => $type === 'worker' ? $workEntries->get($person->id) : null,
            ];
        });

        $summary = [
            'in' => $rows->where('status', 'in')->count(),
            'out' => $rows->where('status', 'out')->count(),
            'absent' => $rows->where('status', 'absent')->count(),
            'labourers_in' => $rows->where('status', 'in')->where('type', 'worker')->count(),
        ];

        // Working first, then not yet in, then gone home.
        $rows = $rows->sortBy(fn ($row) => [['in' => 0, 'absent' => 1, 'out' => 2][$row['status']], $row['person']->name])->values();

        return view('frontend.attendance.main.index', compact('rows', 'summary', 'branches', 'branchId', 'type', 'otherPeople', 'branchNames'));
    }

    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['worker', 'user'])],
            'id' => ['required', 'integer'],
            // The branch they are working at today; their home branch when not given.
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
        ]);

        $person = $this->trackedPerson($validated['type'], $validated['id']);

        if ($this->clock->openShift($person)) {
            return back()->with('error', __(':name is already checked in.', ['name' => $person->name]));
        }

        $branchId = isset($validated['branch_id']) ? (int) $validated['branch_id'] : null;
        $shift = $this->clock->checkIn($person, now(), 'manual', $request->user(), null, $branchId);

        $message = $shift->branch_id && (int) $shift->branch_id !== (int) $person->branch_id
            ? __(':name checked in at :time at :branch.', ['name' => $person->name, 'time' => $shift->check_in_at->format('h:i A'), 'branch' => $shift->branch?->name])
            : __(':name checked in at :time.', ['name' => $person->name, 'time' => $shift->check_in_at->format('h:i A')]);

        return back()->with('success', $message);
    }

    /**
     * Labourers say what they made, loaded or dispatched as they leave.
     */
    public function checkOutForm(Attendance $attendance)
    {
        if (! $attendance->isOpen()) {
            return redirect()->route('attendance')->with('error', __('This shift is already checked out.'));
        }

        if (! $attendance->isWorker()) {
            return redirect()->route('attendance');
        }

        $worker = $attendance->attendable;
        $entry = WorkEntry::with(['items', 'salaryPayment'])->where('worker_id', $worker->id)->whereDate('work_date', $attendance->work_date)->first();

        $products = Product::where('is_active', true)
            ->when($entry, fn ($query) => $query->orWhereIn('id', $entry->items->pluck('product_id')))
            ->with('activityRates')->orderBy('name')->get();
        $activities = WorkActivity::where('is_active', true)
            ->when($entry, fn ($query) => $query->orWhereIn('id', $entry->items->pluck('work_activity_id')))
            ->orderByDesc('is_production')->orderBy('is_dispatch')->orderBy('name')->get();
        $rateMap = $products->mapWithKeys(fn ($product) => [
            $product->id => $activities->mapWithKeys(fn ($activity) => [$activity->id => $product->rateFor($activity)]),
        ]);

        $suggestedMark = $this->clock->suggestedMark($worker, $attendance->work_date, $attendance);

        return view('frontend.attendance.checkout.index', compact('attendance', 'worker', 'entry', 'products', 'activities', 'rateMap', 'suggestedMark'));
    }

    public function checkOut(Request $request, Attendance $attendance)
    {
        if (! $attendance->isOpen()) {
            return redirect()->route('attendance')->with('error', __('This shift is already checked out.'));
        }

        $person = $attendance->attendable;

        $paidDay = $attendance->isWorker() && WorkEntry::where('worker_id', $person->id)
            ->whereDate('work_date', $attendance->work_date)->whereNotNull('salary_payment_id')->exists();

        if (! $attendance->isWorker() || $paidDay) {
            $this->clock->checkOut($attendance, now(), 'manual', $request->user());

            return redirect()->route('attendance')->with('success', __(':name checked out after :hours.', ['name' => $person->name, 'hours' => Attendance::formatMinutes($attendance->minutes())]));
        }

        $validated = $request->validate([
            'attendance' => ['required', Rule::in(array_keys(WorkEntry::ATTENDANCE))],
            'items' => ['nullable', 'array'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.work_activity_id' => ['required', 'exists:work_activities,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01', 'max:9999999'],
        ], [], [
            'items.*.product_id' => __('product'),
            'items.*.work_activity_id' => __('work type'),
            'items.*.quantity' => __('quantity'),
        ]);

        DB::transaction(fn () => $this->clock->checkOut(
            $attendance, now(), 'manual', $request->user(), $validated['attendance'], array_values($validated['items'] ?? [])
        ));

        return redirect()->route('attendance')->with('success', __(':name checked out after :hours. Work saved.', [
            'name' => $person->name,
            'hours' => Attendance::formatMinutes($attendance->minutes()),
        ]));
    }

    protected function trackedPerson(string $type, int $id): Worker|User
    {
        return $type === 'worker'
            ? Worker::attendanceTracked()->findOrFail($id)
            : User::attendanceTracked()->findOrFail($id);
    }
}
