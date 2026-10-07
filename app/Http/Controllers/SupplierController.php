<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $suppliers = Supplier::with('branch')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('nic', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('frontend.supplier.main.index', compact('suppliers', 'search'));
    }

    public function create()
    {
        $branches = Branch::orderBy('name')->get();

        return view('frontend.supplier.add.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'nic' => ['required', 'string', 'max:20', 'unique:suppliers,nic'],
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers')->with('success', __('Supplier added successfully.'));
    }

    public function edit(Supplier $supplier)
    {
        $branches = Branch::orderBy('name')->get();

        return view('frontend.supplier.update.index', compact('supplier', 'branches'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'nic' => ['required', 'string', 'max:20', 'unique:suppliers,nic,'.$supplier->id],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $supplier->update($validated);

        return redirect()->route('suppliers')->with('success', __('Supplier updated successfully.'));
    }

    public function toggleStatus(Supplier $supplier)
    {
        $supplier->update(['is_active' => ! $supplier->is_active]);

        return redirect()->route('suppliers')->with('success', __($supplier->is_active ? ':name is now active.' : ':name is now inactive.', ['name' => $supplier->name]));
    }
}
