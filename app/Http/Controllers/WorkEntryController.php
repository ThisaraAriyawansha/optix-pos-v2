<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\WorkActivity;
use App\Models\WorkEntry;
use App\Models\Worker;
use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WorkEntryController extends Controller
{
    /**
     * Day sheet: every worker with the work recorded for the chosen date.
     */
    public function index(Request $request)
    {
        $date = $request->date('date')?->toDateString() ?? now()->toDateString();
        $branchId = $request->query('branch_id', $request->user()->branch_id);
        $branches = Branch::orderBy('name')->get();

        $entries = WorkEntry::query()
            ->with(['items.product', 'items.activity', 'worker'])
            ->whereDate('work_date', $date)
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->get()
            ->keyBy('worker_id');

        // Active workers of the branch, plus anyone who has an entry that day.
        $workers = Worker::query()
            ->where(function ($query) use ($branchId, $entries) {
                $query->where('is_active', true)
                    ->when($branchId, fn ($query) => $query->where('branch_id', $branchId));
            })
            ->orWhereIn('id', $entries->keys())
            ->orderBy('name')
            ->get();

        // This branch's workers who worked at another branch that day.
        $elsewhere = $branchId
            ? WorkEntry::with('branch')->whereDate('work_date', $date)
                ->whereIn('worker_id', $workers->pluck('id')->diff($entries->keys()))
                ->get()->keyBy('worker_id')
            : collect();

        $summary = [
            'present' => $entries->whereIn('attendance', ['present', 'half_day'])->count(),
            'recorded' => $entries->count(),
            'earnings' => (float) $entries->sum('total_earnings'),
        ];

        $productTotals = $entries->flatMap->items
            ->groupBy('product_id')
            ->map(fn ($items) => [
                'product' => $items->first()->product,
                'produced' => (float) $items->filter(fn ($item) => $item->activity->is_production)->sum('quantity'),
                'dispatched' => (float) $items->filter(fn ($item) => $item->activity->is_dispatch)->sum('quantity'),
            ])
            ->sortBy(fn ($row) => $row['product']->name)
            ->values();

        return view('frontend.labour.work.main.index', compact('date', 'branchId', 'branches', 'workers', 'entries', 'elsewhere', 'summary', 'productTotals'));
    }

    public function create(Request $request)
    {
        $date = $request->date('date')?->toDateString() ?? now()->toDateString();
        $workerId = $request->query('worker');

        if ($workerId && $existing = WorkEntry::where('worker_id', $workerId)->whereDate('work_date', $date)->first()) {
            return redirect()->route('labour.work.edit', $existing);
        }

        return view('frontend.labour.work.add.index', $this->formData() + [
            'date' => $date,
            'selectedWorker' => $workerId,
            // The day sheet the worker was entered from: where they worked that day.
            'selectedBranch' => $request->query('branch_id'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateEntry($request);

        $entry = DB::transaction(function () use ($validated, $request) {
            $entry = WorkEntry::create([
                'worker_id' => $validated['worker_id'],
                'branch_id' => $validated['branch_id'],
                'work_date' => $validated['work_date'],
                'attendance' => $validated['attendance'],
                'notes' => $validated['notes'] ?? null,
                'user_id' => $request->user()->id,
            ]);
            $entry->syncItems($validated['items'] ?? []);

            return $entry;
        });

        $message = __('Work saved for :name — :amount earned.', ['name' => $entry->worker->name, 'amount' => Money::format($entry->total_earnings)]);

        if ($request->input('action') === 'add_another') {
            return redirect()->route('labour.work.create', ['date' => $entry->work_date->toDateString(), 'branch_id' => $entry->branch_id])->with('success', $message);
        }

        return redirect()->route('labour.work', ['date' => $entry->work_date->toDateString(), 'branch_id' => $entry->branch_id])->with('success', $message);
    }

    public function edit(WorkEntry $entry)
    {
        $entry->load(['items', 'worker', 'salaryPayment']);

        return view('frontend.labour.work.update.index', $this->formData($entry) + [
            'entry' => $entry,
            'date' => $entry->work_date->toDateString(),
            'selectedWorker' => $entry->worker_id,
        ]);
    }

    public function update(Request $request, WorkEntry $entry)
    {
        if ($entry->isPaid()) {
            return back()->with('error', __('This work is already paid in :code and cannot be changed.', ['code' => $entry->salaryPayment->payment_code]));
        }

        $validated = $this->validateEntry($request, $entry);

        DB::transaction(function () use ($entry, $validated) {
            $entry->update([
                'worker_id' => $validated['worker_id'],
                'branch_id' => $validated['branch_id'],
                'work_date' => $validated['work_date'],
                'attendance' => $validated['attendance'],
                'notes' => $validated['notes'] ?? null,
            ]);
            $entry->syncItems($validated['items'] ?? []);
        });

        return redirect()->route('labour.work', ['date' => $entry->work_date->toDateString(), 'branch_id' => $entry->branch_id])
            ->with('success', __('Work updated for :name.', ['name' => $entry->worker->name]));
    }

    public function destroy(WorkEntry $entry)
    {
        if ($entry->isPaid()) {
            return back()->with('error', __('This work is already paid in :code and cannot be deleted.', ['code' => $entry->salaryPayment->payment_code]));
        }

        $entry->delete();

        return redirect()->route('labour.work', ['date' => $entry->work_date->toDateString(), 'branch_id' => $entry->branch_id])
            ->with('success', __('Work entry deleted.'));
    }

    /**
     * Active workers, products and activities — plus any inactive ones an existing entry
     * already uses, so editing an old entry keeps its selections.
     */
    protected function formData(?WorkEntry $entry = null): array
    {
        $workers = Worker::where('is_active', true)
            ->when($entry, fn ($query) => $query->orWhere('id', $entry->worker_id))
            ->with('branch')->orderBy('name')->get();
        $branches = Branch::where('status', true)
            ->when($entry, fn ($query) => $query->orWhere('id', $entry->branch_id))
            ->orderBy('name')->get();
        $products = Product::where('is_active', true)
            ->when($entry, fn ($query) => $query->orWhereIn('id', $entry->items->pluck('product_id')))
            ->with('activityRates')->orderBy('name')->get();
        $activities = WorkActivity::where('is_active', true)
            ->when($entry, fn ($query) => $query->orWhereIn('id', $entry->items->pluck('work_activity_id')))
            ->orderBy('name')->get();

        // product id => activity id => rate, so the form can preview earnings as lines are typed.
        $rateMap = $products->mapWithKeys(fn ($product) => [
            $product->id => $activities->mapWithKeys(fn ($activity) => [$activity->id => $product->rateFor($activity)]),
        ]);

        return compact('workers', 'branches', 'products', 'activities', 'rateMap');
    }

    protected function validateEntry(Request $request, ?WorkEntry $entry = null): array
    {
        return $request->validate([
            'worker_id' => [
                'required',
                'exists:workers,id',
                Rule::unique('work_entries', 'worker_id')
                    ->where(fn ($query) => $query->whereDate('work_date', $request->input('work_date')))
                    ->ignore($entry),
            ],
            'branch_id' => ['required', 'exists:branches,id'],
            'work_date' => ['required', 'date', 'before_or_equal:today'],
            'attendance' => ['required', Rule::in(array_keys(WorkEntry::ATTENDANCE))],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['nullable', 'array'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.work_activity_id' => ['required', 'exists:work_activities,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01', 'max:9999999'],
        ], [
            'worker_id.unique' => __('Work for this worker is already recorded on this date. Edit that entry instead.'),
        ], [
            'worker_id' => __('worker'),
            'items.*.product_id' => __('product'),
            'items.*.work_activity_id' => __('work type'),
            'items.*.quantity' => __('quantity'),
        ]);
    }
}
