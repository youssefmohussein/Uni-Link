<?php

namespace App\Middlewares;

/**
 * Guest Middleware
 * Redirects authenticated users away from guest-only pages
 */
class GuestMiddleware
{
    /**
     * Handle middleware
     */
    public function handle(): bool
    {
        if (isset($_SESSION['user_id'])) {
            $user = $_SESSION['user'] ?? null;
            
            if ($user) {
                if ($user->role_name === 'admin' || $user->role_id == 1) {
                    redirect(url('admin/dashboard'));
                } elseif ($user->role_name === 'staff' || $user->role_id == 2) {
                    redirect(url('staff/dashboard'));
                } else {
                    redirect(url('customer/menu'));
                }
            }
        }
        
        return true;
    }
}

