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
        $userRole = $request->input('user_role');
        
        // Validate based on role
        if ($userRole === 'beneficiary') {
            $credentials = $request->validate([
                'user_role' => 'required|in:beneficiary',
                'unique_id' => 'required|string|between:8,12|regex:/^[A-Z0-9]+$/',
            ], [
                'unique_id.required' => 'Unique ID is required for beneficiaries',
                'unique_id.between' => 'Unique ID must be between 8 and 12 characters',
                'unique_id.regex' => 'Unique ID must contain only uppercase letters and numbers',
            ]);
            
            // Find beneficiary by unique_id
            $user = \App\Models\User::where('unique_id', $request->unique_id)
                ->whereHas('role', function($query) {
                    $query->where('name', 'Beneficiary');
                })
                ->first();
                
            if (!$user) {
                return back()->withErrors([
                    'unique_id' => 'Invalid Unique ID. Please check your ID and try again.',
                ])->onlyInput('unique_id', 'user_role');
            }
            
            // Log in the beneficiary without password
            Auth::login($user);
            
        } else {
            // Admin, Staff, or Barangay login with email/password
            $credentials = $request->validate([
                'user_role' => 'required|in:admin,staff,barangay',
                'email'    => 'required|email',
                'password' => 'required',
            ]);
            
            // Attempt authentication with email and password
            if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return back()->withErrors([
                    'email' => 'These credentials do not match our records.',
                ])->onlyInput('email', 'user_role');
            }
            
            // Verify the user role matches the selected role
            $user = Auth::user();
            $actualRole = $user->role->name;
            
            // Map role names
            $roleMap = [
                'admin' => 'Admin',
                'staff' => 'Staff', 
                'barangay' => 'Barangay Partner'
            ];
            
            if ($actualRole !== $roleMap[$userRole]) {
                Auth::logout();
                return back()->withErrors([
                    'user_role' => 'The selected role does not match your account role.',
                ])->onlyInput('email', 'user_role');
            }
        }
        
        $request->session()->regenerate();
        
        $role = Auth::user()->role->name;
        
        return match($role) {
            'Admin'            => redirect()->route('admin.dashboard'),
            'Staff'            => redirect()->route('staff.dashboard'),
            'Barangay Partner' => redirect()->route('barangay.dashboard'),
            'Beneficiary'       => redirect()->route('beneficiary.dashboard'),
            default            => redirect('/'),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}