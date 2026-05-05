<?php
require_once 'config.php';

$email = 'manager@gmail.com';
$password = 'manager123';
$fullName = 'Admin Manager';
$role = 'Manager';

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, role = ?, username = ? WHERE email = ?");
        $stmt->execute([$hashedPassword, $role, 'manager', $email]);
        echo "User updated successfully.\n";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, full_name, role, username, is_active) VALUES (?, ?, ?, ?, ?, 1)");
        $stmt->execute([$email, $hashedPassword, $fullName, $role, 'manager']);
        echo "User created successfully.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
