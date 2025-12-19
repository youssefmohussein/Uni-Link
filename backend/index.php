<?php
/**
 * Uni-Link Backend - Main Entry Point
 * 
 * Clean OOP architecture with dependency injection
 */

// Load autoloader
require_once __DIR__ . '/config/autoload.php';

// CORS Headers for React frontend - MUST be before any output
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-USER-ID, Authorization, X-Requested-With");
header("Content-Type: application/json");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Start session for authentication
// Using default PHP session configuration for localhost
if (session_status() === PHP_SESSION_NONE) {
    // Only set httponly for security, let PHP handle the rest
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

// Error handling - return JSON errors
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $errstr,
        'file' => basename($errfile),
        'line' => $errline
    ]);
    exit;
});

set_exception_handler(function ($e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
});

// Load DI container and services
$container = require_once __DIR__ . '/config/services.php';

// Load routes
$routes = require_once __DIR__ . '/config/routes.php';

// Parse request
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove /backend/index.php or /backend from path
$requestUri = str_replace('/backend/index.php', '', $requestUri);
$requestUri = str_replace('/backend', '', $requestUri);

// Ensure path starts with /
if ($requestUri === '' || $requestUri[0] !== '/') {
    $requestUri = '/' . $requestUri;
}

// Log request for debugging
error_log("API Request: $requestMethod $requestUri");

// Match route
$matched = false;

foreach ($routes as $route => $handler) {
    list($method, $path) = explode(' ', $route, 2);

    if ($method === $requestMethod && $path === $requestUri) {
        list($controllerName, $methodName) = $handler;

        try {
            // Get controller from DI container
            $controller = $container->get($controllerName);

            if (method_exists($controller, $methodName)) {
                $controller->$methodName();
                $matched = true;
                break;
            } else {
                throw new Exception("Method {$methodName} not found in {$controllerName}");
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }
}

// Route not found
if (!$matched) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => 'Route not found',
        'requested' => "$requestMethod $requestUri",
        'available_routes' => array_keys($routes)
    ]);
}
