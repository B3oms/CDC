<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Checking household_requests table structure...\n";

if (Schema::hasTable('household_requests')) {
    $columns = Schema::getColumnListing('household_requests');
    echo "Columns in household_requests table:\n";
    print_r($columns);
    
    // Check if head_of_household column exists
    if (in_array('head_of_household', $columns)) {
        echo "✓ head_of_household column exists\n";
    } else {
        echo "✗ head_of_household column MISSING\n";
    }
    
    // Check if birthday column exists
    if (in_array('birthday', $columns)) {
        echo "✓ birthday column exists\n";
    } else {
        echo "✗ birthday column MISSING\n";
    }
    
    // Check table constraints
    echo "\nChecking table constraints...\n";
    try {
        $desc = DB::select("DESCRIBE household_requests");
        foreach ($desc as $column) {
            echo $column->Field . " - " . $column->Type . " - " . ($column->Null == 'NO' ? 'NOT NULL' : 'NULL') . " - " . ($column->Default ? "DEFAULT " . $column->Default : 'NO DEFAULT') . "\n";
        }
    } catch (Exception $e) {
        echo "Error getting table description: " . $e->getMessage() . "\n";
    }
} else {
    echo "household_requests table does not exist!\n";
}

echo "\nChecking household_members table structure...\n";

if (Schema::hasTable('household_members')) {
    $columns = Schema::getColumnListing('household_members');
    echo "Columns in household_members table:\n";
    print_r($columns);
} else {
    echo "household_members table does not exist!\n";
}
