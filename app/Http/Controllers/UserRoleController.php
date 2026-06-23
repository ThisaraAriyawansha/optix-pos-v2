<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function manage()
    {
        $roles = UserRole::withCount('users')->orderBy('name')->get();

        return view('frontend.users.role.main.index', compact('roles'));
    }

    public function create()
    {
        return view('frontend.users.role.addrole.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:user_roles,name'],
        ]);

        UserRole::create($validated);

        return redirect()->route('roles.manage')->with('success', 'User role added successfully.');
    }
}
