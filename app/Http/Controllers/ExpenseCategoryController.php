<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $categories = ExpenseCategory::withCount('expenses')
            ->withSum('expenses', 'amount')
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return view('frontend.expenses.categories.main.index', compact('categories'));
    }

    public function store(Request $request)
    {
        ExpenseCategory::create($this->validateCategory($request));

        return redirect()->route('expenses.categories')->with('success', __('Expense type added successfully.'));
    }

    public function edit(ExpenseCategory $category)
    {
        return view('frontend.expenses.categories.update.index', compact('category'));
    }

    public function update(Request $request, ExpenseCategory $category)
    {
        $validated = $this->validateCategory($request, $category);
        $validated['is_active'] = $request->boolean('is_active');

        $category->update($validated);

        return redirect()->route('expenses.categories')->with('success', __('Expense type updated successfully.'));
    }

    public function toggleStatus(ExpenseCategory $category)
    {
        $category->update(['is_active' => ! $category->is_active]);

        return redirect()->route('expenses.categories')->with('success', __($category->is_active ? ':name is now active.' : ':name is now inactive.', ['name' => $category->label()]));
    }

    public function destroy(ExpenseCategory $category)
    {
        if ($category->expenses()->exists()) {
            return redirect()->route('expenses.categories')->with('error', __(':name has recorded expenses. Deactivate it instead.', ['name' => $category->label()]));
        }

        $category->delete();

        return redirect()->route('expenses.categories')->with('success', __('Expense type deleted.'));
    }

    protected function validateCategory(Request $request, ?ExpenseCategory $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('expense_categories', 'name')->ignore($category)],
            'description' => ['nullable', 'string', 'max:255'],
            'color' => ['required', Rule::in(array_keys(ExpenseCategory::COLORS))],
            'icon' => ['required', Rule::in(array_keys(ExpenseCategory::ICONS))],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
