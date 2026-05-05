<?php

namespace App\Controllers;

use App\Core\Database;
use App\Helpers\Security;
use App\Helpers\Session;
use PDO;
use Exception;

class AuthController
{
    // Roles hierarchy
    const ROLE_MANAGER = 4;
    const ROLE_CHEF_BOSS = 3;
    const ROLE_TABLE_MANAGER = 2;
    const ROLE_WAITER = 1;
    
    // Login attempt limits
    const MAX_LOGIN_ATTEMPTS = 5;
    const LOCKOUT_DURATION = 900; // 15 minutes in seconds

    public static function login($email, $password, $csrfToken = null)
    {
        $attempts = null; // Initialize attempts variable
        
        try {
            // Start secure session
            Session::start();
            
            // Verify CSRF token if provided (for form submissions)
            if ($csrfToken !== null) {
                Security::verifyCSRFToken($csrfToken);
            }
            
            // Skip login attempt checking for testing
            // $attempts = Session::checkLoginAttempts(
            //     self::MAX_LOGIN_ATTEMPTS, 
            //     self::LOCKOUT_DURATION
            // );
            
            // if (!$attempts['allowed']) {
            //     $minutes = ceil($attempts['time_left'] / 60);
            //     throw new Exception("Too many login attempts. Please try again in {$minutes} minutes.");
            // }
            
            // Validate input
            $email = Security::sanitize($email);
            $password = Security::sanitize($password);
            
            if (empty($email) || empty($password)) {
                throw new Exception('Email and password are required');
            }
            
            // Get user from database
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();
            
            error_log('LOGIN: user row = ' . print_r($user, true));
            
            // Verify password
            if (!$user || !password_verify($password, $user['password_hash'])) {
                error_log('LOGIN: password_verify failed for email: ' . $email);
                throw new Exception('Invalid email or password');
            }
            
            error_log('LOGIN: password_verify succeeded');
            
            // Check if password needs rehashing
            if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE users SET password_hash = :hash WHERE user_id = :id");
                $stmt->execute(['hash' => $newHash, 'id' => $user['user_id']]);
            }
            
            // Clear login attempts on successful login
            Session::remove('login_attempts');
            
        // Role ID mapping
        $roleMap = [
            'Manager' => self::ROLE_MANAGER,
            'Chef Boss' => self::ROLE_CHEF_BOSS,
            'Table Manager' => self::ROLE_TABLE_MANAGER,
            'Waiter' => self::ROLE_WAITER
        ];
        $roleId = $roleMap[$user['role']] ?? 1;

        // Set session data
        $sessionData = [
            'user_id' => $user['user_id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'] ?? '',
            'role' => $user['role'],
            'role_id' => $roleId,
            'last_login' => $user['last_login'] ?? null,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'CLI',
            'fingerprint' => self::generateSessionFingerprint()
        ];
            
            // Set session data
            foreach ($sessionData as $key => $value) {
                Session::set($key, $value);
            }
            
            // Update last login
            $stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE user_id = :id");
            $stmt->execute(['id' => $user['user_id']]);
            
            // Regenerate session ID to prevent session fixation
            Session::regenerate();
            
            return [
                'success' => true,
                'user' => [
                    'id' => $user['user_id'],
                    'email' => $user['email'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role']
                ]
            ];
            
        } catch (Exception $e) {
            // Log the error
            error_log('Login error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'attempts_remaining' => isset($attempts) ? (self::MAX_LOGIN_ATTEMPTS - $attempts['attempts']) : null
            ];
        }
    }

    public static function logout()
    {
        // Clear all session data
        Session::destroy();
        
        // Redirect to root index page
        if (!headers_sent()) {
            header('Location: ../../index.php');
            exit();
        }
    }

    public static function isLoggedIn()
    {
        // Check if user is logged in and session is still valid
        $userId = Session::get('user_id');
        $logFile = __DIR__ . '/../../public/debug.log';
        file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] IS LOGGED IN Check: user_id = " . ($userId ?: 'NULL') . "\n", FILE_APPEND);
        if (!$userId) {
            return false;
        }
        
        // Check for session hijacking
        // $currentFingerprint = self::generateSessionFingerprint();
        // $storedFingerprint = Session::get('fingerprint');
        
        // if ($currentFingerprint !== $storedFingerprint) {
        //     self::logout();
        //     return false;
        // }
        
        return true;
    }
    
    private static function generateSessionFingerprint()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'CLI';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        return hash('sha256', $userAgent . $ip);
    }

    public static function requireLogin($redirect = 'login.php')
    {
        $logFile = __DIR__ . '/../../public/debug.log';
        $isLoggedIn = self::isLoggedIn();
        file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] REQUIRE LOGIN Check: isLoggedIn = " . ($isLoggedIn ? 'TRUE' : 'FALSE') . " | session_id = " . session_id() . "\n", FILE_APPEND);
        if (!$isLoggedIn) {
            Session::setFlash('error', 'Please log in to access this page.');
            
            // Store the requested URL for redirecting after login
            if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_SERVER['REQUEST_URI'])) {
                Session::set('redirect_after_login', $_SERVER['REQUEST_URI']);
            }
            
            if (!headers_sent()) {
                header("Location: {$redirect}");
                exit();
            }
            
            // If headers already sent, output JavaScript redirect
            echo "<script>window.location.href = '{$redirect}';</script>";
            exit();
        }
        error_log('REQUIRE LOGIN: passed');
    }

    public static function checkAccess($requiredRoleName)
    {
        self::requireLogin();
        
        $roleMap = [
            'Manager' => self::ROLE_MANAGER,
            'Chef Boss' => self::ROLE_CHEF_BOSS,
            'Table Manager' => self::ROLE_TABLE_MANAGER,
            'Waiter' => self::ROLE_WAITER
        ];

        $userRole = Session::get('role');
        $userRoleLevel = $roleMap[$userRole] ?? 0;
        $requiredLevel = $roleMap[$requiredRoleName] ?? 100; // Default to high if not found

        if ($userRoleLevel < $requiredLevel) {
            if (Security::isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode([
                    'error' => 'Unauthorized',
                    'message' => 'You do not have permission to access this resource.'
                ]);
                http_response_code(403);
                exit();
            } else {
                // Log unauthorized access attempt
                error_log(sprintf(
                    'Unauthorized access attempt: User ID %s tried to access %s',
                    Session::get('user_id'),
                    $_SERVER['REQUEST_URI'] ?? ''
                ));
                
                // Show error page
                http_response_code(403);
                include __DIR__ . '/../Views/errors/403.php';
                exit();
            }
        }
    }
    
    public static function getRoleName($roleId = null) {
        if ($roleId === null) {
            return Session::get('role');
        }
        
        $roles = [
            self::ROLE_MANAGER => 'Manager',
            self::ROLE_CHEF_BOSS => 'Chef Boss',
            self::ROLE_TABLE_MANAGER => 'Table Manager',
            self::ROLE_WAITER => 'Waiter'
        ];
        
        return $roles[$roleId] ?? 'Staff';
    }
    
    public static function getAllRoles()
    {
        return [
            self::ROLE_MANAGER => 'Manager',
            self::ROLE_CHEF_BOSS => 'Chef Boss',
            self::ROLE_TABLE_MANAGER => 'Table Manager',
            self::ROLE_WAITER => 'Waiter'
        ];
    }
}
