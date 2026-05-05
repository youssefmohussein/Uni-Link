<?php

namespace App\Middlewares;

/**
 * CSRF Protection Middleware
 * Validates CSRF tokens on POST requests
 */
class CsrfMiddleware
{
    /**
     * Handle middleware
     */
    public function handle(): bool
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            
            if (!validate_csrf($token)) {
                http_response_code(403);
                die('Invalid CSRF token. Please refresh the page and try again.');
            }
        }
        
        return true;
    }
}

