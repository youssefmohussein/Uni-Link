<?php
namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\StudentRepository;
use App\Repositories\ProfessorRepository;
use App\Repositories\AdminRepository;

/**
 * User Service
 * 
 * Business logic for user management
 */
class UserService extends BaseService {
    private UserRepository $userRepo;
    private StudentRepository $studentRepo;
    private ProfessorRepository $professorRepo;
    private AdminRepository $adminRepo;
    
    public function __construct(
        UserRepository $userRepo,
        StudentRepository $studentRepo,
        ProfessorRepository $professorRepo,
        AdminRepository $adminRepo
    ) {
        $this->userRepo = $userRepo;
        $this->studentRepo = $studentRepo;
        $this->professorRepo = $professorRepo;
        $this->adminRepo = $adminRepo;
    }
    
    /**
     * Create new user
     * 
     * @param array $data User data
     * @return array Created user
     * @throws \Exception If validation fails
     */
    public function createUser(array $data): array {
        // Normalize role to uppercase for database storage (schema uses ENUM('ADMIN', 'PROFESSOR', 'STUDENT'))
        if (isset($data['role'])) {
            $data['role'] = $this->normalizeRoleForDatabase($data['role']);
        }
        
        // Validate
        $errors = $this->validate($data, [
            'user_id' => ['required', 'numeric'],
            'username' => ['required', 'min:3'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
            'role' => ['required', 'in:STUDENT,PROFESSOR,ADMIN']
        ]);
        
        if (!empty($errors)) {
            throw new \Exception($this->formatValidationErrors($errors), 400);
        }
        
        // Check if user ID already exists
        if ($this->userRepo->exists((int)$data['user_id'])) {
            throw new \Exception('User ID already exists', 400);
        }
        
        // Sanitize data
        $sanitized = $this->sanitizeData($data, ['username', 'email', 'bio', 'job_title']);
        
        // Hash password
        $sanitized['password'] = $this->hashPassword($data['password']);
        
        // Role-specific validation (use uppercase for comparison)
        if ($sanitized['role'] === 'STUDENT') {
            if (!isset($data['year'])) {
                throw new \Exception('Academic year is required for students', 400);
            }
            $year = (int)$data['year'];
            $gpa = $year == 1 ? 0.0 : ($data['gpa'] ?? null);
            
            if ($year != 1 && $gpa === null) {
                throw new \Exception('GPA is required for students not in year 1', 400);
            }
        }
        
        // Create user within transaction
        return $this->transaction(function() use ($sanitized, $data) {
            // Insert into Users table - all role information is stored here
            $userId = $this->userRepo->create([
                'user_id' => (int)$sanitized['user_id'],
                'username' => $sanitized['username'],
                'email' => $sanitized['email'],
                'password' => $sanitized['password'],
                'phone' => $data['phone'] ?? null,
                'profile_image' => $data['profile_image'] ?? null,
                'bio' => $sanitized['bio'] ?? null,
                'job_title' => $sanitized['job_title'] ?? null,
                'role' => $sanitized['role'], // Role stored in Users table (ADMIN, PROFESSOR, STUDENT)
                'faculty_id' => $data['faculty_id'] ?? null,
                'major_id' => $data['major_id'] ?? null
            ]);
            
            // No separate role tables - all role information is in Users table
            return $this->userRepo->find($userId);
        }, $this->userRepo);
    }
    
    /**
     * Update user
     * 
     * @param int $userId User ID
     * @param array $data Updated data
     * @return array Updated user
     * @throws \Exception If validation fails
     */
    public function updateUser(int $userId, array $data): array {
        // Get existing user
        $existingUser = $this->userRepo->find($userId);
        
        if (!$existingUser) {
            throw new \Exception('User not found', 404);
        }
        
        // Normalize roles: database stores uppercase, but we accept mixed case input
        $oldRole = strtoupper($existingUser['role'] ?? '');
        $newRole = isset($data['role']) ? $this->normalizeRoleForDatabase($data['role']) : $oldRole;
        
        // Validate role (must be uppercase for database)
        if (!in_array($newRole, ['STUDENT', 'PROFESSOR', 'ADMIN'])) {
            throw new \Exception('Invalid role. Must be one of: STUDENT, PROFESSOR, ADMIN', 400);
        }
        
        // Sanitize data
        $sanitized = $this->sanitizeData($data, ['username', 'email', 'bio', 'job_title']);
        
        // Prepare update data
        $updateData = [
            'username' => $sanitized['username'] ?? $existingUser['username'],
            'email' => $sanitized['email'] ?? $existingUser['email'],
            'phone' => $data['phone'] ?? $existingUser['phone'],
            'profile_image' => $data['profile_image'] ?? $existingUser['profile_image'],
            'bio' => $sanitized['bio'] ?? $existingUser['bio'],
            'job_title' => $sanitized['job_title'] ?? $existingUser['job_title'],
            'role' => $newRole,
            'faculty_id' => $data['faculty_id'] ?? $existingUser['faculty_id'],
            'major_id' => $data['major_id'] ?? $existingUser['major_id']
        ];
        
        // Update password if provided
        if (!empty($data['password'])) {
            $updateData['password'] = $this->hashPassword($data['password']);
        } else {
            $updateData['password'] = $existingUser['password'];
        }
        
        // Update within transaction
        return $this->transaction(function() use ($userId, $updateData, $oldRole, $newRole, $data) {
            // Update Users table
            $this->userRepo->update($userId, $updateData);
            
            // Role change is handled by updating the role field in Users table
            // No separate role tables to update
            
            return $this->userRepo->find($userId);
        }, $this->userRepo);
    }
    
    /**
     * Change user role
     * Role is stored in Users table, no separate role tables to update
     * 
     * @param int $userId User ID
     * @param string $oldRole Old role
     * @param string $newRole New role
     * @param array $data Additional data
     */
    private function changeRole(int $userId, string $oldRole, string $newRole, array $data): void {
        // Role change is handled by updating the role field in Users table
        // No separate role tables exist - all role information is in Users table
        // This method is kept for compatibility but does nothing
    }
    
    /**
     * Normalize role to uppercase for database storage
     * Accepts mixed case input (Admin/Professor/Student) and converts to uppercase (ADMIN/PROFESSOR/STUDENT)
     * 
     * @param string $role Role in any case
     * @return string Role in uppercase
     */
    private function normalizeRoleForDatabase(string $role): string {
        return match(strtoupper($role)) {
            'ADMIN', 'Admin', 'admin' => 'ADMIN',
            'PROFESSOR', 'Professor', 'professor' => 'PROFESSOR',
            'STUDENT', 'Student', 'student' => 'STUDENT',
            default => strtoupper($role) // Fallback: uppercase whatever was provided
        };
    }
    
    /**
     * Delete user
     * 
     * @param int $userId User ID
     * @return bool Success status
     */
    public function deleteUser(int $userId): bool {
        $user = $this->userRepo->find($userId);
        
        if (!$user) {
            throw new \Exception('User not found', 404);
        }
        
        return $this->transaction(function() use ($userId) {
            // No separate role tables - just delete from Users table
            return $this->userRepo->delete($userId);
        }, $this->userRepo);
    }
    
    /**
     * Get user profile with complete information
     * 
     * @param int $userId User ID
     * @return array User profile
     */
    public function getUserProfile(int $userId): array {
        // Use repository method instead of calling queryOne directly
        $user = $this->userRepo->getUserProfileData($userId);
        
        if (!$user) {
            throw new \Exception('User not found', 404);
        }
        
        return $user;
    }
    
    /**
     * Get all users
     * 
     * @param int|null $limit Limit
     * @param int $offset Offset
     * @return array Array of users
     */
    public function getAllUsers(?int $limit = null, int $offset = 0): array {
        $users = $this->userRepo->findAll($limit, $offset, 'user_id ASC');
        
        // Remove passwords
        foreach ($users as &$user) {
            unset($user['password']);
        }
        
        return $users;
    }
}
