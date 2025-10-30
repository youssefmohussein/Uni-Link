<?php
require_once __DIR__ . '/../utils/EnvLoader.php';

class InstallController {
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $env = loadEnv(__DIR__ . '/../.env');
        $lockFile = __DIR__ . '/../install.lock';

        // 🧱 Prevent re-installation if lock file exists
        if (file_exists($lockFile)) {
            header('Content-Type: text/html');
            echo "
            <div style='font-family:Arial,sans-serif;text-align:center;margin-top:80px;'>
                <h2 style='color:#555;'>⚙️ Unilink is already installed</h2>
                <p style='color:#777;'>To reinstall, delete the <b>install.lock</b> file from the backend folder.</p>
            </div>";
            return;
        }

        // 🧩 Show login form on GET
        if ($method === 'GET') {
            header('Content-Type: text/html');
            echo '
            <html>
            <head>
                <title>Unilink Installer</title>
                <style>
                    body { font-family: Arial,sans-serif; background: #f2f2f2; display: flex;
                           justify-content: center; align-items: center; height: 100vh; margin:0; }
                    form { background: #fff; padding: 30px 40px; border-radius: 12px; 
                           box-shadow: 0 6px 20px rgba(0,0,0,0.1); min-width: 320px; }
                    input { width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc; }
                    button { width: 100%; padding: 12px; border-radius: 5px; border: none; background: #007bff; color: #fff; cursor:pointer; font-size:16px; }
                    button:hover { background: #0056b3; }
                    h2 { margin-top:0; text-align:center; color:#333; }
                    label { font-weight: bold; color: #555; display: block; margin-bottom:5px; }
                    .error { color: red; text-align:center; margin-bottom:15px; }
                    .success { color: green; text-align:center; margin-bottom:15px; }
                    p { color: #555; text-align:center; }
                </style>
            </head>
            <body>
                <form method="POST">
                    <h2>Unilink Installer</h2>
                    <label>Email</label>
                    <input type="email" name="email" required placeholder="Enter admin email">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Enter admin password">
                    <button type="submit">Install</button>
                </form>
            </body>
            </html>';
            return;
        }

        // ⚙️ Handle POST (installation)
        if ($method === 'POST') {
            $inputEmail = $_POST['email'] ?? '';
            $inputPass = $_POST['password'] ?? '';

            // Verify credentials
            if ($inputEmail !== $env['ADMIN_EMAIL'] || $inputPass !== $env['ADMIN_PASS']) {
                echo "<div class='error'>❌ Invalid credentials. Please check your email and password.</div>";
                return;
            }

            $host = $env['DB_HOST'];
            $user = $env['DB_USER'];
            $pass = $env['DB_PASS'];
            $dbName = $env['DB_NAME'];
            $sqlFile = __DIR__ . '/../' . $env['SQL_FILE'];

            try {
                // Connect to MySQL (no DB selected yet)
                $pdo = new PDO("mysql:host=$host", $user, $pass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Create DB if not exists and select it
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $pdo->exec("USE `$dbName`");

                // Check SQL file
                if (!file_exists($sqlFile)) {
                    throw new Exception("SQL file not found: $sqlFile");
                }

                // Execute SQL
                $sql = file_get_contents($sqlFile);
                $pdo->exec($sql);

                // Create lock file
                file_put_contents($lockFile, "Installed on " . date('Y-m-d H:i:s'));

                echo "<div class='success'>✅ Database <b>$dbName</b> installed successfully!</div>";
                echo "<p>You can now safely remove <b>install.sql</b> for security reasons.</p>";

                // Export database automatically
                $exportDir = __DIR__ . '/../exports';
                if (!is_dir($exportDir)) mkdir($exportDir, 0777, true);
                $exportFile = "$exportDir/{$dbName}_backup.sql";
                $dumpCommand = "mysqldump -h $host -u $user " . (!empty($pass) ? "-p$pass " : "") . "$dbName > \"$exportFile\"";
                exec($dumpCommand, $output, $resultCode);

                if ($resultCode === 0) {
                    echo "<p>📁 Database exported to <code>$exportFile</code></p>";
                } else {
                    echo "<p style='color:red;'>⚠️ Failed to export database automatically. Check server permissions or mysqldump path.</p>";
                }

            } catch (PDOException $e) {
                echo "<div class='error'>❌ Installation failed: " . $e->getMessage() . "</div>";
            } catch (Exception $e) {
                echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
            }
        }
    }
}
?>
