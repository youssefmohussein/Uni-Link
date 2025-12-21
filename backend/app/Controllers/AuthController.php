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
class AuthController
{
    private AuthService $authService;

    /**
     * Constructor with dependency injection
     * 
     * @param AuthService $authService Authentication service
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Login endpoint
     * POST /api/auth/login
     */
    public function login(): void
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (!isset($input['identifier'], $input['password'])) {
                ResponseHandler::error('Missing credentials', 400);
                return;
            }

            $user = $this->authService->login($input['identifier'], $input['password']);

            // Determine redirect based on role
            // Get role from user model (should be normalized to mixed case: Admin, Professor, Student)
            $userRole = $user->getRole();

            // Normalize to uppercase for comparison (handle any case variations)
            $roleUpper = strtoupper(trim($userRole ?? ''));

            // Redirect map based on role - ADMIN goes to /admin/dashboard, PROFESSOR to /professor, STUDENT to /posts
            // Get redirect URL - explicitly check each role
            if ($roleUpper === 'ADMIN') {
                $redirect = '/admin/dashboard';
            } elseif ($roleUpper === 'PROFESSOR') {
                $redirect = '/professor';
            } elseif ($roleUpper === 'STUDENT') {
                $redirect = '/posts';
            } else {
                // Default fallback
                $redirect = '/posts';
                error_log("WARNING: Unknown role '{$userRole}' (uppercase: '{$roleUpper}'), defaulting to /posts");
            }

            // Debug logging
            error_log("Login redirect - User ID: {$user->getUserId()}, Role: '{$userRole}', Uppercase: '{$roleUpper}', Redirect: '{$redirect}'");

            // Get final role value for response
            $finalRole = $user->getRole();

            ResponseHandler::success([
                'id' => $user->getUserId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'role' => $finalRole,
                'profile_image' => $user->getProfileImage(),
                'redirect' => $redirect
            ], 'Login successful');

        } catch (\Exception $e) {
            // Ensure code is always an integer (getCode() can return string, int, or 0)
            $exceptionCode = $e->getCode();
            if (is_numeric($exceptionCode) && $exceptionCode > 0) {
                $code = (int) $exceptionCode;
            } else {
                $code = 500; // Default to 500 if code is invalid, 0, or non-numeric
            }
            ResponseHandler::error($e->getMessage(), $code);
        }
    }

    /**
     * Logout endpoint
     * POST /api/auth/logout
     */
    public function logout(): void
    {
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
    public function getCurrentUser(): void
    {
        try {
            $user = $this->authService->getCurrentUser();

            // Debug logging
            error_log("getCurrentUser - User data: " . json_encode($user));

            // Return format expected by frontend ProtectedRoute
            // ResponseHandler::success wraps data in 'data' key, so we need to check the structure
            $responseData = [
                'authenticated' => $user !== null,
                'user' => $user
            ];

            error_log("getCurrentUser - Response data: " . json_encode($responseData));

            ResponseHandler::success($responseData);

        } catch (\Exception $e) {
            ResponseHandler::error($e->getMessage(), 500);
        }
    }
}
