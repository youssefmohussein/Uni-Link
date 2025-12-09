<?php
/**
 * Uni-Link Backend - Main Entry Point
 * 
 * Updated to work with React frontend and new OOP architecture
 */

// Load autoloader for new OOP classes
require_once __DIR__ . '/config/autoload.php';

// Load legacy controllers
require_once __DIR__ . '/config/legacy.php';

// CORS Headers for React frontend
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
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error handling - return JSON errors
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $errstr,
        'file' => basename($errfile),
        'line' => $errline
    ]);
    exit;
});

set_exception_handler(function($e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    ]);
    exit;
});

// Load DI container and services
try {
    $container = require_once __DIR__ . '/config/services.php';
} catch (Exception $e) {
    // Container not critical for legacy routes
    $container = null;
}

// Parse request
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove /backend/index.php from path
$requestUri = str_replace('/backend/index.php', '', $requestUri);
$requestUri = str_replace('/backend', '', $requestUri);

// Log request for debugging
error_log("API Request: $requestMethod $requestUri");

// Try new OOP routes first
if ($container) {
    $routes = require_once __DIR__ . '/config/routes.php';
    $routeKey = "$requestMethod $requestUri";
    
    foreach ($routes as $route => $handler) {
        list($method, $path) = explode(' ', $route, 2);
        
        if ($method === $requestMethod && $path === $requestUri) {
            list($controllerName, $methodName) = $handler;
            
            try {
                // Try DI container first
                try {
                    $controller = $container->get($controllerName);
                } catch (Exception $e) {
                    // Fall back to legacy controller
                    if (class_exists($controllerName)) {
                        $controller = new $controllerName();
                    } else {
                        throw new Exception("Controller {$controllerName} not found");
                    }
                }
                
                if (method_exists($controller, $methodName)) {
                    $controller->$methodName();
                    exit;
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
}

// Fall back to legacy routing system
require_once __DIR__ . '/routes/loginRoutes.php';
require_once __DIR__ . '/routes/userRoutes.php';
require_once __DIR__ . '/routes/studentRoutes.php';
require_once __DIR__ . '/routes/adminRoutes.php';
require_once __DIR__ . '/routes/facultyRoutes.php';
require_once __DIR__ . '/routes/majorRoutes.php';
require_once __DIR__ . '/routes/professorRoute.php';
require_once __DIR__ . '/routes/postRoutes.php';
require_once __DIR__ . '/routes/postMediaRoutes.php';
require_once __DIR__ . '/routes/commentRoutes.php';
require_once __DIR__ . '/routes/postInteractionRoutes.php';
require_once __DIR__ . '/routes/cvRoutes.php';
require_once __DIR__ . '/routes/projectRoutes.php';
require_once __DIR__ . '/routes/skillCategoryRoutes.php';
require_once __DIR__ . '/routes/skillRoutes.php';
require_once __DIR__ . '/routes/projectSkillRoutes.php';
require_once __DIR__ . '/routes/userSkillRoutes.php';
require_once __DIR__ . '/routes/announcementRoutes.php';
require_once __DIR__ . '/routes/projectReviewRoutes.php';
require_once __DIR__ . '/routes/dashboardRoutes.php';
require_once __DIR__ . '/routes/projectRoomRoutes.php';
require_once __DIR__ . '/routes/savedPostRoutes.php';

// Parse request for legacy routing
if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] !== '') {
    $request = $_SERVER['PATH_INFO'];
} else {
    $request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $request = str_replace('/backend/index.php', '', $request);
}

$method = $_SERVER['REQUEST_METHOD'];

// Try legacy routes
$matched = false;

if (registerLoginRoutes($request, $method)) {
    $matched = true;
} elseif (registerUserRoutes($request, $method)) {
    $matched = true;
} elseif (registerStudentRoutes($request, $method)) {
    $matched = true;
} elseif (registerAdminRoutes($request, $method)) {
    $matched = true;
} elseif (registerProfessorRoutes($request, $method)) {
    $matched = true;
} elseif (registerFacultyRoutes($request, $method)) {
    $matched = true;
} elseif (registerMajorRoutes($request, $method)) {
    $matched = true;
} elseif (registerPostRoutes($request, $method)) {
    $matched = true;
} elseif (registerPostMediaRoutes($request, $method)) {
    $matched = true;
} elseif (registerCommentRoutes($request, $method)) {
    $matched = true;
} elseif (registerPostInteractionRoutes($request, $method)) {
    $matched = true;
} elseif (registerCVRoutes($request, $method)) {
    $matched = true;
} elseif (registerProjectRoutes($request, $method)) {
    $matched = true;
} elseif (registerSkillCategoryRoutes($request, $method)) {
    $matched = true;
} elseif (registerSkillRoutes($request, $method)) {
    $matched = true;
} elseif (registerProjectSkillRoutes($request, $method)) {
    $matched = true;
} elseif (registerUserSkillRoutes($request, $method)) {
    $matched = true;
} elseif (registerAnnouncementRoutes($request, $method)) {
    $matched = true;
} elseif (registerProjectReviewRoutes($request, $method)) {
    $matched = true;
} elseif (registerDashboardRoutes($request, $method)) {
    $matched = true;
} elseif (registerProjectRoomRoutes($request, $method)) {
    $matched = true;
} elseif (registerSavedPostRoutes($request, $method)) {
    $matched = true;
}

// Route not found
if (!$matched) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => 'Route not found',
        'requested' => "$method $request"
    ]);
}
