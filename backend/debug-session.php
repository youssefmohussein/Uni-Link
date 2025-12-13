<?php
/**
 * Session Debug Endpoint
 * Provides detailed information about session state
 */

// Load autoloader
require_once __DIR__ . '/config/autoload.php';

// CORS Headers
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Configure session (same as index.php)
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Collect debug info
$debugInfo = [
    'session_id' => session_id(),
    'session_status' => session_status(),
    'session_data' => $_SESSION ?? [],
    'cookie_params' => session_get_cookie_params(),
    'cookies_received' => $_COOKIE ?? [],
    'headers_sent' => headers_sent(),
    'session_save_path' => session_save_path(),
    'session_name' => session_name()
];

echo json_encode($debugInfo, JSON_PRETTY_PRINT);
