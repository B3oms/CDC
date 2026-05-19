<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulate form data
$formData = [
    'head_name' => 'Test User',
    'head_age' => 35,
    'head_sex' => 'male',
    'head_date_of_birth' => '1990-07-23',
    'address' => 'Test Address',
    'members' => [
        ['name' => 'Member 1', 'age' => 25, 'sex' => 'female'],
        ['name' => 'Member 2', 'age' => 20, 'sex' => 'male']
    ]
];

echo "Testing form validation...\n";

$request = new \Illuminate\Http\Request();
$request->merge($formData);

$controller = new \App\Http\Controllers\Barangay\HouseholdRequestController();

try {
    // Test validation
    $validated = $request->validate([
        'head_name' => 'required|string|max:255',
        'head_age' => 'required|integer|min:1|max:120',
        'head_sex' => 'required|in:male,female',
        'head_date_of_birth' => 'required|date|before:today',
        'address' => 'required|string',
        'members' => 'required|array|min:0',
        'members.*.name' => 'required|string|max:255',
        'members.*.age' => 'required|integer|min:1|max:120',
        'members.*.sex' => 'required|in:male,female',
    ]);

    echo "✓ Validation passed\n";
    echo "Validated data:\n";
    print_r($validated);

    // Test model creation
    echo "\nTesting model creation...\n";
    
    $createData = [
        'barangay_id' => 12, // Assuming this exists
        'head_of_household' => $validated['head_name'],
        'birthday' => $validated['head_date_of_birth'],
        'address' => $validated['address'],
        'family_size' => 1 + count($validated['members']),
        'requested_by' => 5, // Assuming this user exists
    ];

    echo "Data to be created:\n";
    print_r($createData);

    // Check if model fillable includes these fields
    $model = new \App\Models\HouseholdRequest();
    echo "\nModel fillable fields:\n";
    print_r($model->getFillable());

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
