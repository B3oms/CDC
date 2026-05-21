<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;

echo "Testing Beneficiary Login Form Submission\n";
echo "========================================\n\n";

// Simulate form submission data
$requestData = [
    'user_role' => 'beneficiary',
    'unique_id' => 'BE-URAN-Y67W',
    '_token' => csrf_token()
];

echo "Form Data:\n";
echo "- User Role: " . $requestData['user_role'] . "\n";
echo "- Unique ID: " . $requestData['unique_id'] . "\n\n";

// Create request
$request = Request::create('/login', 'POST', $requestData);
$request->headers->set('Content-Type', 'application/x-www-form-urlencoded');

// Test validation
echo "Testing Validation:\n";
$rules = [
    'user_role' => 'required|in:beneficiary',
    'unique_id' => 'required|string|size:12|regex:/^[A-Z]{2}-[A-Z]{4}-[A-Z0-9]{4}$/'
];

$validator = validator()->make($requestData, $rules);
if ($validator->fails()) {
    echo "Validation failed:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "- " . $error . "\n";
    }
} else {
    echo "✓ Validation passed\n";
}

// Test beneficiary lookup
echo "\nTesting Beneficiary Lookup:\n";
$beneficiary = \App\Models\Beneficiary::where('unique_id', $requestData['unique_id'])->first();
if ($beneficiary) {
    echo "✓ Beneficiary found: " . $beneficiary->first_name . " " . $beneficiary->last_name . "\n";
    echo "  User ID: " . ($beneficiary->user_id ?? 'NULL') . "\n";
    
    if ($beneficiary->user_id) {
        $user = $beneficiary->user;
        if ($user) {
            echo "✓ User account found\n";
            echo "  Email: " . $user->email . "\n";
            echo "  Status: " . $user->status . "\n";
            echo "  Role: " . $user->role->name . "\n";
        } else {
            echo "✗ User account not found\n";
        }
    } else {
        echo "✗ No user_id associated with beneficiary\n";
    }
} else {
    echo "✗ Beneficiary not found\n";
}

// Test route existence
echo "\nTesting Routes:\n";
echo "- beneficiary.dashboard route exists: " . (\Route::has('beneficiary.dashboard') ? 'YES' : 'NO') . "\n";
echo "- login.post route exists: " . (\Route::has('login.post') ? 'YES' : 'NO') . "\n";

echo "\nLogin submission test completed.\n";
