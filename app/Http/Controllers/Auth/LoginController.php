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
            'Beneficiary'       => redirect()->route('beneficiary.dashboard'),
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
                'unique_id' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) {
                        // Check old format: BAL-SPUP-2026-069 (17 chars)
                        if (preg_match('/^[A-Z]{3}-[A-Z]{4}-[0-9]{4}-[0-9]{3}$/', $value)) {
                            return;
                        }
                        // Check new format: BE-URAN-Y67W (12 chars)
                        if (preg_match('/^[A-Z]{2}-[A-Z]{4}-[A-Z0-9]{4}$/', $value)) {
                            return;
                        }
                        $fail('Unique ID must be in format: BAL-SPUP-2026-069 or BE-URAN-Y67W');
                    },
                ],
            ], [
                'unique_id.required' => 'Unique ID is required for beneficiaries',
            ]);
            
            // Find beneficiary by unique_id
            $beneficiary = \App\Models\Beneficiary::where('unique_id', $request->unique_id)
                ->first();
            
            if (!$beneficiary || !$beneficiary->user_id) {
                return back()->withErrors([
                    'unique_id' => 'Invalid Unique ID. Please check your ID and try again.',
                ])->onlyInput('unique_id', 'user_role');
            }
            
            // Get the user account
            $user = $beneficiary->user;
                
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