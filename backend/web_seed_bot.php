<?php
// backend/web_seed_bot.php

// Helper to load autoloader if not already loaded (though in a full app we better rely on index.php)
// But since we want to run this standalone, we need to bootstrap.
require_once __DIR__ . '/config/autoload.php';
require_once __DIR__ . '/config/env_loader.php';
loadEnv(__DIR__ . '/.env');

use App\Repositories\UserRepository;
use App\Utils\Database;

// Basic error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/html');

echo "<h1>Seeding Bot User</h1>";

try {
    $db = Database::getInstance()->getConnection();
    $userRepo = new UserRepository($db);

    $username = 'unilink';
    $email = 'bot@unilink.com';
    
    // Check if user exists
    $existing = $userRepo->findByUsername($username);
    if ($existing) {
        echo "<p style='color:orange'>Bot user 'unilink' already exists with ID: " . $existing['user_id'] . "</p>";
    } else {
        $userData = [
            'username' => $username,
            'email' => $email,
            'password' => password_hash('unilink_bot_secret', PASSWORD_DEFAULT),
            'role' => 'ADMIN',
            'bio' => 'I am the Uni-Link AI Assistant. Mention @unilink to ask me anything!',
            'profile_image' => 'uploads/defaults/bot.png'
        ];

        $userId = $userRepo->create($userData);

        if ($userId) {
            // Add to admins
            $stmt = $db->prepare("INSERT INTO admins (user_id, permissions) VALUES (?, ?)");
            $stmt->execute([$userId, json_encode(['all'])]);
            
            echo "<p style='color:green'>Bot user 'unilink' created successfully with ID: $userId</p>";
        } else {
            echo "<p style='color:red'>Failed to create bot user.</p>";
        }
    }

} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
