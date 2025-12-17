<?php
/**
 * Database Connection Test Script
 * Run this to verify database connectivity and check user data
 */

require_once __DIR__ . '/config/autoload.php';

use App\Utils\Database;

echo "=== Database Connection Test ===\n\n";

try {
    // Test database connection
    echo "1. Testing database connection...\n";
    $db = Database::getInstance()->getConnection();
    echo "   ✓ Database connected successfully!\n\n";
    
    // Check users table
    echo "2. Checking users table...\n";
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM users");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   ✓ Total users in database: {$result['count']}\n\n";
    
    // List first 5 users
    if ($result['count'] > 0) {
        echo "3. Sample users (first 5):\n";
        $stmt = $db->prepare("SELECT user_id, username, email, role FROM users LIMIT 5");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($users as $user) {
            echo "   - ID: {$user['user_id']}, Username: {$user['username']}, Email: {$user['email']}, Role: {$user['role']}\n";
        }
        echo "\n";
    } else {
        echo "3. No users found in database!\n";
        echo "   You may need to create a test user first.\n\n";
    }
    
    echo "=== Test Complete ===\n";
    echo "If you see users listed above, try logging in with one of those credentials.\n";
    
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
    echo "\nPlease check:\n";
    echo "1. Database server is running\n";
    echo "2. .env file has correct database credentials\n";
    echo "3. Database exists and schema is imported\n";
}
