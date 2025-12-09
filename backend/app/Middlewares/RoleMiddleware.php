<?php
namespace App\Middlewares;

use App\Utils\ResponseHandler;
use App\Strategies\RoleAccess\AdminAccessStrategy;
use App\Strategies\RoleAccess\ProfessorAccessStrategy;
use App\Strategies\RoleAccess\StudentAccessStrategy;

/**
 * Role Middleware
 * 
 * Handles role-based access control using Strategy Pattern
 */
class RoleMiddleware {
    /**
     * Require specific role
     * 
     * @param string $requiredRole Required role name
     */
    public static function requireRole(string $requiredRole): void {
        AuthMiddleware::handle();
        
        $userRole = AuthMiddleware::getCurrentUserRole();
        
        if (strtolower($userRole) !== strtolower($requiredRole)) {
            ResponseHandler::error('Forbidden. Insufficient permissions.', 403);
            exit;
        }
    }
    
    /**
     * Require one of multiple roles
     * 
     * @param array $roles Allowed roles
     */
    public static function requireAnyRole(array $roles): void {
        AuthMiddleware::handle();
        
        $userRole = AuthMiddleware::getCurrentUserRole();
        
        $hasRole = false;
        foreach ($roles as $role) {
            if (strtolower($userRole) === strtolower($role)) {
                $hasRole = true;
                break;
            }
        }
        
        if (!$hasRole) {
            ResponseHandler::error('Forbidden. Insufficient permissions.', 403);
            exit;
        }
    }
    
    /**
     * Check resource access using Strategy Pattern
     * 
     * @param string $resource Resource identifier
     * @param array $context Additional context
     */
    public static function checkResourceAccess(string $resource, array $context = []): void {
        AuthMiddleware::handle();
        
        $userId = AuthMiddleware::getCurrentUserId();
        $userRole = AuthMiddleware::getCurrentUserRole();
        
        // Get appropriate strategy based on role
        $strategy = match($userRole) {
            'Admin' => new AdminAccessStrategy(),
            'Professor' => new ProfessorAccessStrategy(),
            'Student' => new StudentAccessStrategy(),
            default => null
        };
        
        if (!$strategy || !$strategy->canAccessResource($userId, $resource, $context)) {
            ResponseHandler::error('Forbidden. You cannot access this resource.', 403);
            exit;
        }
    }
}
