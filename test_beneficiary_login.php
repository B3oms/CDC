<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Beneficiary;
use Illuminate\Support\Facades\Auth;

// Find a beneficiary with a user account
$beneficiary = Beneficiary::where('unique_id', 'BE-URAN-Y67W')->with('user')->first();

if ($beneficiary && $beneficiary->user) {
    echo "Testing beneficiary authentication:\n";
    echo "Beneficiary: " . $beneficiary->first_name . " " . $beneficiary->last_name . "\n";
    echo "Unique ID: " . $beneficiary->unique_id . "\n";
    echo "User ID: " . $beneficiary->user_id . "\n";
    echo "User Email: " . $beneficiary->user->email . "\n";
    echo "User Status: " . $beneficiary->user->status . "\n";
    echo "User Role: " . $beneficiary->user->role->name . "\n";
    
    // Test authentication
    echo "\nTesting manual authentication...\n";
    Auth::login($beneficiary->user);
    echo "Auth check: " . (Auth::check() ? 'YES' : 'NO') . "\n";
    echo "Auth user role: " . Auth::user()->role->name . "\n";
    
    // Test redirect
    $role = Auth::user()->role->name;
    echo "Redirect route: beneficiary.dashboard\n";
    
    Auth::logout();
    echo "Logged out for testing\n";
} else {
    echo "No valid beneficiary found for testing\n";
}

echo "\nBeneficiary login test completed.\n";
