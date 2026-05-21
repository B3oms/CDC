<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Beneficiary;
use App\Models\Barangay;

echo "Fixing Beneficiary Links:\n\n";

// Get a valid barangay ID first
$firstBarangay = Barangay::first();
if (!$firstBarangay) {
    echo "No barangays found in database!\n";
    exit(1);
}

$defaultBarangayId = $firstBarangay->id;
echo "Using default barangay ID: " . $defaultBarangayId . " (" . $firstBarangay->name . ")\n\n";

// Find users with beneficiary role but no beneficiary record
$beneficiaryUsers = User::whereHas('role', function($query) {
    $query->where('name', 'Beneficiary');
})->get();

foreach ($beneficiaryUsers as $user) {
    $beneficiary = Beneficiary::where('user_id', $user->id)->first();
    
    if (!$beneficiary) {
        echo "Creating missing beneficiary record for user: " . $user->email . "\n";
        
        // Create a new beneficiary record
        $newBeneficiary = new Beneficiary();
        $newBeneficiary->user_id = $user->id;
        $newBeneficiary->first_name = $user->first_name;
        $newBeneficiary->last_name = $user->last_name;
        $newBeneficiary->contact_number = $user->contact_number ?? '09999999999';
        $newBeneficiary->address = $user->address ?? 'Default Address';
        $newBeneficiary->barangay_id = $user->barangay_id ?? $defaultBarangayId;
        
        // Generate a unique ID
        $newBeneficiary->unique_id = Beneficiary::generateUniqueId();
        
        // Set default values
        $newBeneficiary->family_size = 1;
        $newBeneficiary->monthly_income = 0;
        $newBeneficiary->vulnerability_level = 'Low';
        $newBeneficiary->has_senior = 0;
        $newBeneficiary->children_count = 0;
        $newBeneficiary->criteria_met = 0;
        $newBeneficiary->is_verified = 0;
        $newBeneficiary->is_indigenous = 0;
        $newBeneficiary->is_pwd = 0;
        
        $newBeneficiary->save();
        
        echo "  Created beneficiary: " . $newBeneficiary->first_name . " " . $newBeneficiary->last_name . "\n";
        echo "  Unique ID: " . $newBeneficiary->unique_id . "\n";
        echo "  Beneficiary ID: " . $newBeneficiary->id . "\n\n";
    } else {
        echo "User " . $user->email . " already has beneficiary record\n";
    }
}

echo "Beneficiary links fixed!\n";
