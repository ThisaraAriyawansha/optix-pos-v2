<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $customers = Customer::with('branch')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('customer_code', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('frontend.customers.main.index', compact('customers', 'search'));
    }

    public function create()
    {
        $branches = Branch::orderBy('name')->get();

        return view('frontend.customers.add.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $validated['customer_code'] = $this->generateCustomerCode();

        Customer::create($validated);

        return redirect()->route('customers')->with('success', __('Customer added successfully.'));
    }

    public function edit(Customer $customer)
    {
        $branches = Branch::orderBy('name')->get();

        return view('frontend.customers.update.index', compact('customer', 'branches'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $customer->update($validated);

        return redirect()->route('customers')->with('success', __('Customer updated successfully.'));
    }

    public function toggleStatus(Customer $customer)
    {
        $customer->update(['is_active' => ! $customer->is_active]);

        return redirect()->route('customers')->with('success', __($customer->is_active ? ':name is now active.' : ':name is now inactive.', ['name' => $customer->name]));
    }

    protected function generateCustomerCode(): string
    {
        $lastId = Customer::max('id') ?? 0;

        return 'CUS-'.str_pad((string) ($lastId + 1), 4, '0', STR_PAD_LEFT);
    }
}
