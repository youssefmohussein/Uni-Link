<?php

namespace App\Middlewares;

/**
 * Admin Authorization Middleware
 * Checks if user has admin role
 */
class AdminMiddleware
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
        if (!$user || ($user->role_name !== 'admin' && $user->role_id != 1)) {
            http_response_code(403);
            die('Access denied. Admin privileges required.');
        }
        
        return true;
    }
}

