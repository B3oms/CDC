<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Beneficiary;
use App\Models\User;

// Check beneficiaries with and without user accounts
$allBeneficiaries = Beneficiary::all();
echo "Beneficiary Analysis:\n";
echo "Total beneficiaries: " . $allBeneficiaries->count() . "\n\n";

$withUser = 0;
$withoutUser = 0;

foreach ($allBeneficiaries as $beneficiary) {
    if ($beneficiary->user_id && $beneficiary->user) {
        $withUser++;
    } else {
        $withoutUser++;
        echo "Beneficiary without user: " . $beneficiary->first_name . " " . $beneficiary->last_name . "\n";
        echo "  Unique ID: " . $beneficiary->unique_id . "\n";
        echo "  User ID: " . ($beneficiary->user_id ?? 'NULL') . "\n";
        echo "\n";
    }
}

echo "Summary:\n";
echo "With user accounts: " . $withUser . "\n";
echo "Without user accounts: " . $withoutUser . "\n";

// Check if there are users with beneficiary role but no beneficiary record
echo "\nUsers with Beneficiary role:\n";
$beneficiaryUsers = User::whereHas('role', function($query) {
    $query->where('name', 'Beneficiary');
})->get();

foreach ($beneficiaryUsers as $user) {
    $beneficiary = Beneficiary::where('user_id', $user->id)->first();
    echo "User: " . $user->email . " -> Beneficiary: " . ($beneficiary ? 'EXISTS' : 'MISSING') . "\n";
}

echo "\nBeneficiary link analysis completed.\n";
