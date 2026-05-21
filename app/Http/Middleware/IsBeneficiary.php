<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsBeneficiary
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role->name === 'Beneficiary') {
            return $next($request);
        }

        // If not a beneficiary, redirect to login with error
        return redirect()->route('login')
            ->with('error', 'Access denied. Beneficiary access required.');
    }
}
