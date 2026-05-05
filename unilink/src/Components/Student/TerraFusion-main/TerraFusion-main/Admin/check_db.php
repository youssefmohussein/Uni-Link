<?php
require_once 'app/Models/Database.php';
require_once 'app/Helpers/Security.php';
require_once 'app/Helpers/Session.php';

use App\Models\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    // Check if users table exists
    $stmt = $db->query("SHOW TABLES LIKE 'users'");
    $tableExists = $stmt->rowCount() > 0;
    
    echo "Users table exists: " . ($tableExists ? "Yes" : "No") . "\n";
    
    if ($tableExists) {
        // Show table structure
        $stmt = $db->query("DESCRIBE users");
        $columns = $stmt->fetchAll();
        
        echo "\nUsers table structure:\n";
        foreach ($columns as $column) {
            echo "- {$column['Field']} ({$column['Type']})\n";
        }
        
        // Show existing users
        $stmt = $db->query("SELECT * FROM users");
        $users = $stmt->fetchAll();
        
        echo "\nExisting users (" . count($users) . "):\n";
        foreach ($users as $user) {
            echo "- ID: {$user['user_id']}, Username: {$user['username']}, Role: {$user['role']}\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
