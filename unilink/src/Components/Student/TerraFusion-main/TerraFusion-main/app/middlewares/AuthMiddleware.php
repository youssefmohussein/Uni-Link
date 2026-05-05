<?php

namespace App\Middlewares;

/**
 * Authentication Middleware
 * Checks if user is authenticated
 */
class AuthMiddleware
{
    /**
     * Handle middleware
     */
    public function handle(): bool
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header('Location: ' . url('login'));
            exit;
        }
        
        // Check session timeout (10 minutes for Admin/Staff)
        if (isset($_SESSION['last_activity'])) {
            $user = $_SESSION['user'] ?? null;
            $isAdminOrStaff = $user && ($user->role_name === 'admin' || $user->role_name === 'staff');
            
            if ($isAdminOrStaff && (time() - $_SESSION['last_activity'] > 600)) {
                // 10 minutes timeout
                session_destroy();
                header('Location: ' . url('login') . '?timeout=1');
                exit;
            }
        }
        
        $_SESSION['last_activity'] = time();
        return true;
    }
}

