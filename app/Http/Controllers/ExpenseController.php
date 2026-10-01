<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $branchId = $request->query('branch_id');
        $branches = Branch::orderBy('name')->get();

        $now = now();
        $monthRange = [$now->copy()->startOfMonth()->toDateString(), $now->copy()->endOfMonth()->toDateString()];
        $lastMonthRange = [$now->copy()->subMonthNoOverflow()->startOfMonth()->toDateString(), $now->copy()->subMonthNoOverflow()->endOfMonth()->toDateString()];
        $weekRange = [$now->copy()->startOfWeek()->toDateString(), $now->copy()->endOfWeek()->toDateString()];

        $base = fn () => Expense::query()->when($branchId, fn ($query) => $query->where('branch_id', $branchId));

        $stats = [
            'today' => (float) $base()->whereDate('expense_date', $now->toDateString())->sum('amount'),
            'week' => (float) $base()->whereBetween('expense_date', $weekRange)->sum('amount'),
            'month' => (float) $base()->whereBetween('expense_date', $monthRange)->sum('amount'),
            'last_month' => (float) $base()->whereBetween('expense_date', $lastMonthRange)->sum('amount'),
            'month_count' => $base()->whereBetween('expense_date', $monthRange)->count(),
        ];

        $stats['change'] = $stats['last_month'] > 0
            ? round((($stats['month'] - $stats['last_month']) / $stats['last_month']) * 100, 1)
            : null;

        $monthScope = fn ($query) => $query->whereBetween('expense_date', $monthRange)
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId));

        $categories = ExpenseCategory::query()
            ->withSum(['expenses as month_total' => $monthScope], 'amount')
            ->withCount(['expenses as month_count' => $monthScope])
            ->get()
            ->filter(fn ($category) => $category->is_active || $category->month_total > 0)
            ->sortByDesc(fn ($category) => (float) $category->month_total)
            ->values();

        $recentExpenses = $base()
            ->with(['category', 'branch'])
            ->latest('expense_date')
            ->latest('id')
            ->take(6)
            ->get();

        return view('frontend.expenses.main.index', compact('stats', 'categories', 'recentExpenses', 'branches', 'branchId', 'monthRange'));
    }

    public function list(Request $request)
    {
        $filters = $request->only(['search', 'category', 'branch_id', 'payment_method', 'from', 'to']);

        $query = Expense::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('expense_code', 'like', "%{$search}%")
                        ->orWhere('paid_to', 'like', "%{$search}%")
                        ->orWhere('reference_no', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%");
                });
            })
            ->when($filters['category'] ?? null, fn ($query, $category) => $query->where('expense_category_id', $category))
            ->when($filters['branch_id'] ?? null, fn ($query, $branchId) => $query->where('branch_id', $branchId))
            ->when($filters['payment_method'] ?? null, fn ($query, $method) => $query->where('payment_method', $method))
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('expense_date', '>=', $from))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('expense_date', '<=', $to));

        $total = (float) (clone $query)->sum('amount');

        $expenses = $query->with(['category', 'branch', 'user'])
            ->latest('expense_date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $categories = ExpenseCategory::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();
        $hasFilters = collect($filters)->filter(fn ($value) => filled($value))->isNotEmpty();

        return view('frontend.expenses.list.index', compact('expenses', 'categories', 'branches', 'filters', 'total', 'hasFilters'));
    }

    public function create(Request $request)
    {
        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();
        $selectedCategory = $request->query('category');

        return view('frontend.expenses.add.index', compact('categories', 'branches', 'selectedCategory'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateExpense($request);

        $validated['title'] = $validated['title'] ?: ExpenseCategory::find($validated['expense_category_id'])->name;
        $validated['expense_code'] = $this->generateExpenseCode();
        $validated['user_id'] = $request->user()?->id;

        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] = $request->file('receipt')->store('expense-receipts', 'public');
        }

        unset($validated['receipt']);

        $expense = Expense::create($validated);

        $message = __('Expense :code saved successfully.', ['code' => $expense->expense_code]);

        if ($request->input('action') === 'add_another') {
            return redirect()->route('expenses.create')->with('success', $message);
        }

        return redirect()->route('expenses.list')->with('success', $message);
    }

    public function edit(Expense $expense)
    {
        $categories = ExpenseCategory::where('is_active', true)
            ->orWhere('id', $expense->expense_category_id)
            ->orderBy('name')
            ->get();
        $branches = Branch::orderBy('name')->get();

        return view('frontend.expenses.update.index', compact('expense', 'categories', 'branches'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $this->validateExpense($request, $expense);

        $validated['title'] = $validated['title'] ?: ExpenseCategory::find($validated['expense_category_id'])->name;

        if ($request->hasFile('receipt') || $request->boolean('remove_receipt')) {
            if ($expense->receipt_path) {
                Storage::disk('public')->delete($expense->receipt_path);
            }
            $validated['receipt_path'] = $request->hasFile('receipt')
                ? $request->file('receipt')->store('expense-receipts', 'public')
                : null;
        }

        unset($validated['receipt'], $validated['remove_receipt']);

        $expense->update($validated);

        return redirect()->route('expenses.list')->with('success', __('Expense :code updated successfully.', ['code' => $expense->expense_code]));
    }

    public function destroy(Expense $expense)
    {
        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }

        $expense->delete();

        return redirect()->route('expenses.list')->with('success', __('Expense :code deleted.', ['code' => $expense->expense_code]));
    }

    protected function validateExpense(Request $request, ?Expense $expense = null): array
    {
        return $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'expense_category_id' => [
                'required',
                Rule::exists('expense_categories', 'id')->where(function ($query) use ($expense) {
                    $query->where('is_active', true);
                    if ($expense) {
                        $query->orWhere('id', $expense->expense_category_id);
                    }
                }),
            ],
            'title' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'expense_date' => ['required', 'date', 'before_or_equal:today'],
            'payment_method' => ['required', Rule::in(array_keys(Expense::PAYMENT_METHODS))],
            'paid_to' => ['nullable', 'string', 'max:255'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
            'remove_receipt' => ['nullable', 'boolean'],
        ], [], [
            'expense_category_id' => __('expense type'),
        ]);
    }

    protected function generateExpenseCode(): string
    {
        $lastId = Expense::max('id') ?? 0;

        return 'EXP-'.str_pad((string) ($lastId + 1), 5, '0', STR_PAD_LEFT);
    }
}
