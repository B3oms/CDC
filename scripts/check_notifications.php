<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\NotificationService;

$userId = 1;
try {
    $notifications = NotificationService::getRecentNotifications($userId, 10);
    echo "Retrieved " . count($notifications) . " notifications for user {$userId}.\n";
    foreach ($notifications as $n) {
        echo "- {$n->title}: {$n->message}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
