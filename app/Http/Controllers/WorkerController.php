<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Worker;
use App\Support\EmployeeCode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WorkerController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'branch_id', 'pay_type']);

        $workers = Worker::query()
            ->with('branch')
            ->withSum(['workEntries as unpaid_total' => fn ($query) => $query->whereNull('salary_payment_id')], 'total_earnings')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('nic', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($filters['branch_id'] ?? null, fn ($query, $branchId) => $query->where('branch_id', $branchId))
            ->when($filters['pay_type'] ?? null, fn ($query, $payType) => $query->where('pay_type', $payType))
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(24)
            ->withQueryString();

        $branches = Branch::orderBy('name')->get();

        return view('frontend.labour.workers.main.index', compact('workers', 'branches', 'filters'));
    }

    public function create()
    {
        $branches = Branch::where('status', true)->orderBy('name')->get();

        $nextCode = EmployeeCode::next();

        return view('frontend.labour.workers.add.index', compact('branches', 'nextCode'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateWorker($request);

        $worker = Worker::create($validated);

        return redirect()->route('labour.workers')->with('success', __('Worker :name added successfully.', ['name' => $worker->name]));
    }

    public function edit(Worker $worker)
    {
        $branches = Branch::where('status', true)->orWhere('id', $worker->branch_id)->orderBy('name')->get();

        return view('frontend.labour.workers.update.index', compact('worker', 'branches'));
    }

    public function update(Request $request, Worker $worker)
    {
        $validated = $this->validateWorker($request, $worker);
        $validated['is_active'] = $request->boolean('is_active');

        $worker->update($validated);

        return redirect()->route('labour.workers')->with('success', __('Worker :name updated successfully.', ['name' => $worker->name]));
    }

    public function toggleStatus(Worker $worker)
    {
        $worker->update(['is_active' => ! $worker->is_active]);

        return redirect()->route('labour.workers')->with('success', __($worker->is_active ? ':name is now active.' : ':name is now inactive.', ['name' => $worker->name]));
    }

    protected function validateWorker(Request $request, ?Worker $worker = null): array
    {
        $validated = $request->validate([
            'code' => EmployeeCode::rules($worker),
            'name' => ['required', 'string', 'max:255'],
            'nic' => ['nullable', 'string', 'max:20', Rule::unique('workers', 'nic')->ignore($worker)],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'branch_id' => ['required', 'exists:branches,id'],
            'pay_type' => ['required', Rule::in(array_keys(Worker::PAY_TYPES))],
            'daily_rate' => ['nullable', 'required_if:pay_type,daily', 'numeric', 'min:0', 'max:9999999'],
            'monthly_salary' => ['nullable', 'required_if:pay_type,monthly', 'numeric', 'min:0', 'max:9999999999'],
            'joined_on' => ['nullable', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Blank on create → the model assigns the next ID; blank on edit → keep the current one.
        $validated['code'] = EmployeeCode::normalize($validated['code'] ?? null) ?? $worker?->code;

        return $validated;
    }
}
