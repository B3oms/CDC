<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Deployment Debugging Report:\n";
echo "==========================\n\n";

// Check Laravel environment
echo "Environment Status:\n";
echo "- Environment: " . app()->environment() . "\n";
echo "- Debug Mode: " . (config('app.debug') ? 'ON' : 'OFF') . "\n";
echo "- App URL: " . config('app.url') . "\n";
echo "- App Key: " . (config('app.key') ? 'SET' : 'MISSING') . "\n\n";

// Check database connection
echo "Database Status:\n";
try {
    \DB::connection()->getPdo();
    echo "- Connection: SUCCESS\n";
    echo "- Database: " . \DB::connection()->getDatabaseName() . "\n";
    echo "- Tables Count: " . \DB::select('SHOW TABLES')[0]->{'Tables_in_' . \DB::connection()->getDatabaseName()} . "\n";
} catch (\Exception $e) {
    echo "- Connection: FAILED - " . $e->getMessage() . "\n";
}

// Check critical tables
echo "\nCritical Tables Status:\n";
$criticalTables = ['users', 'beneficiaries', 'migrations'];
foreach ($criticalTables as $table) {
    try {
        $exists = \Schema::hasTable($table);
        $count = $exists ? \DB::table($table)->count() : 0;
        echo "- $table: " . ($exists ? 'EXISTS' : 'MISSING') . " ($count records)\n";
    } catch (\Exception $e) {
        echo "- $table: ERROR - " . $e->getMessage() . "\n";
    }
}

// Check migrations
echo "\nMigration Status:\n";
try {
    $ranMigrations = \DB::table('migrations')->pluck('migration')->toArray();
    $totalMigrations = count(glob(__DIR__ . '/database/migrations/*.php'));
    echo "- Total Migrations: $totalMigrations\n";
    echo "- Ran Migrations: " . count($ranMigrations) . "\n";
    
    // Check for critical migrations
    $criticalMigrations = [
        '2026_05_21_171222_add_unique_id_to_beneficiaries_table',
        '2026_05_21_173242_update_unique_id_field_size_in_beneficiaries_table'
    ];
    
    foreach ($criticalMigrations as $migration) {
        $ran = in_array($migration, $ranMigrations);
        echo "- $migration: " . ($ran ? 'RAN' : 'PENDING') . "\n";
    }
} catch (\Exception $e) {
    echo "- Migration Check Failed: " . $e->getMessage() . "\n";
}

// Check cache and config
echo "\nCache Status:\n";
try {
    echo "- Config Cached: " . (app()->configurationIsCached() ? 'YES' : 'NO') . "\n";
    echo "- Routes Cached: " . (app()->routesAreCached() ? 'YES' : 'NO') . "\n";
    echo "- Events Cached: " . (app()->eventsAreCached() ? 'YES' : 'NO') . "\n";
} catch (\Exception $e) {
    echo "- Cache Check Failed: " . $e->getMessage() . "\n";
}

// Check file permissions
echo "\nFile Permissions:\n";
$paths = [
    'storage/logs',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'bootstrap/cache'
];

foreach ($paths as $path) {
    $exists = is_dir($path);
    $writable = $exists && is_writable($path);
    echo "- $path: " . ($exists ? 'EXISTS' : 'MISSING') . ($writable ? ' (WRITABLE)' : ' (NOT WRITABLE)') . "\n";
}

// Check for common issues
echo "\nCommon Issues Check:\n";

// Check beneficiaries table structure
try {
    if (\Schema::hasTable('beneficiaries')) {
        $columns = \Schema::getColumnListing('beneficiaries');
        $hasUniqueId = in_array('unique_id', $columns);
        echo "- Beneficiaries unique_id column: " . ($hasUniqueId ? 'EXISTS' : 'MISSING') . "\n";
        
        // Check for beneficiary users without beneficiary records
        $beneficiaryUsers = \DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.name', 'Beneficiary')
            ->leftJoin('beneficiaries', 'users.id', '=', 'beneficiaries.user_id')
            ->whereNull('beneficiaries.user_id')
            ->count();
        
        if ($beneficiaryUsers > 0) {
            echo "- WARNING: $beneficiaryUsers beneficiary users without beneficiary records\n";
        }
    }
} catch (\Exception $e) {
    echo "- Beneficiaries check failed: " . $e->getMessage() . "\n";
}

// Check for syntax errors in key files
echo "\nSyntax Check:\n";
$keyFiles = [
    'app/Database/Migrations/SafeMigration.php',
    'app/Http/Controllers/Auth/LoginController.php',
    'app/Http/Controllers/Beneficiary/DashboardController.php'
];

foreach ($keyFiles as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        $output = [];
        $returnCode = 0;
        exec("php -l " . __DIR__ . "/" . $file . " 2>&1", $output, $returnCode);
        echo "- $file: " . ($returnCode === 0 ? 'OK' : 'SYNTAX ERROR - ' . implode(' ', $output)) . "\n";
    } else {
        echo "- $file: MISSING\n";
    }
}

echo "\nDebugging completed.\n";
