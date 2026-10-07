<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use App\Models\UserRole;
use App\Support\EmployeeCode;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('frontend.users.main.index');
    }

    public function manage(Request $request)
    {
        $search = $request->query('search');

        $users = User::with(['role', 'branch'])
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('employee_code', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('frontend.users.manage.index', compact('users', 'search'));
    }

    public function create()
    {
        $roles = UserRole::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();

        $nextCode = EmployeeCode::next();

        return view('frontend.users.adduser.index', compact('roles', 'branches', 'nextCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_code' => EmployeeCode::rules(),
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone_number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'role_id' => ['required', 'exists:user_roles,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $validated['employee_code'] = EmployeeCode::normalize($validated['employee_code'] ?? null);

        User::create($validated);

        return redirect()->route('users.manage')->with('success', __('User added successfully.'));
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
            'employee_code' => EmployeeCode::rules($user),
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone_number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'role_id' => ['required', 'exists:user_roles,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'status' => ['nullable', 'boolean'],
        ]);

        $validated['employee_code'] = EmployeeCode::normalize($validated['employee_code'] ?? null) ?? $user->employee_code;

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $validated['status'] = $request->boolean('status');

        $user->update($validated);

        return redirect()->route('users.manage')->with('success', __('User updated successfully.'));
    }

    public function toggleStatus(User $user)
    {
        $user->update(['status' => ! $user->status]);

        return redirect()->route('users.manage')->with('success', __($user->status ? ':name is now active.' : ':name is now inactive.', ['name' => $user->name]));
    }
}
