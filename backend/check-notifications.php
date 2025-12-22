<?php
/**
 * Check Notifications Table
 * 
 * This script checks if notifications are being created from mentions.
 * Access it at: http://localhost/backend/check-notifications.php
 */

require_once __DIR__ . '/bootstrap.php';

use App\Utils\Database;

try {
    $db = Database::getInstance()->getConnection();

    echo "<h2>Recent Notifications</h2>";
    $stmt = $db->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 10");
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($notifications)) {
        echo "<p style='color: red;'>❌ No notifications found in database!</p>";
        echo "<p>This confirms notifications are NOT being created from mentions.</p>";
    } else {
        echo "<p style='color: green;'>✅ Found " . count($notifications) . " notifications</p>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>User ID</th><th>Type</th><th>Title</th><th>Message</th><th>Created At</th></tr>";
        foreach ($notifications as $notif) {
            echo "<tr>";
            echo "<td>" . $notif['notification_id'] . "</td>";
            echo "<td>" . $notif['user_id'] . "</td>";
            echo "<td>" . $notif['type'] . "</td>";
            echo "<td>" . $notif['title'] . "</td>";
            echo "<td>" . $notif['message'] . "</td>";
            echo "<td>" . $notif['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    echo "<hr>";
    echo "<h2>Recent Mentions</h2>";
    $stmt = $db->query("SELECT * FROM chat_mentions ORDER BY created_at DESC LIMIT 10");
    $mentions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<p>Found " . count($mentions) . " mentions</p>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Mention ID</th><th>Message ID</th><th>User ID</th><th>Created At</th></tr>";
    foreach ($mentions as $mention) {
        echo "<tr>";
        echo "<td>" . $mention['mention_id'] . "</td>";
        echo "<td>" . $mention['message_id'] . "</td>";
        echo "<td>" . $mention['user_id'] . "</td>";
        echo "<td>" . $mention['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
