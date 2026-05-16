<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Bootstrapping the application so Eloquent and facades work
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Notification;

// Change user id if necessary
$userId = 1;

try {
    Notification::createNotification($userId, 'test', 'Test Notification', 'This is a test notification', null, null);
    echo "Test notification created for user {$userId}.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
