<?php
require_once 'app/Controllers/AuthController.php';
require_once 'app/Models/Database.php';
require_once 'app/Helpers/Security.php';
require_once 'app/Helpers/Session.php';

use App\Controllers\AuthController;
use App\Models\Database;

echo "Testing login step by step...\n\n";

// Test 1: Check if users exist
$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT username, password_hash FROM users WHERE username = 'waiter'");
$user = $stmt->fetch();

if ($user) {
    echo "Found user: {$user['username']}\n";
    echo "Password hash: " . substr($user['password_hash'], 0, 20) . "...\n";
    
    // Test password verification
    $password = 'password123';
    if (password_verify($password, $user['password_hash'])) {
        echo "Password verification: SUCCESS\n";
        
        // Test actual login
        echo "\nTesting AuthController login...\n";
        $result = AuthController::login('waiter', 'password123');
        
        if ($result['success']) {
            echo "Login SUCCESS!\n";
            echo "User: {$result['user']['username']}\n";
            echo "Role: {$result['user']['role']}\n";
        } else {
            echo "Login FAILED!\n";
            echo "Error: {$result['message']}\n";
        }
    } else {
        echo "Password verification: FAILED\n";
    }
} else {
    echo "User 'waiter' not found!\n";
}
?>
