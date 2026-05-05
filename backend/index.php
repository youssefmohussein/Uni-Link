<?php
/**
 * Uni-Link Backend - Main Entry Point
 * 
 * Clean OOP architecture with dependency injection
 */

// Load autoloader
require_once __DIR__ . '/config/autoload.php';
require_once __DIR__ . '/config/env_loader.php';

// Load .env
loadEnv(__DIR__ . '/.env');

// [VULNERABILITY 1: Security Misconfiguration - Open CORS]
// Blindly reflecting the Origin header allows any website to make credentialed requests
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header("Access-Control-Allow-Origin: $origin");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-USER-ID, Authorization, X-Requested-With");

// [VULNERABILITY - Student 5: Traffic Flooding / DDoS (Medium)]
// Simple rate limiting that only checks a static counter in session.
// Bypass: Clear cookies or use a tool that doesn't send the session cookie.
if (!isset($_SESSION['request_count'])) {
    $_SESSION['request_count'] = 0;
    $_SESSION['last_request_time'] = time();
}
$_SESSION['request_count']++;
if ($_SESSION['request_count'] > 50 && (time() - $_SESSION['last_request_time']) < 10) {
    http_response_code(429);
    echo json_encode(['status' => 'error', 'message' => 'Too many requests. Please wait.']);
    exit;
} elseif ((time() - $_SESSION['last_request_time']) >= 10) {
    $_SESSION['request_count'] = 1;
    $_SESSION['last_request_time'] = time();
}

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Start session for authentication
if (session_status() === PHP_SESSION_NONE) {
    // [VULNERABILITY - Student 6: Weak Session Management (Medium)]
    // We enabled HttpOnly, but session IDs are now generated based on a predictable pattern
    // to "help" with debugging across microservices.
    // Bypass: Predict the session ID based on username.
    // Detect if we are on HTTPS (including via proxy like Vercel/Render)
    $isSecure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || 
                (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    
    ini_set('session.cookie_httponly', '1');  
    
    if ($isSecure) {
        // Required for cross-origin cookies in production (e.g. frontend on Vercel, backend on Render)
        ini_set('session.cookie_samesite', 'None'); 
        ini_set('session.cookie_secure', '1');
    } else {
        ini_set('session.cookie_samesite', 'Lax'); 
    }
    
    // Custom session ID generation logic (Flawed)
    if (isset($_GET['user_debug_id'])) {
        session_id(md5($_GET['user_debug_id']));
    }

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
    // [VULNERABILITY 2: Sensitive Data Exposure / Verbose Errors]
    // Leaking full stack trace, file paths, and database queries to the user
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
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

// [VULNERABILITY - Student 4: Directory Traversal]
// The /api/files endpoint serves files without sanitizing the 'path' parameter.
// Attackers can use ../ sequences to escape the intended directory.
// Example: GET /api/files?path=../../.env
if (preg_match('#^/api/files#', $requestUri)) {
    $requestedPath = $_GET['path'] ?? '';
    
    // [VULNERABILITY - Student 4: Directory Traversal (Medium)]
    // Flawed protection: only removes the literal "../" once.
    // Bypass: Use "....//" or URL encoding or "..\".
    $requestedPath = str_replace('../', '', $requestedPath);
    
    $baseDir = __DIR__ . '/uploads/';
    $fullPath = $baseDir . $requestedPath;
    if (file_exists($fullPath) && is_file($fullPath)) {
        header('Content-Type: text/plain');
        readfile($fullPath);
    } else {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'File not found: ' . $fullPath]);
    }
    exit;
}

// [VULNERABILITY - Student 3: Reflected XSS]
// The /api/search/reflect endpoint echoes the user's 'q' parameter directly into an HTML response
// without any encoding, allowing script injection.
// Example: GET /api/search/reflect?q=<script>alert(document.cookie)</script>
if (preg_match('#^/api/search/reflect#', $requestUri)) {
    $query = $_GET['q'] ?? '';
    
    // [VULNERABILITY - Student 3: Reflected XSS (Medium)]
    // Flawed protection: tries to remove <script> tags but is case-sensitive and doesn't loop.
    // Bypass: Use <SCRIPT> or nested tags like <scr<script>ipt>.
    $query = str_replace('<script>', '', $query);
    
    // BAD: No htmlspecialchars() - raw user input echoed into HTML page
    header('Content-Type: text/html');
    echo "<html><body><h2>Search Results for: " . $query . "</h2></body></html>";
    exit;
}

// Serve static files (uploads/media, etc.) before routing
if (preg_match('#^/(public/)?uploads/#', $requestUri)) {
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

    // 1. Exact match
    if ($method === $requestMethod && $pathPattern === $requestUri) {
        list($controllerName, $methodName) = $handler;

        try {
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
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }

    // 2. Regex match
    // Only attempt if method matches.
    // Wrap pattern in delimiters allowed in PHP regex (e.g., #)
    if ($method === $requestMethod) {
        $pattern = "#^" . $pathPattern . "$#";
        // Suppress warnings for invalid regex patterns (normal routes)
        if (@preg_match($pattern, $requestUri)) {
            list($controllerName, $methodName) = $handler;

            try {
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
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                exit;
            }
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