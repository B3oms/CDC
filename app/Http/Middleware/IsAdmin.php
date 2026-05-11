<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect('/');
        }

        $role = auth()->user()->role->name;

        if ($role === 'Staff') {
            return redirect()->route('staff.dashboard');
        }

        if ($role === 'Barangay Partner') {
            return redirect()->route('barangay.dashboard');
        }

        if ($role !== 'Admin') {
            return redirect('/');
        }

        return $next($request);
    }
}