<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Beneficiary;

// Test the beneficiary relationship
$user = User::whereHas('role', function($query) {
    $query->where('name', 'Beneficiary');
})->first();

if ($user) {
    echo "Testing User-Beneficiary Relationship:\n";
    echo "User ID: " . $user->id . "\n";
    echo "User Email: " . $user->email . "\n";
    echo "User Role: " . $user->role->name . "\n";
    
    $beneficiary = $user->beneficiary;
    echo "Has Beneficiary: " . ($beneficiary ? 'YES' : 'NO') . "\n";
    
    if ($beneficiary) {
        echo "Beneficiary ID: " . $beneficiary->id . "\n";
        echo "Beneficiary Name: " . $beneficiary->first_name . " " . $beneficiary->last_name . "\n";
        echo "Unique ID: " . $beneficiary->unique_id . "\n";
        echo "Relationship working: YES\n";
    } else {
        echo "Relationship working: NO - No beneficiary found\n";
        
        // Check if beneficiary exists separately
        $separateBeneficiary = Beneficiary::where('user_id', $user->id)->first();
        echo "Separate beneficiary check: " . ($separateBeneficiary ? 'FOUND' : 'NOT FOUND') . "\n";
    }
} else {
    echo "No beneficiary user found for testing\n";
}

echo "\nBeneficiary relationship test completed.\n";
