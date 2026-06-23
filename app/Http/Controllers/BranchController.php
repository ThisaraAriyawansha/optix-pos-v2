<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::latest()->get();

        return view('frontend.branch.main.index', compact('branches'));
    }

    public function create()
    {
        return view('frontend.branch.addbranch.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address' => ['required', 'string', 'max:255'],
            'main_contact' => ['required', 'string', 'max:20'],
            'secondary_contact' => ['nullable', 'string', 'max:20'],
        ]);

        Branch::create($validated);

        return redirect()->route('branches')->with('success', 'Branch added successfully.');
    }

    public function edit(Branch $branch)
    {
        return view('frontend.branch.updatebranch.index', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address' => ['required', 'string', 'max:255'],
            'main_contact' => ['required', 'string', 'max:20'],
            'secondary_contact' => ['nullable', 'string', 'max:20'],
            'status' => ['nullable', 'boolean'],
        ]);

        $validated['status'] = $request->boolean('status');

        $branch->update($validated);

        return redirect()->route('branches')->with('success', 'Branch updated successfully.');
    }

    public function toggleStatus(Branch $branch)
    {
        $branch->update(['status' => ! $branch->status]);

        return redirect()->route('branches')->with('success', "{$branch->name} is now ".($branch->status ? 'active' : 'inactive').'.');
    }
}
