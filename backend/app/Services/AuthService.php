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
        if (!password_verify($password, $userData['password'])) {
            throw new \Exception('Incorrect password', 401);
        }
        
        // Get complete user data with role info
        $completeUserData = $this->userRepo->findWithRoleData($userData['user_id']);
        
        // Create appropriate user model
        $user = $this->createUserModel($completeUserData);
        
        // Start session
        $this->createSession($user);
        
        return $user;
    }
    
    /**
     * Logout user
     */
    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
    }
    
    /**
     * Get current authenticated user
     * 
     * @return array|null Session user data
     */
    public function getCurrentUser(): ?array {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
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
        return match($userData['role']) {
            'Admin' => new Admin($userData),
            'Student' => new Student($userData),
            'Professor' => new Professor($userData),
            default => throw new \Exception('Invalid user role')
        };
    }
    
    /**
     * Create session for user
     * 
     * @param User $user User model
     */
    private function createSession(User $user): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['user'] = [
            'id' => $user->getUserId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'role' => $user->getRole()
        ];
    }
}
