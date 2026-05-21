<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Deployment Issues Check:\n";
echo "======================\n\n";

// Check for missing classes or imports
echo "Checking Class Imports:\n";
try {
    // Test beneficiary relationship
    $user = new \App\Models\User();
    echo "✓ User model loads\n";
    
    $beneficiary = new \App\Models\Beneficiary();
    echo "✓ Beneficiary model loads\n";
    
    $distribution = new \App\Models\Distribution();
    echo "✓ Distribution model loads\n";
    
} catch (\Exception $e) {
    echo "✗ Model loading failed: " . $e->getMessage() . "\n";
}

// Check for syntax errors in views
echo "\nChecking View Files:\n";
$viewFiles = [
    'beneficiary.layouts.app',
    'beneficiary.dashboard',
    'beneficiary.profile',
    'beneficiary.relief-history'
];

foreach ($viewFiles as $view) {
    try {
        $viewContent = view($view);
        echo "✓ View '$view' compiles\n";
    } catch (\Exception $e) {
        echo "✗ View '$view' ERROR: " . $e->getMessage() . "\n";
    }
}

// Check for missing database columns
echo "\nChecking Database Schema:\n";
try {
    // Check if beneficiaries table has unique_id column
    $columns = \Schema::getColumnListing('beneficiaries');
    if (in_array('unique_id', $columns)) {
        echo "✓ beneficiaries.unique_id column exists\n";
    } else {
        echo "✗ beneficiaries.unique_id column MISSING\n";
    }
    
    // Check if distributions table has date_distributed column
    $columns = \Schema::getColumnListing('distributions');
    if (in_array('date_distributed', $columns)) {
        echo "✓ distributions.date_distributed column exists\n";
    } else {
        echo "✗ distributions.date_distributed column MISSING\n";
    }
    
    // Check if users table has status column
    $columns = \Schema::getColumnListing('users');
    if (in_array('status', $columns)) {
        echo "✓ users.status column exists\n";
    } else {
        echo "✗ users.status column MISSING\n";
    }
    
} catch (\Exception $e) {
    echo "✗ Schema check failed: " . $e->getMessage() . "\n";
}

// Check for missing migrations
echo "\nChecking Migrations:\n";
try {
    $ranMigrations = \DB::table('migrations')->pluck('migration')->toArray();
    
    $requiredMigrations = [
        '2026_05_21_171222_add_unique_id_to_beneficiaries_table',
        '2026_05_21_173242_update_unique_id_field_size_in_beneficiaries_table'
    ];
    
    foreach ($requiredMigrations as $migration) {
        if (in_array($migration, $ranMigrations)) {
            echo "✓ Migration '$migration' has run\n";
        } else {
            echo "✗ Migration '$migration' MISSING\n";
        }
    }
    
} catch (\Exception $e) {
    echo "✗ Migration check failed: " . $e->getMessage() . "\n";
}

// Check file permissions
echo "\nChecking File Permissions:\n";
$paths = [
    'storage/logs',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views'
];

foreach ($paths as $path) {
    if (is_dir($path)) {
        $writable = is_writable($path);
        echo "✓ Directory '$path' exists" . ($writable ? " (writable)" : " (NOT writable)") . "\n";
    } else {
        echo "✗ Directory '$path' MISSING\n";
    }
}

echo "\nDeployment check completed.\n";
