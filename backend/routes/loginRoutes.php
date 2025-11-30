<?php
require_once __DIR__ . '/../controllers/LoginController.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';

function registerLoginRoutes($request, $method) {
    // Login endpoint
    if ($request === '/login' && $method === 'POST') {
        $controller = new LoginController();
        $controller->login();
        return true;
    }
    
    // Logout endpoint
    if ($request === '/logout' && $method === 'POST') {
        AuthMiddleware::logout();
        return true;
    }
    
    // Check session endpoint
    if ($request === '/check-session' && $method === 'GET') {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (AuthMiddleware::isAuthenticated()) {
            $user = AuthMiddleware::getCurrentUser();
            echo json_encode([
                "status" => "success",
                "authenticated" => true,
                "user" => $user
            ]);
        } else {
            echo json_encode([
                "status" => "success",
                "authenticated" => false
            ]);
        }
        return true;
    }
    
    return false;
}
