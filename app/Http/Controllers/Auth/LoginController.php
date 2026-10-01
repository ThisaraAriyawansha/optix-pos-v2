<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login()
    {
        return view('frontend.login.index');
    }

    public function postLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => __('These credentials do not match our records.'),
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $locale = $request->session()->get('locale');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($locale) {
            $request->session()->put('locale', $locale);
        }

        return redirect()->route('login');
    }
}
