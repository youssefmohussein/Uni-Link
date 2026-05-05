<?php
require_once 'app/Models/Database.php';
require_once 'app/Helpers/Security.php';
require_once 'app/Helpers/Session.php';

use App\Models\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    // Get users with their password hashes
    $stmt = $db->query("SELECT user_id, username, password_hash FROM users");
    $users = $stmt->fetchAll();
    
    echo "Checking password verification:\n\n";
    
    foreach ($users as $user) {
        echo "User: {$user['username']}\n";
        echo "Hash: " . substr($user['password_hash'], 0, 20) . "...\n";
        
        // Test password verification
        $testPassword = 'password123';
        $isValid = password_verify($testPassword, $user['password_hash']);
        echo "Password 'password123' verifies: " . ($isValid ? "YES" : "NO") . "\n";
        
        if (!$isValid) {
            // Generate new hash for comparison
            $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
            echo "New hash would be: " . substr($newHash, 0, 20) . "...\n";
            
            // Update the password hash
            $updateStmt = $db->prepare("UPDATE users SET password_hash = :hash WHERE user_id = :id");
            $updateStmt->execute(['hash' => $newHash, 'id' => $user['user_id']]);
            echo "Updated password hash for {$user['username']}\n";
        }
        
        echo "---\n";
    }
    
    echo "\nPassword verification and update completed!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
