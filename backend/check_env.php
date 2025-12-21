<?php
require_once __DIR__ . '/config/env_loader.php';
loadEnv(__DIR__ . '/.env');

header('Content-Type: text/plain');

$apiKey = $_ENV['AI_API_KEY'] ?? 'NOT_SET';
$model = $_ENV['AI_MODEL'] ?? 'NOT_SET';

echo "Environment Check:\n";
echo "AI_API_KEY: " . ($apiKey !== 'NOT_SET' ? substr($apiKey, 0, 5) . '...' : 'NOT_SET') . "\n";
echo "AI_MODEL: " . $model . "\n";
