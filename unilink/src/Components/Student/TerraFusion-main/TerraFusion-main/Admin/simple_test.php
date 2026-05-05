<?php
require_once 'app/Models/Database.php';

use App\Models\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Testing database connection and user lookup...\n\n";
    
    // Test 1: Get user
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => 'manager']);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "Found user: {$user['username']}\n";
        echo "User ID: {$user['user_id']}\n";
        echo "Role: {$user['role']}\n";
        echo "Password hash: " . substr($user['password_hash'], 0, 20) . "...\n";
        
        // Test password verification
        $password = 'password123';
        if (password_verify($password, $user['password_hash'])) {
            echo "Password verification: SUCCESS\n";
        } else {
            echo "Password verification: FAILED\n";
            
            // Show what the correct hash would be
            $correctHash = password_hash($password, PASSWORD_DEFAULT);
            echo "Correct hash should be: " . substr($correctHash, 0, 20) . "...\n";
        }
    } else {
        echo "User not found!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
