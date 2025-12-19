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
            
            // Insert into role-specific table based on user role
            switch ($sanitized['role']) {
                case 'STUDENT':
                    // Insert into students table
                    $this->studentRepo->create([
                        'user_id' => $userId,
                        'year' => (int)($data['year'] ?? 1),
                        'gpa' => $data['gpa'] ?? null,
                        'points' => 0,
                        'enrollment_date' => $data['enrollment_date'] ?? date('Y-m-d')
                    ]);
                    break;
                    
                case 'PROFESSOR':
                    // Insert into professors table
                    $this->professorRepo->create([
                        'user_id' => $userId,
                        'academic_rank' => $data['academic_rank'] ?? null,
                        'department' => $data['department'] ?? null,
                        'office_location' => $data['office_location'] ?? null,
                        'office_hours' => $data['office_hours'] ?? null
                    ]);
                    break;
                    
                case 'ADMIN':
                    // Insert into admins table
                    $this->adminRepo->create([
                        'user_id' => $userId,
                        'permissions' => $data['permissions'] ?? json_encode([])
                    ]);
                    break;
            }
            
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
            
            // Handle role change if role has changed
            if ($oldRole !== $newRole) {
                $this->changeRole($userId, $oldRole, $newRole, $data);
            } else {
                // Update role-specific table data even if role hasn't changed
                $this->updateRoleSpecificData($userId, $newRole, $data);
            }
            
            return $this->userRepo->find($userId);
        }, $this->userRepo);
    }
    
    /**
     * Update role-specific data without changing role
     * 
     * @param int $userId User ID
     * @param string $role Current role
     * @param array $data Additional data
     */
    private function updateRoleSpecificData(int $userId, string $role, array $data): void {
        switch ($role) {
            case 'STUDENT':
                // Find student record
                $student = $this->studentRepo->findOneBy('user_id', $userId);
                if ($student) {
                    $updateData = [];
                    if (isset($data['year'])) $updateData['year'] = (int)$data['year'];
                    if (isset($data['gpa'])) $updateData['gpa'] = $data['gpa'];
                    if (isset($data['enrollment_date'])) $updateData['enrollment_date'] = $data['enrollment_date'];
                    
                    if (!empty($updateData)) {
                        $this->studentRepo->update($student['student_id'], $updateData);
                    }
                }
                break;
                
            case 'PROFESSOR':
                // Find professor record
                $professor = $this->professorRepo->findOneBy('user_id', $userId);
                if ($professor) {
                    $updateData = [];
                    if (isset($data['academic_rank'])) $updateData['academic_rank'] = $data['academic_rank'];
                    if (isset($data['department'])) $updateData['department'] = $data['department'];
                    if (isset($data['office_location'])) $updateData['office_location'] = $data['office_location'];
                    if (isset($data['office_hours'])) $updateData['office_hours'] = $data['office_hours'];
                    
                    if (!empty($updateData)) {
                        $this->professorRepo->update($professor['professor_id'], $updateData);
                    }
                }
                break;
                
            case 'ADMIN':
                // Find admin record
                $admin = $this->adminRepo->findOneBy('user_id', $userId);
                if ($admin) {
                    if (isset($data['permissions'])) {
                        $this->adminRepo->update($admin['admin_id'], [
                            'permissions' => $data['permissions']
                        ]);
                    }
                }
                break;
        }
    }
    
    /**
     * Change user role
     * Deletes old role-specific record and creates new one
     * 
     * @param int $userId User ID
     * @param string $oldRole Old role
     * @param string $newRole New role
     * @param array $data Additional data
     */
    private function changeRole(int $userId, string $oldRole, string $newRole, array $data): void {
        // Delete old role-specific record
        switch ($oldRole) {
            case 'STUDENT':
                $student = $this->studentRepo->findOneBy('user_id', $userId);
                if ($student) {
                    $this->studentRepo->delete($student['student_id']);
                }
                break;
                
            case 'PROFESSOR':
                $professor = $this->professorRepo->findOneBy('user_id', $userId);
                if ($professor) {
                    $this->professorRepo->delete($professor['professor_id']);
                }
                break;
                
            case 'ADMIN':
                $admin = $this->adminRepo->findOneBy('user_id', $userId);
                if ($admin) {
                    $this->adminRepo->delete($admin['admin_id']);
                }
                break;
        }
        
        // Create new role-specific record
        switch ($newRole) {
            case 'STUDENT':
                $this->studentRepo->create([
                    'user_id' => $userId,
                    'year' => (int)($data['year'] ?? 1),
                    'gpa' => $data['gpa'] ?? null,
                    'points' => 0,
                    'enrollment_date' => $data['enrollment_date'] ?? date('Y-m-d')
                ]);
                break;
                
            case 'PROFESSOR':
                $this->professorRepo->create([
                    'user_id' => $userId,
                    'academic_rank' => $data['academic_rank'] ?? null,
                    'department' => $data['department'] ?? null,
                    'office_location' => $data['office_location'] ?? null,
                    'office_hours' => $data['office_hours'] ?? null
                ]);
                break;
                
            case 'ADMIN':
                $this->adminRepo->create([
                    'user_id' => $userId,
                    'permissions' => $data['permissions'] ?? json_encode([])
                ]);
                break;
        }
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
