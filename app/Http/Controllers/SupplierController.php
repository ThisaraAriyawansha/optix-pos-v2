<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::with('branch')->latest()->get();

        return view('frontend.supplier.main.index', compact('suppliers'));
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

        return redirect()->route('suppliers')->with('success', 'Supplier added successfully.');
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

        return redirect()->route('suppliers')->with('success', 'Supplier updated successfully.');
    }

    public function toggleStatus(Supplier $supplier)
    {
        $supplier->update(['is_active' => ! $supplier->is_active]);

        return redirect()->route('suppliers')->with('success', "{$supplier->name} is now ".($supplier->is_active ? 'active' : 'inactive').'.');
    }
}
