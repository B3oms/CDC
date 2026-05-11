<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
{
    if (Auth::check()) {
        $role = Auth::user()->role->name;
        return match($role) {
            'Admin'            => redirect()->route('admin.dashboard'),
            'Staff'            => redirect()->route('staff.dashboard'),
            'Barangay Partner' => redirect()->route('barangay.dashboard'),
            default            => redirect('/'),
        };
    }
    return view('auth.login');
}

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $role = Auth::user()->role->name;

            return match($role) {
    'Admin'            => redirect()->route('admin.dashboard'),
    'Staff'            => redirect()->route('staff.dashboard'),
    'Barangay Partner' => redirect()->route('barangay.dashboard'),
    default            => redirect('/'),
};
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}