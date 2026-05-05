<?php
require_once 'app/Models/Database.php';

use App\Models\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Adding missing columns to users table...\n";
    
    // Add last_login column if it doesn't exist
    $db->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS last_login DATETIME NULL");
    echo "Added last_login column\n";
    
    // Add is_active column if it doesn't exist (for future use)
    $db->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS is_active TINYINT(1) DEFAULT 1");
    echo "Added is_active column\n";
    
    // Show the updated table structure
    $stmt = $db->query("DESCRIBE users");
    $columns = $stmt->fetchAll();
    
    echo "\nUpdated users table structure:\n";
    foreach ($columns as $column) {
        echo "- {$column['Field']} ({$column['Type']})\n";
    }
    
    echo "\nDatabase schema updated successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
