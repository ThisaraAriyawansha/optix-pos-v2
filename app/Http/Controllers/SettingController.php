<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('frontend.settings.main.index');
    }

    public function profile(Request $request)
    {
        $user = $request->user();

        return view('frontend.settings.profile.index', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone_number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        unset($validated['current_password']);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('settings')->with('success', __('Profile updated successfully.'));
    }
}
