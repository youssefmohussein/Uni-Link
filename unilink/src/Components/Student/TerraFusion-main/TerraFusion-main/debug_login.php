<?php
require_once 'config/database.php';

$email = 'manager@gmail.com';
$password = 'manager123';

echo "Checking user: $email\n";

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "User found in DB.\n";
    echo "Role: " . $user['role'] . "\n";
    echo "Hash: " . $user['password_hash'] . "\n";
    
    if (password_verify($password, $user['password_hash'])) {
        echo "Password MATCHES.\n";
    } else {
        echo "Password DOES NOT MATCH.\n";
        echo "Generating new hash for '$password'...\n";
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        echo "New Hash: $newHash\n";
        
        // Auto-fix
        $update = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
        $update->execute([$newHash, $user['user_id']]);
        echo "Password updated in DB.\n";
    }
} else {
    echo "User NOT found.\n";
}
?>
