<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdminOrStaff
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect('/');
        }

        $role = auth()->user()->role->name;

        if (!in_array($role, ['Admin', 'Staff'])) {
            if ($role === 'Barangay Partner') {
                return redirect()->route('barangay.dashboard');
            }
            return redirect('/');
        }

        return $next($request);
    }
}