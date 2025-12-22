<?php
/**
 * Test Notification Creation
 * 
 * This script creates a test notification to verify the notification system works.
 * Access it at: http://localhost/backend/test-notification.php?user_id=YOUR_USER_ID
 */

require_once __DIR__ . '/bootstrap.php';

use App\Repositories\NotificationRepository;
use App\Utils\Database;

// Get user_id from query parameter
$userId = isset($_GET['user_id']) ? (int) $_GET['user_id'] : null;

if (!$userId) {
    die("Please provide user_id in query parameter. Example: test-notification.php?user_id=1");
}

try {
    $notificationRepo = new NotificationRepository();

    // Create a test notification
    $notificationId = $notificationRepo->create([
        'user_id' => $userId,
        'type' => 'CHAT_MENTION',
        'title' => 'Test Notification',
        'message' => 'This is a test notification created at ' . date('Y-m-d H:i:s'),
        'related_entity_type' => 'CHAT_ROOM',
        'related_entity_id' => 1
    ]);

    echo "✅ Success! Created test notification with ID: " . $notificationId . "\n";
    echo "User ID: " . $userId . "\n";
    echo "Check your notification bell to see if it appears.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString();
}
