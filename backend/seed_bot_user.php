<?php
require_once __DIR__ . '/config/autoload.php';

use App\Repositories\UserRepository;
use App\Utils\Database;

try {
    $db = Database::getInstance()->getConnection();
    $userRepo = new UserRepository($db);

    $username = 'unilink';
    $email = 'bot@unilink.com';
    
    // Check if user exists
    if ($userRepo->findByUsername($username)) {
        echo "Bot user 'unilink' already exists.\n";
        exit;
    }

    $userData = [
        'username' => $username,
        'email' => $email,
        'password' => password_hash('unilink_bot_secret', PASSWORD_DEFAULT),
        'role' => 'ADMIN', // specific role for bot
        'bio' => 'I am the Uni-Link AI Assistant. Mention @unilink to ask me anything!',
        'profile_image' => 'uploads/defaults/bot.png'
    ];

    $userId = $userRepo->create($userData);

    if ($userId) {
        // Also add to admins table since role is ADMIN
        $stmt = $db->prepare("INSERT INTO admins (user_id, permissions) VALUES (?, ?)");
        $stmt->execute([$userId, json_encode(['all'])]);
        
        echo "Bot user 'unilink' created successfully with ID: $userId\n";
    } else {
        echo "Failed to create bot user.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
