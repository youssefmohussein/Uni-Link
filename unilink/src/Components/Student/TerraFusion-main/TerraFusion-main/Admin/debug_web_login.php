<?php
require_once 'app/Models/Database.php';
require_once 'app/Helpers/Security.php';
require_once 'app/Helpers/Session.php';
require_once 'app/Controllers/AuthController.php';

use App\Models\Database;
use App\Controllers\AuthController;

echo "=== DEBUGGING WEB LOGIN ISSUE ===\n\n";

try {
    $db = \App\Models\Database::getInstance()->getConnection();
    
    // 1. Check all users in database
    echo "1. CHECKING ALL USERS IN DATABASE:\n";
    $stmt = $db->query("SELECT user_id, username, role, password_hash FROM users");
    $users = $stmt->fetchAll();
    
    foreach ($users as $user) {
        echo "User: {$user['username']} | Role: {$user['role']} | Hash: " . substr($user['password_hash'], 0, 20) . "...\n";
        
        // Test password verification
        $password = 'password123';
        $isValid = password_verify($password, $user['password_hash']);
        echo "  -> Password 'password123' verifies: " . ($isValid ? "YES" : "NO") . "\n";
        
        if (!$isValid) {
            echo "  -> REHASHING PASSWORD...\n";
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $updateStmt = $db->prepare("UPDATE users SET password_hash = :hash WHERE user_id = :id");
            $updateStmt->execute(['hash' => $newHash, 'id' => $user['user_id']]);
            echo "  -> Password updated for {$user['username']}\n";
        }
        echo "\n";
    }
    
    // 2. Test session fingerprint generation
    echo "2. TESTING SESSION FINGERPRINT:\n";
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'CLI-Test';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $fingerprint = hash('sha256', $userAgent . $ip);
    echo "User Agent: " . substr($userAgent, 0, 30) . "...\n";
    echo "IP: $ip\n";
    echo "Fingerprint: " . substr($fingerprint, 0, 20) . "...\n\n";
    
    // 3. Test complete authentication flow
    echo "3. TESTING COMPLETE AUTHENTICATION FLOW:\n";
    
    foreach (['manager', 'chef', 'tablemanager', 'waiter'] as $username) {
        echo "Testing login for: $username\n";
        $result = \App\Controllers\AuthController::login($username, 'password123');

        if ($result['success']) {
            echo "  -> SUCCESS: User {$result['user']['username']} logged in\n";
            echo "  -> Role: {$result['user']['role']}\n";

            // Test isLoggedIn
            $isLoggedIn = \App\Controllers\AuthController::isLoggedIn();
            echo "  -> isLoggedIn check: " . ($isLoggedIn ? "YES" : "NO") . "\n";

            // Logout for next test
            \App\Controllers\AuthController::logout();
        } else {
            echo "  -> FAILED: {$result['message']}\n";
        }
        echo "\n";
    }
    
    echo "=== DEBUGGING COMPLETE ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
