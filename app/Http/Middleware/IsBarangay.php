<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsBarangay
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect('/');
        }

        $role = auth()->user()->role->name;

        if ($role === 'Admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($role === 'Staff') {
            return redirect()->route('staff.dashboard');
        }

        if ($role !== 'Barangay Partner') {
            return redirect('/');
        }

        return $next($request);
    }
}