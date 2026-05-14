<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsStaff
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect('/');
        }

        try {
            $role = auth()->user()->role->name;

            if ($role === 'Admin') {
                return redirect()->route('admin.dashboard');
            }

            if ($role === 'Barangay Partner') {
                return redirect()->route('barangay.dashboard');
            }

            if ($role !== 'Staff') {
                return redirect('/');
            }

            return $next($request);
        } catch (\Exception $e) {
            return redirect('/');
        }
    }
}