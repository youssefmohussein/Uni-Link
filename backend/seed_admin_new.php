<?php
require_once __DIR__ . '/config/autoload.php';

use App\Repositories\UserRepository;
use App\Utils\Database;

try {
    $db = Database::getInstance()->getConnection();
    $userRepo = new UserRepository($db);

    $username = 'admin';
    $email = 'admin@unilink.com';
    $password = 'admin123';
    
    // Check if user exists
    if ($userRepo->findByUsername($username)) {
        echo "Admin user 'admin' already exists.\n";
        exit;
    }

    $userData = [
        'username' => $username,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'role' => 'ADMIN',
        'bio' => 'System Administrator',
        'profile_image' => 'uploads/defaults/admin.png'
    ];

    $userId = $userRepo->create($userData);

    if ($userId) {
        // Also add to admins table since role is ADMIN
        $stmt = $db->prepare("INSERT INTO admins (user_id, permissions) VALUES (?, ?)");
        $stmt->execute([$userId, json_encode(['all'])]);
        
        echo "Admin user created successfully!\n";
        echo "Username: $username\n";
        echo "Password: $password\n";
    } else {
        echo "Failed to create admin user.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
