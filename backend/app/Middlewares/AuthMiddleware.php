<?php
namespace App\Middlewares;

use App\Utils\ResponseHandler;

/**
 * Enhanced Auth Middleware
 * 
 * Handles authentication verification for protected routes
 */
class AuthMiddleware {
    /**
     * Verify user is authenticated
     * Throws exception if not authenticated
     */
    public static function handle(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user'])) {
            ResponseHandler::error('Unauthorized. Please login.', 401);
            exit;
        }
    }
    
    /**
     * Get current user ID from session
     * 
     * @return int|null
     */
    public static function getCurrentUserId(): ?int {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user']['id'] ?? null;
    }
    
    /**
     * Get current user role from session
     * 
     * @return string|null
     */
    public static function getCurrentUserRole(): ?string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user']['role'] ?? null;
    }
    
    /**
     * Get current user data from session
     * 
     * @return array|null
     */
    public static function getCurrentUser(): ?array {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user'] ?? null;
    }
}
