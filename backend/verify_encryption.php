<?php
// backend/verify_encryption.php

// Define constants expected by the app if any
// Load Database config
// require_once __DIR__ . '/config/database.php'; // Removed as it doesn't exist

// Setup basic autoloader or require files manually
require_once __DIR__ . '/app/Utils/Container.php';
require_once __DIR__ . '/app/Utils/EnvLoader.php';
require_once __DIR__ . '/app/Utils/Database.php';
require_once __DIR__ . '/app/Utils/Encryption.php';
require_once __DIR__ . '/app/Repositories/BaseRepository.php';
require_once __DIR__ . '/app/Repositories/ChatRepository.php';

use App\Repositories\ChatRepository;
use App\Utils\Encryption;
use App\Utils\Database;

echo "Starting Encryption Verification...\n";

// 1. Setup DB Connection
$db = Database::getInstance()->getConnection();

// 2. Get a valid user and room
$userStmt = $db->query("SELECT user_id FROM users LIMIT 1");
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

$roomStmt = $db->query("SELECT room_id FROM chat_rooms LIMIT 1");
$room = $roomStmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !$room) {
    die("Error: maintain existing users and rooms first.\n");
}

$userId = $user['user_id'];
$roomId = $room['room_id'];

echo "Using User ID: $userId, Room ID: $roomId\n";

// 3. Instantiate Repository
$repo = new ChatRepository();

// 4. Create a test message
$originalContent = "Secret Message " . rand(1000, 9999);
$messageData = [
    'room_id' => $roomId,
    'sender_id' => $userId,
    'content' => $originalContent,
    'message_type' => 'TEXT'
];

echo "Creating message: '$originalContent'\n";
$messageId = $repo->createMessage($messageData);

if (!$messageId) {
    die("Error: Failed to create message.\n");
}
echo "Message Created with ID: $messageId\n";

// 5. Verify Database Content (Raw)
$stmt = $db->prepare("SELECT content FROM chat_messages WHERE message_id = ?");
$stmt->execute([$messageId]);
$rawContent = $stmt->fetchColumn();

echo "Raw DB Content: $rawContent\n";

if ($rawContent === $originalContent) {
    echo "FAIL: Message stored in Plaintext in DB!\n";
} else {
    echo "PASS: Message is NOT Plaintext in DB.\n";
    // Check if it looks like base64
    if (base64_decode($rawContent, true)) {
        echo "PASS: Message seems to be Base64 encoded (Encrypted).\n";
    }
}

// 6. Verify Decryption via Repository
$messages = $repo->getRoomMessages($roomId, 100); // Fetch recent messages
$found = false;
foreach ($messages as $msg) {
    if ($msg['message_id'] == $messageId) {
        if ($msg['content'] === $originalContent) {
            echo "PASS: Repository correctly decrypted the message.\n";
            $found = true;
        } else {
            echo "FAIL: Repository returned: '{$msg['content']}', expected '$originalContent'\n";
        }
        break;
    }
}

if (!$found) {
    echo "FAIL: Could not find created message in getRoomMessages.\n";
}

// 7. Cleanup
$repo->deleteMessage($messageId);
echo "Cleanup: Message deleted.\n";
