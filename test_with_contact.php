<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\HouseholdRequest;
use Illuminate\Support\Facades\DB;

echo "Testing with contact number field...\n";

try {
    // Test with contact number
    echo "Test: Model create with contact number\n";
    
    $data = [
        'barangay_id' => 12,
        'head_of_household' => 'Test User',
        'birthday' => '1990-07-23',
        'address' => 'Test Address',
        'contact_number' => '09123456789',
        'family_size' => 1,
        'requested_by' => 5,
        'status' => 'pending',
    ];
    
    $created = HouseholdRequest::create($data);
    
    echo "✓ Model create successful with contact number\n";
    echo "Created ID: " . $created->id . "\n";
    
    // Clean up
    HouseholdRequest::find($created->id)->delete();
    
} catch (Exception $e) {
    echo "✗ Model create failed: " . $e->getMessage() . "\n";
}

try {
    // Test with null contact number
    echo "\nTest: Model create with null contact number\n";
    
    $data = [
        'barangay_id' => 12,
        'head_of_household' => 'Test User 2',
        'birthday' => '1990-07-23',
        'address' => 'Test Address 2',
        'contact_number' => null,
        'family_size' => 1,
        'requested_by' => 5,
        'status' => 'pending',
    ];
    
    $created = HouseholdRequest::create($data);
    
    echo "✓ Model create successful with null contact number\n";
    echo "Created ID: " . $created->id . "\n";
    
    // Clean up
    HouseholdRequest::find($created->id)->delete();
    
} catch (Exception $e) {
    echo "✗ Model create failed with null contact number: " . $e->getMessage() . "\n";
}
