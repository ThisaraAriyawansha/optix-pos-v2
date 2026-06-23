<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('frontend.users.main.index');
    }

    public function manage()
    {
        $users = User::with(['role', 'branch'])->latest()->get();

        return view('frontend.users.manage.index', compact('users'));
    }

    public function create()
    {
        $roles = UserRole::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();

        return view('frontend.users.adduser.index', compact('roles', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone_number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'role_id' => ['required', 'exists:user_roles,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create($validated);

        return redirect()->route('users.manage')->with('success', 'User added successfully.');
    }

    public function edit(User $user)
    {
        $roles = UserRole::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();

        return view('frontend.users.updateuser.index', compact('user', 'roles', 'branches'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone_number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'role_id' => ['required', 'exists:user_roles,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'status' => ['nullable', 'boolean'],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $validated['status'] = $request->boolean('status');

        $user->update($validated);

        return redirect()->route('users.manage')->with('success', 'User updated successfully.');
    }

    public function toggleStatus(User $user)
    {
        $user->update(['status' => ! $user->status]);

        return redirect()->route('users.manage')->with('success', "{$user->name} is now ".($user->status ? 'active' : 'inactive').'.');
    }
}
