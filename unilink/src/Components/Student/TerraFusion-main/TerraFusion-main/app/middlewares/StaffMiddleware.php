<?php

namespace App\Middlewares;

/**
 * Staff Authorization Middleware
 * Checks if user has staff or admin role
 */
class StaffMiddleware
{
    /**
     * Handle middleware
     */
    public function handle(): bool
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login'));
            exit;
        }
        
        $user = $_SESSION['user'] ?? null;
        $allowedRoles = ['admin', 'staff'];
        
        if (!$user || (!in_array($user->role_name, $allowedRoles) && !in_array($user->role_id, [1, 2]))) {
            http_response_code(403);
            die('Access denied. Staff privileges required.');
        }
        
        return true;
    }
}

