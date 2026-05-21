<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Production Environment Debugging:\n";
echo "================================\n\n";

// Check Laravel version
echo "Laravel Version: " . app()->version() . "\n";
echo "Environment: " . app()->environment() . "\n";
echo "Debug Mode: " . (config('app.debug') ? 'ON' : 'OFF') . "\n\n";

// Check database connection
try {
    \DB::connection()->getPdo();
    echo "✓ Database Connection: OK\n";
    echo "Database Name: " . \DB::connection()->getDatabaseName() . "\n";
} catch (\Exception $e) {
    echo "✗ Database Connection: FAILED\n";
    echo "Error: " . $e->getMessage() . "\n";
}

// Check key tables
echo "\nChecking Database Tables:\n";
$tables = ['users', 'beneficiaries', 'distributions', 'relief_events'];
foreach ($tables as $table) {
    try {
        $count = \DB::table($table)->count();
        echo "✓ Table '$table': $count records\n";
    } catch (\Exception $e) {
        echo "✗ Table '$table': ERROR - " . $e->getMessage() . "\n";
    }
}

// Check beneficiary relationships
echo "\nChecking Beneficiary Relationships:\n";
try {
    $beneficiaryUsers = \App\Models\User::whereHas('role', function($query) {
        $query->where('name', 'Beneficiary');
    })->get();
    
    echo "Users with Beneficiary role: " . $beneficiaryUsers->count() . "\n";
    
    foreach ($beneficiaryUsers as $user) {
        $beneficiary = $user->beneficiary;
        echo "  - User {$user->email}: " . ($beneficiary ? "Has beneficiary record" : "MISSING beneficiary record") . "\n";
    }
} catch (\Exception $e) {
    echo "✗ Beneficiary relationship check failed: " . $e->getMessage() . "\n";
}

// Check routes
echo "\nChecking Key Routes:\n";
$routes = ['login.post', 'beneficiary.dashboard', 'logout'];
foreach ($routes as $routeName) {
    try {
        if (\Route::has($routeName)) {
            echo "✓ Route '$routeName': Exists\n";
        } else {
            echo "✗ Route '$routeName': Missing\n";
        }
    } catch (\Exception $e) {
        echo "✗ Route '$routeName': ERROR - " . $e->getMessage() . "\n";
    }
}

// Check cache status
echo "\nCache Status:\n";
try {
    echo "Config Cache: " . (app()->configurationIsCached() ? 'CACHED' : 'NOT CACHED') . "\n";
    echo "Routes Cache: " . (app()->routesAreCached() ? 'CACHED' : 'NOT CACHED') . "\n";
    echo "Events Cache: " . (app()->eventsAreCached() ? 'CACHED' : 'NOT CACHED') . "\n";
} catch (\Exception $e) {
    echo "✗ Cache check failed: " . $e->getMessage() . "\n";
}

echo "\nDebugging completed.\n";
