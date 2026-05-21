<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

// Activate all beneficiary user accounts
$beneficiaryUsers = User::whereHas('role', function($query) {
    $query->where('name', 'Beneficiary');
})->get();

echo "Activating beneficiary user accounts...\n";
$activatedCount = 0;

foreach ($beneficiaryUsers as $user) {
    if (!$user->is_active) {
        $user->is_active = true;
        $user->save();
        echo "  Activated: " . $user->email . "\n";
        $activatedCount++;
    }
}

echo "\nActivated $activatedCount beneficiary accounts.\n";

// Verify activation
echo "\nVerification:\n";
$testUser = $beneficiaryUsers->first();
echo "Test user: " . $testUser->email . "\n";
echo "Active status: " . ($testUser->is_active ? 'YES' : 'NO') . "\n";
echo "Role: " . $testUser->role->name . "\n";
if ($testUser->beneficiary) {
    echo "Unique ID: " . $testUser->beneficiary->unique_id . "\n";
}

echo "\nBeneficiary login should now work properly!\n";
