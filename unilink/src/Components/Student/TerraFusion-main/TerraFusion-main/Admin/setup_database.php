<?php
// Database setup script
require_once 'app/Models/Database.php';
require_once 'app/Helpers/Security.php';
require_once 'app/Helpers/Session.php';

use App\Models\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    // Read and execute database schema
    $schema = file_get_contents(__DIR__ . '/database_schema.sql');
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            $db->exec($statement);
        }
    }
    
    echo "Database schema created successfully!\n";
    
    // Execute user setup
    $users = file_get_contents(__DIR__ . '/setup_users_phpmyadmin.sql');
    $statements = array_filter(array_map('trim', explode(';', $users)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            $db->exec($statement);
        }
    }
    
    echo "Users created successfully!\n";
    
    // Verify users
    $stmt = $db->query("SELECT username, role_id FROM users WHERE username IN ('manager', 'chef', 'tablemanager', 'waiter')");
    $users = $stmt->fetchAll();
    
    echo "\nCreated users:\n";
    foreach ($users as $user) {
        $role = ['Manager', 'Chef Boss', 'Table Manager', 'Waiter'][$user['role_id'] - 1] ?? 'Unknown';
        echo "- {$user['username']} (Role: {$role})\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
