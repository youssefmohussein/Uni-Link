<?php
require_once __DIR__ . '/EnvLoader.php';

function installDatabaseStreamed() {
    header("Content-Type: application/json");

    // Load .env variables
    $env = loadEnv(__DIR__ . '/../.env');
    $dbHost = $env['DB_HOST'] ?? 'localhost';
    $dbUser = $env['DB_USER'] ?? 'root';
    $dbPass = $env['DB_PASS'] ?? '';
    $dbName = $env['DB_NAME'] ?? 'unilink_db';
    $sqlFile = __DIR__ . '/../' . ($env['SQL_FILE'] ?? 'install.sql');
    $adminEmail = $env['ADMIN_EMAIL'] ?? '';
    $adminPass = $env['ADMIN_PASS'] ?? '';
    $importFile = __DIR__ . '/../exports/' . $dbName . '_backup.sql';

    try {
        // Step 1️⃣ — Connect to MySQL (no DB yet)
        $pdo = new PDO("mysql:host=$dbHost", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Step 2️⃣ — Create database if not exists
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$dbName`");

        // Step 3️⃣ — If export file exists, import it
        if (file_exists($importFile)) {
            $sql = file_get_contents($importFile);
            $pdo->exec($sql);
            echo json_encode([
                "status" => "success",
                "message" => "Database imported from existing backup file successfully."
            ]);
            return;
        }

        // Step 4️⃣ — Otherwise, install using SQL file
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

        // Step 5️⃣ — Create admin user from .env
        if (!empty($adminEmail) && !empty($adminPass)) {
            $hashedPassword = password_hash($adminPass, PASSWORD_BCRYPT);

            // Check if user already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $stmt->execute([':email' => $adminEmail]);
            $exists = $stmt->fetchColumn();

            if (!$exists) {
                $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
                $stmt->execute([':email' => $adminEmail, ':password' => $hashedPassword]);
            }
        }

        // Step 6️⃣ — Auto-export for sharing
        $exportsDir = __DIR__ . '/../exports/';
        if (!is_dir($exportsDir)) {
            mkdir($exportsDir, 0777, true);
        }

        $exportFile = $exportsDir . $dbName . '_backup.sql';
        $dumpCommand = "mysqldump -h $dbHost -u $dbUser " . (!empty($dbPass) ? "-p$dbPass " : "") . "$dbName > \"$exportFile\"";
        exec($dumpCommand, $output, $resultCode);

        if ($resultCode === 0) {
            $exportMsg = "Backup created at: $exportFile";
        } else {
            $exportMsg = "⚠️ Failed to create backup automatically.";
        }

        echo json_encode([
            "status" => "success",
            "message" => "Database and admin created successfully.",
            "backup" => $exportMsg
        ]);

    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    }
}
?>
