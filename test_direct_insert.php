<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\HouseholdRequest;
use Illuminate\Support\Facades\DB;

echo "Testing direct database insertion...\n";

try {
    // Test 1: Direct SQL insertion
    echo "Test 1: Direct SQL insertion\n";
    $result = DB::insert("
        INSERT INTO household_requests 
        (barangay_id, head_of_household, birthday, address, family_size, requested_by, status, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    ", [12, 'Test User', '1990-07-23', 'Test Address', 1, 5, 'pending']);
    
    echo "✓ Direct SQL insertion successful\n";
    
    // Get the inserted record
    $record = DB::table('household_requests')->where('head_of_household', 'Test User')->first();
    echo "Inserted record ID: " . $record->id . "\n";
    
    // Clean up
    DB::table('household_requests')->where('id', $record->id)->delete();
    
} catch (Exception $e) {
    echo "✗ Direct SQL insertion failed: " . $e->getMessage() . "\n";
}

try {
    // Test 2: Model creation with fill
    echo "\nTest 2: Model creation with fill\n";
    
    $model = new HouseholdRequest();
    $model->barangay_id = 12;
    $model->head_of_household = 'Test User 2';
    $model->birthday = '1990-07-23';
    $model->address = 'Test Address 2';
    $model->family_size = 1;
    $model->requested_by = 5;
    $model->status = 'pending';
    
    $model->save();
    
    echo "✓ Model creation successful\n";
    echo "Model ID: " . $model->id . "\n";
    
    // Clean up
    HouseholdRequest::find($model->id)->delete();
    
} catch (Exception $e) {
    echo "✗ Model creation failed: " . $e->getMessage() . "\n";
}

try {
    // Test 3: Model create method
    echo "\nTest 3: Model create method\n";
    
    $data = [
        'barangay_id' => 12,
        'head_of_household' => 'Test User 3',
        'birthday' => '1990-07-23',
        'address' => 'Test Address 3',
        'family_size' => 1,
        'requested_by' => 5,
        'status' => 'pending',
    ];
    
    $created = HouseholdRequest::create($data);
    
    echo "✓ Model create successful\n";
    echo "Created ID: " . $created->id . "\n";
    
    // Clean up
    HouseholdRequest::find($created->id)->delete();
    
} catch (Exception $e) {
    echo "✗ Model create failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nTesting model fillable fields...\n";
$model = new HouseholdRequest();
echo "Fillable fields: " . implode(', ', $model->getFillable()) . "\n";
echo "Guarded fields: " . implode(', ', $model->getGuarded()) . "\n";
