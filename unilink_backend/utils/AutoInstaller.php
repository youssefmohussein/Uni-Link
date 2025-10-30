<?php
require_once __DIR__ . '/EnvLoader.php';

function installDatabaseStreamed() {
    $env = loadEnv(__DIR__ . '/../.env');
    $dbHost = $env['DB_HOST'];
    $dbUser = $env['DB_USER'];
    $dbPass = $env['DB_PASS'];
    $dbName = $env['DB_NAME'];
    $sqlFile = __DIR__ . '/../' . $env['SQL_FILE'];
    $adminEmail = $env['ADMIN_EMAIL'];
    $adminPass = $env['ADMIN_PASS'];

    try {
        // Step 1: Connect to MySQL (no db yet)
        $pdo = new PDO("mysql:host=$dbHost", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Step 2: Create database if not exists
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$dbName`");

        // Step 3: Load and execute SQL file
        if (!file_exists($sqlFile)) {
            throw new Exception("SQL file not found at $sqlFile");
        }

        $query = '';
        foreach (file($sqlFile) as $line) {
            if (substr($line, 0, 2) == '--' || trim($line) == '') continue;
            $query .= $line;
            if (substr(trim($line), -1) == ';') {
                $pdo->exec($query);
                $query = '';
            }
        }

        // Step 4: Insert admin automatically
        $hashedPassword = password_hash($adminPass, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
        $stmt->execute([':email' => $adminEmail, ':password' => $hashedPassword]);

        echo json_encode(["status" => "success", "message" => "Database and admin created successfully."]);

    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
?>
