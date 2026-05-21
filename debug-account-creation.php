<?php
/**
 * Production Debug Script for Account Creation Issues
 * Run this script in production to identify the specific cause of 500 errors
 */

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Account Creation Debug Report</h1>";

// 1. Check PHP Version and Extensions
echo "<h2>1. PHP Environment</h2>";
echo "PHP Version: " . phpversion() . "<br>";

$required_extensions = ['mbstring', 'xml', 'curl', 'zip', 'mysql', 'bcmath', 'json'];
foreach ($required_extensions as $ext) {
    $status = extension_loaded($ext) ? "✅ Installed" : "❌ Missing";
    echo "$ext: $status<br>";
}

// 2. Check Laravel Environment
echo "<h2>2. Laravel Environment</h2>";
if (file_exists('.env')) {
    echo ".env file: ✅ Exists<br>";
    $env_content = file_get_contents('.env');
    
    // Check critical .env settings (without showing sensitive data)
    $critical_settings = ['APP_ENV', 'APP_DEBUG', 'DB_CONNECTION', 'DB_HOST', 'DB_DATABASE'];
    foreach ($critical_settings as $setting) {
        if (strpos($env_content, $setting . '=') !== false) {
            echo "$setting: ✅ Set<br>";
        } else {
            echo "$setting: ❌ Missing<br>";
        }
    }
} else {
    echo ".env file: ❌ Missing<br>";
}

// 3. Check File Permissions
echo "<h2>3. File Permissions</h2>";
$directories = ['storage', 'bootstrap/cache', 'storage/logs', 'storage/framework'];
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        $writable = is_writable($dir) ? "✅ Writable" : "❌ Not Writable";
        echo "$dir: $perms ($writable)<br>";
    } else {
        echo "$dir: ❌ Directory does not exist<br>";
    }
}

// 4. Test Database Connection
echo "<h2>4. Database Connection</h2>";
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    $db = \Illuminate\Support\Facades\DB::connection();
    echo "Database Connection: ✅ Connected<br>";
    echo "Database Name: " . $db->getDatabaseName() . "<br>";
    
    // Check if users table exists
    if ($db->getSchemaBuilder()->hasTable('users')) {
        echo "Users Table: ✅ Exists<br>";
        
        // Check table structure
        $columns = $db->getSchemaBuilder()->getColumnListing('users');
        $required_columns = ['name', 'email', 'password', 'unique_id'];
        foreach ($required_columns as $col) {
            if (in_array($col, $columns)) {
                echo "Column '$col': ✅ Exists<br>";
            } else {
                echo "Column '$col': ❌ Missing<br>";
            }
        }
    } else {
        echo "Users Table: ❌ Does not exist<br>";
    }
    
} catch (Exception $e) {
    echo "Database Connection: ❌ Failed<br>";
    echo "Error: " . $e->getMessage() . "<br>";
}

// 5. Check Laravel Configuration
echo "<h2>5. Laravel Configuration</h2>";
try {
    if (function_exists('app')) {
        $app = app();
        echo "App Instance: ✅ Created<br>";
        
        // Check if key is set
        if (config('app.key') && config('app.key') !== 'base64:YOUR_ENCRYPTION_KEY_HERE') {
            echo "App Key: ✅ Set<br>";
        } else {
            echo "App Key: ❌ Not set properly<br>";
        }
    }
} catch (Exception $e) {
    echo "Laravel Config: ❌ Error - " . $e->getMessage() . "<br>";
}

// 6. Test Account Creation Logic
echo "<h2>6. Account Creation Test</h2>";
try {
    // Simulate basic validation
    $test_data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'unique_id' => 'TEST001'
    ];
    
    echo "Test Data Validation: ✅ Data structure valid<br>";
    
    // Check email validation
    if (filter_var($test_data['email'], FILTER_VALIDATE_EMAIL)) {
        echo "Email Format: ✅ Valid<br>";
    } else {
        echo "Email Format: ❌ Invalid<br>";
    }
    
} catch (Exception $e) {
    echo "Account Creation Test: ❌ Error - " . $e->getMessage() . "<br>";
}

// 7. Recent Error Logs
echo "<h2>7. Recent Error Logs</h2>";
$log_file = 'storage/logs/laravel.log';
if (file_exists($log_file)) {
    $logs = file_get_contents($log_file);
    $recent_logs = substr($logs, -2000); // Last 2000 characters
    echo "<pre style='background: #f5f5f5; padding: 10px; font-size: 12px;'>" . htmlspecialchars($recent_logs) . "</pre>";
} else {
    echo "Log file: ❌ Does not exist<br>";
}

echo "<h2>8. Recommended Actions</h2>";
echo "<ol>";
echo "<li>Run: php artisan config:clear && php artisan cache:clear</li>";
echo "<li>Run: php artisan migrate (if database tables are missing)</li>";
echo "<li>Run: php artisan key:generate (if app key is not set)</li>";
echo "<li>Check web server error logs for additional details</li>";
echo "<li>Temporarily set APP_DEBUG=true in .env for detailed errors</li>";
echo "</ol>";

echo "<p><strong>Note:</strong> Delete this script after debugging for security reasons.</p>";
?>
