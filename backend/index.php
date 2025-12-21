<?php
/**
 * Uni-Link Backend - Main Entry Point
 * 
 * Clean OOP architecture with dependency injection
 */

// Load autoloader
require_once __DIR__ . '/config/autoload.php';

// CORS Headers for React frontend - MUST be before any output
// Note: Content-Type will be set per request (JSON for API, appropriate type for static files)
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-USER-ID, Authorization, X-Requested-With");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Start session for authentication
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');

    // Set custom error log for debugging
    $logDir = __DIR__ . '/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0777, true);
    }
    if (is_dir($logDir)) {
        ini_set('log_errors', '1');
        ini_set('error_log', $logDir . '/debug.log');
    }

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

// Parse request FIRST to check for static files
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove /backend/index.php or /backend from path
// Handle both /backend/uploads/... and /uploads/... cases
$requestUri = $path;
if (strpos($requestUri, '/backend/') === 0) {
    $requestUri = substr($requestUri, 8); // Remove '/backend/'
} elseif (strpos($requestUri, '/backend') === 0) {
    $requestUri = substr($requestUri, 7); // Remove '/backend'
}
$requestUri = '/' . ltrim($requestUri, '/'); // Ensure it starts with /

// Serve static files (uploads/media, etc.) before routing
if (preg_match('#^/uploads/#', $requestUri)) {
    // Ensure requestUri starts with / for path construction
    $normalizedUri = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ltrim($requestUri, '/'));
    $filePath = __DIR__ . DIRECTORY_SEPARATOR . $normalizedUri;

    // Debug logging
    $exists = file_exists($filePath);
    $isFile = $exists && is_file($filePath);
    error_log("Static file request - URI: $requestUri, Normalized: $normalizedUri, Path: $filePath, Exists: " . ($exists ? 'yes' : 'no') . ", IsFile: " . ($isFile ? 'yes' : 'no'));

    if ($exists && $isFile) {
        // Check if file is readable
        if (!is_readable($filePath)) {
            error_log("File exists but is not readable: $filePath");
            http_response_code(403);
            header("Content-Type: application/json");
            echo json_encode([
                'status' => 'error',
                'message' => 'File is not readable: ' . $requestUri
            ]);
            exit;
        }

        // Determine MIME type
        $mimeType = mime_content_type($filePath);
        if (!$mimeType) {
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'mp4' => 'video/mp4',
                'webm' => 'video/webm',
                'pdf' => 'application/pdf'
            ];
            $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
        }

        // Clear any previous output
        if (ob_get_level()) {
            ob_clean();
        }

        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: public, max-age=31536000'); // Cache for 1 year

        // Output the file
        readfile($filePath);
        exit;
    } else {
        // File not found - return 404 JSON instead of continuing to route
        $debugInfo = [
            'request_uri' => $requestUri,
            'normalized_uri' => $normalizedUri,
            'file_path' => $filePath,
            'file_exists' => file_exists($filePath),
            'is_file' => is_file($filePath),
            'is_dir' => is_dir($filePath),
            'parent_dir_exists' => file_exists(dirname($filePath))
        ];
        error_log("Static file not found: " . json_encode($debugInfo));

        http_response_code(404);
        header("Content-Type: application/json");
        echo json_encode([
            'status' => 'error',
            'message' => 'File not found: ' . $requestUri,
            'debug' => $debugInfo
        ]);
        exit;
    }
}

// Load DI container and services
$container = require_once __DIR__ . '/config/services.php';

// Set default Content-Type for API responses (only if not already set by static file handler)
if (!headers_sent()) {
    header("Content-Type: application/json");
}

// Load routes
$routes = require_once __DIR__ . '/config/routes.php';

// Ensure path starts with /
if ($requestUri === '' || $requestUri[0] !== '/') {
    $requestUri = '/' . $requestUri;
}

// Match route
$matched = false;

foreach ($routes as $route => $handler) {
    list($method, $pathPattern) = explode(' ', $route, 2);

    if ($method === $requestMethod && $pathPattern === $requestUri) {
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
            http_response_code($e->getCode() ?: 500);
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
        'message' => 'Route not found: ' . "$requestMethod $requestUri"
    ]);
}