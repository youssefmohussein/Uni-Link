<?php
namespace App\Controllers;

use App\Services\AuthService;
use App\Utils\ResponseHandler;

/**
 * Auth Controller
 * 
 * Handles HTTP requests for authentication
 * Uses dependency injection for services
 */
class AuthController {
    private AuthService $authService;
    
    /**
     * Constructor with dependency injection
     * 
     * @param AuthService $authService Authentication service
     */
    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }
    
    /**
     * Login endpoint
     * POST /api/auth/login
     */
    public function login(): void {
        try {
            $input = json_decode(file_get_contents("php://input"), true);
            
            if (!isset($input['identifier'], $input['password'])) {
                ResponseHandler::error('Missing credentials', 400);
                return;
            }
            
            $user = $this->authService->login($input['identifier'], $input['password']);
            
            // Determine redirect based on role
            $redirectMap = [
                'Student' => '/posts',
                'Professor' => '/professor',
                'Admin' => '/admin'
            ];
            
            $redirect = $redirectMap[$user->getRole()] ?? '/posts';
            
            ResponseHandler::success([
                'id' => $user->getUserId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'role' => $user->getRole(),
                'redirect' => $redirect
            ], 'Login successful');
            
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            ResponseHandler::error($e->getMessage(), $code);
        }
    }
    
    /**
     * Logout endpoint
     * POST /api/auth/logout
     */
    public function logout(): void {
        try {
            $this->authService->logout();
            ResponseHandler::success(null, 'Logged out successfully');
        } catch (\Exception $e) {
            ResponseHandler::error($e->getMessage(), 500);
        }
    }
    
    /**
     * Get current user endpoint
     * GET /api/auth/me
     * GET /check-session (legacy)
     */
    public function getCurrentUser(): void {
        try {
            $user = $this->authService->getCurrentUser();
            
            // Return format expected by frontend ProtectedRoute
            ResponseHandler::success([
                'authenticated' => $user !== null,
                'user' => $user
            ]);
            
        } catch (\Exception $e) {
            ResponseHandler::error($e->getMessage(), 500);
        }
    }
}
