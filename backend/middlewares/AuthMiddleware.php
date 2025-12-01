<?php
require_once __DIR__ . '/../utils/DbConnection.php';

class AuthMiddleware
{
    /**
     * Check if user is authenticated
     */
    public static function isAuthenticated()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['user']) && isset($_SESSION['user']['id']);
    }

    /**
     * Require authentication - returns 401 if not authenticated
     */
    public static function requireAuth()
    {
        if (!self::isAuthenticated()) {
            http_response_code(401);
            echo json_encode([
                "status" => "error",
                "message" => "Unauthorized. Please login."
            ]);
            exit;
        }
    }

    /**
     * Check if user has required role
     */
    public static function hasRole($requiredRole)
    {
        if (!self::isAuthenticated()) {
            return false;
        }

        $userRole = $_SESSION['user']['role'] ?? null;
        return strtolower($userRole) === strtolower($requiredRole);
    }

    /**
     * Require specific role - returns 403 if user doesn't have role
     */
    public static function requireRole($requiredRole)
    {
        self::requireAuth();

        if (!self::hasRole($requiredRole)) {
            http_response_code(403);
            echo json_encode([
                "status" => "error",
                "message" => "Forbidden. You don't have permission to access this resource."
            ]);
            exit;
        }
    }

    /**
     * Require one of multiple roles
     */
    public static function requireAnyRole($roles)
    {
        self::requireAuth();

        $userRole = strtolower($_SESSION['user']['role'] ?? '');
        $hasRole = false;

        foreach ($roles as $role) {
            if (strtolower($role) === $userRole) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            http_response_code(403);
            echo json_encode([
                "status" => "error",
                "message" => "Forbidden. You don't have permission to access this resource."
            ]);
            exit;
        }
    }

    /**
     * Get current user data from session
     */
    public static function getCurrentUser()
    {
        if (!self::isAuthenticated()) {
            return null;
        }

        return $_SESSION['user'];
    }

    /**
     * Logout user
     */
    public static function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_unset();
        session_destroy();
        
        echo json_encode([
            "status" => "success",
            "message" => "Logged out successfully"
        ]);
    }
}
