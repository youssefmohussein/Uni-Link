<?php
namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\Admin;
use App\Models\Student;
use App\Models\Professor;
use App\Models\User;

/**
 * Auth Service
 * 
 * Handles authentication logic
 * Business logic layer - uses repositories for data access
 */
class AuthService {
    private UserRepository $userRepo;
    
    /**
     * Constructor with dependency injection
     * 
     * @param UserRepository $userRepo User repository
     */
    public function __construct(UserRepository $userRepo) {
        $this->userRepo = $userRepo;
    }
    
    /**
     * Login user
     * 
     * @param string $identifier Email or username
     * @param string $password Password
     * @return User User model instance
     * @throws \Exception If login fails
     */
    public function login(string $identifier, string $password): User {
        // Find user
        $userData = $this->userRepo->findByEmailOrUsername($identifier);
        
        if (!$userData) {
            throw new \Exception('Account not found', 404);
        }
        
        // Verify password
        if (!password_verify($password, $userData['password_hash'])) {
            throw new \Exception('Incorrect password', 401);
        }
        
        // Get complete user data with role info
        $completeUserData = $this->userRepo->findWithRoleData($userData['user_id']);
        
        // Debug: Log role from database
        error_log("AuthService - User ID: {$userData['user_id']}, DB Role: '{$userData['role']}', Normalized Role: '{$completeUserData['role']}'");
        
        // Create appropriate user model
        $user = $this->createUserModel($completeUserData);
        
        // Debug: Log role from model
        error_log("AuthService - Model Role: '{$user->getRole()}'");
        
        // Start session
        $this->createSession($user);
        
        // Debug: Log session creation
        error_log("Session created for user: " . $user->getUserId() . ", Session ID: " . session_id());
        error_log("Session data: " . json_encode($_SESSION));
        
        return $user;
    }
    
    /**
     * Logout user
     */
    public function logout(): void {
        session_unset();
        session_destroy();
    }
    
    /**
     * Get current authenticated user
     * 
     * @return array|null Session user data
     */
    public function getCurrentUser(): ?array {
        // Debug: Log session check
        error_log("Checking session, Session ID: " . session_id());
        error_log("Session data: " . json_encode($_SESSION));
        
        return $_SESSION['user'] ?? null;
    }
    
    /**
     * Check if user is authenticated
     * 
     * @return bool
     */
    public function isAuthenticated(): bool {
        return $this->getCurrentUser() !== null;
    }
    
    /**
     * Get current user ID
     * 
     * @return int|null
     */
    public function getCurrentUserId(): ?int {
        $user = $this->getCurrentUser();
        return $user['id'] ?? null;
    }
    
    /**
     * Create user model from data
     * Demonstrates polymorphism
     * 
     * @param array $userData User data
     * @return User User model instance
     */
    private function createUserModel(array $userData): User {
        // Normalize role from database (uppercase: ADMIN/PROFESSOR/STUDENT) to mixed case (Admin/Professor/Student)
        $dbRole = $userData['role'] ?? '';
        $normalizedRole = $this->normalizeRole($dbRole);
        $userData['role'] = $normalizedRole;
        
        return match($normalizedRole) {
            'Admin' => new Admin($userData),
            'Student' => new Student($userData),
            'Professor' => new Professor($userData),
            default => throw new \Exception('Invalid user role: ' . $dbRole . ' (expected ADMIN, PROFESSOR, or STUDENT)')
        };
    }
    
    /**
     * Normalize role from database format (uppercase) to code format (mixed case)
     * 
     * @param string $role Role from database
     * @return string Normalized role
     */
    private function normalizeRole(string $role): string {
        return match(strtoupper($role)) {
            'ADMIN' => 'Admin',
            'PROFESSOR' => 'Professor',
            'STUDENT' => 'Student',
            default => $role // Return as-is if not recognized
        };
    }
    
    /**
     * Create session for user
     * 
     * @param User $user User model
     */
    private function createSession(User $user): void {
        $_SESSION['user'] = [
            'id' => $user->getUserId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'role' => $user->getRole()
        ];
    }
}
