<?php
require_once __DIR__ . '/EnvLoader.php';

$env = loadEnv(__DIR__ . '/../.env');

$host = $env['DB_HOST'];
$user = $env['DB_USER'];
$password = $env['DB_PASS'];
$dbname = $env['DB_NAME'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . $e->getMessage()
    ]));
}
?>
