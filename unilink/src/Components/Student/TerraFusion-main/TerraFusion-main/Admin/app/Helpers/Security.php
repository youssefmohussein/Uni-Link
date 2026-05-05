<?php

namespace App\Helpers;

class Security
{
    // Generate and store CSRF token
    public static function generateCSRFToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Verify CSRF token
    public static function verifyCSRFToken($token)
    {
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            throw new \Exception('Invalid CSRF token');
        }
        return true;
    }

    // Check if request is AJAX
    public static function isAjaxRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    // Sanitize input
    public static function sanitize($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    // Check password strength
    public static function isPasswordStrong($password)
    {
        $minLength = 8;
        $hasUppercase = preg_match('/[A-Z]/', $password);
        $hasLowercase = preg_match('/[a-z]/', $password);
        $hasNumber = preg_match('/\d/', $password);
        $hasSpecialChar = preg_match('/[^A-Za-z0-9]/', $password);
        
        return strlen($password) >= $minLength && 
               $hasUppercase && 
               $hasLowercase && 
               $hasNumber && 
               $hasSpecialChar;
    }

    // Generate random string
    public static function generateRandomString($length = 32)
    {
        return bin2hex(random_bytes($length));
    }
}
