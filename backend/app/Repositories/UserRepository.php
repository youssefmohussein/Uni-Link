<?php
namespace App\Repositories;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Utils\Database;
use PDO;

/**
 * User Repository
 * 
 * Handles all database operations for users
 * Implements Repository Pattern for data access layer
 */
class UserRepository implements UserRepositoryInterface {
    protected PDO $db;
    protected string $table = 'Users';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Find user by ID
     * 
     * @param int $id User ID
     * @return array|null User data
     */
    public function find(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    
    /**
     * Find all users
     * 
     * @return array Array of users
     */
    public function findAll(): array {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY user_id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Create new user
     * 
     * @param array $data User data
     * @return int User ID
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (user_id, username, email, password, phone, profile_image, bio, job_title, role, faculty_id, major_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['user_id'],
            $data['username'],
            $data['email'],
            $data['password'],
            $data['phone'] ?? null,
            $data['profile_image'] ?? null,
            $data['bio'] ?? null,
            $data['job_title'] ?? null,
            $data['role'],
            $data['faculty_id'] ?? null,
            $data['major_id'] ?? null
        ]);
        
        return (int)$data['user_id'];
    }
    
    /**
     * Update user
     * 
     * @param int $id User ID
     * @param array $data Updated data
     * @return bool Success
     */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET username = ?, email = ?, password = ?, phone = ?, 
                profile_image = ?, bio = ?, job_title = ?, role = ?, 
                faculty_id = ?, major_id = ?
            WHERE user_id = ?
        ");
        
        return $stmt->execute([
            $data['username'],
            $data['email'],
            $data['password'],
            $data['phone'] ?? null,
            $data['profile_image'] ?? null,
            $data['bio'] ?? null,
            $data['job_title'] ?? null,
            $data['role'],
            $data['faculty_id'] ?? null,
            $data['major_id'] ?? null,
            $id
        ]);
    }
    
    /**
     * Delete user
     * 
     * @param int $id User ID
     * @return bool Success
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE user_id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Find user by email
     * 
     * @param string $email Email
     * @return array|null User data
     */
    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    
    /**
     * Find user by username
     * 
     * @param string $username Username
     * @return array|null User data
     */
    public function findByUsername(string $username): ?array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = ?");
        $stmt->execute([$username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    
    /**
     * Find user by email or username
     * 
     * @param string $identifier Email or username
     * @return array|null User data
     */
    public function findByEmailOrUsername(string $identifier): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE email = ? OR username = ? 
            LIMIT 1
        ");
        $stmt->execute([$identifier, $identifier]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    
    /**
     * Get user with role-specific data
     * 
     * @param int $id User ID
     * @return array|null Complete user data
     */
    public function findWithRoleData(int $id): ?array {
        $user = $this->find($id);
        if (!$user) {
            return null;
        }
        
        // Get role-specific data based on user role
        $roleData = match($user['role']) {
            'Admin' => $this->getAdminData($id),
            'Student' => $this->getStudentData($id),
            'Professor' => $this->getProfessorData($id),
            default => []
        };
        
        return array_merge($user, $roleData ?? []);
    }
    
    /**
     * Get admin-specific data
     */
    private function getAdminData(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM Admin WHERE admin_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    /**
     * Get student-specific data
     */
    private function getStudentData(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM Student WHERE student_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    /**
     * Get professor-specific data
     */
    private function getProfessorData(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM Professor WHERE professor_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction(): bool {
        return $this->db->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit(): bool {
        return $this->db->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback(): bool {
        return $this->db->rollBack();
    }
}
