<?php

namespace App\Helpers;

class Session
{
    // Start session if not already started
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Basic security - must be set BEFORE session_start
            if (!headers_sent()) {
                ini_set('session.cookie_httponly', '1');
                ini_set('session.cookie_path', '/');
            }
            
            session_start();
        }
    }
    
    // Regenerate session ID
    public static function regenerate()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }
    
    // Set session variable
    public static function set($key, $value)
    {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    // Get session variable
    public static function get($key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }
    
    // Remove session variable
    public static function remove($key)
    {
        self::start();
        unset($_SESSION[$key]);
    }
    
    // Destroy session
    public static function destroy()
    {
        self::start();
        $_SESSION = [];
        
        if (ini_get("session.use_cookies") && !headers_sent()) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        session_destroy();
    }
    
    // Set flash message
    public static function setFlash($key, $message)
    {
        self::set('_flash_' . $key, $message);
    }
    
    // Get and clear flash message
    public static function getFlash($key, $default = null)
    {
        $message = self::get('_flash_' . $key, $default);
        self::remove('_flash_' . $key);
        return $message;
    }
    
    // Check if flash message exists
    public static function hasFlash($key)
    {
        return isset($_SESSION['_flash_' . $key]);
    }
    
    // Rate limiting for login attempts
    public static function checkLoginAttempts($maxAttempts = 5, $lockoutTime = 900) // 15 minutes
    {
        $key = 'login_attempts';
        $attempts = self::get($key, []);
        
        // Remove expired attempts
        $now = time();
        $attempts = array_filter($attempts, function($time) use ($now, $lockoutTime) {
            return ($now - $time) < $lockoutTime;
        });
        
        // Check if user is locked out
        if (count($attempts) >= $maxAttempts) {
            $oldestAttempt = min($attempts);
            $timeLeft = $lockoutTime - ($now - $oldestAttempt);
            return [
                'allowed' => false,
                'time_left' => $timeLeft
            ];
        }
        
        // Add new attempt
        $attempts[] = $now;
        self::set($key, $attempts);
        
        return [
            'allowed' => true,
            'attempts' => count($attempts),
            'remaining' => $maxAttempts - count($attempts)
        ];
    }
}
