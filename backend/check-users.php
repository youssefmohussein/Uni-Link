<?php
/**
 * Quick User Check Script
 * Shows if any users exist in the database
 */

// Simple direct database check without using the framework
$envFile = __DIR__ . '/.env';

if (!file_exists($envFile)) {
    die("ERROR: .env file not found!\n");
}

// Parse .env file
$env = [];
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    list($key, $value) = explode('=', $line, 2);
    $env[trim($key)] = trim($value);
}

try {
    $pdo = new PDO(
        "mysql:host={$env['DB_HOST']};dbname={$env['DB_NAME']};charset=utf8mb4",
        $env['DB_USER'],
        $env['DB_PASS']
    );
    
    echo "✓ Database connected!\n\n";
    
    // Count users
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "Total users: $count\n\n";
    
    if ($count > 0) {
        echo "Sample users:\n";
        echo str_repeat("-", 80) . "\n";
        printf("%-5s %-20s %-30s %-15s\n", "ID", "Username", "Email", "Role");
        echo str_repeat("-", 80) . "\n";
        
        $stmt = $pdo->query("SELECT user_id, username, email, role FROM users LIMIT 10");
        while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            printf("%-5s %-20s %-30s %-15s\n", 
                $user['user_id'], 
                $user['username'], 
                $user['email'], 
                $user['role']
            );
        }
        echo str_repeat("-", 80) . "\n";
        echo "\nYou can login with any of the above usernames or emails.\n";
    } else {
        echo "⚠ No users found in database!\n";
        echo "\nYou need to create a user first. Here's a sample SQL:\n\n";
        echo "INSERT INTO users (username, email, password_hash, role) VALUES\n";
        echo "('admin', 'admin@example.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN');\n";
        echo "\nPassword for this user is: 'password'\n";
    }
    
} catch (PDOException $e) {
    die("ERROR: " . $e->getMessage() . "\n");
}
