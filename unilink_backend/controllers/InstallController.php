<?php
require_once __DIR__ . '/../utils/EnvLoader.php';

class InstallController {
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];

        // Show form on GET
        if ($method === 'GET') {
            header('Content-Type: text/html');
            echo '
            <html>
            <head>
                <title>Unilink Installer Login</title>
                <style>
                    body { font-family: Arial; background: #f7f7f7; display: flex; height: 100vh; align-items: center; justify-content: center; margin: 0; }
                    form { background: white; padding: 20px 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); min-width: 300px; }
                    input { display: block; margin-bottom: 10px; width: 100%; padding: 8px; box-sizing: border-box; }
                    button { background: #007bff; color: white; border: none; padding: 10px; width: 100%; border-radius: 5px; cursor:pointer; }
                    button:hover { background: #0056b3; }
                    h2 { margin-top: 0; color: #333; }
                    label { display: block; margin-bottom: 5px; color: #666; }
                </style>
            </head>
            <body>
                <form method="POST">
                    <h2>Installer Login</h2>
                    <label>Email:</label>
                    <input type="email" name="email" required placeholder="Enter admin email">
                    <label>Password:</label>
                    <input type="password" name="password" required placeholder="Enter admin password">
                    <button type="submit">Install</button>
                </form>
            </body>
            </html>';
            return;
        }

        // Handle POST (install process)
        if ($method === 'POST') {
            $env = loadEnv(__DIR__ . '/../.env');

            $inputEmail = $_POST['email'] ?? '';
            $inputPass = $_POST['password'] ?? '';

            if ($inputEmail !== $env['ADMIN_EMAIL'] || $inputPass !== $env['ADMIN_PASS']) {
                echo "<h3 style='color:red;'>❌ Invalid credentials.</h3>";
                return;
            }

            $host = $env['DB_HOST'];
            $user = $env['DB_USER'];
            $pass = $env['DB_PASS'];
            $dbName = $env['DB_NAME'];
            $sqlFile = __DIR__ . '/../' . $env['SQL_FILE'];

            try {
                // 1️⃣ Connect to MySQL WITHOUT specifying database
                $pdo = new PDO("mysql:host=$host", $user, $pass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // 2️⃣ Create the database if not exists
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName`");
                $pdo->exec("USE `$dbName`");

                // 3️⃣ Execute SQL from file
                if (!file_exists($sqlFile)) {
                    throw new Exception("SQL file not found: $sqlFile");
                }

                $sql = file_get_contents($sqlFile);
                $pdo->exec($sql);

                echo "<h3 style='color:green;'>✅ Database '$dbName' installed successfully!</h3>";

            } catch (PDOException $e) {
                echo "<h3 style='color:red;'>❌ Installation failed: " . $e->getMessage() . "</h3>";
            }
        }
    }
}
