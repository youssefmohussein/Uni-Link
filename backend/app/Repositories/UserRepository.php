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
class UserRepository extends BaseRepository implements UserRepositoryInterface {
    protected string $table = 'Users';
    protected string $primaryKey = 'user_id';
    
    // Constructor inherited from BaseRepository
    
    /**
     * Get user profile data with joins
     * 
     * @param int $userId User ID
     * @return array|null User profile data
     */
    public function getUserProfileData(int $userId): ?array {
        // All user data is in Users table, no need to join with separate role tables
        $sql = "
            SELECT 
                u.user_id, u.username, u.email, u.phone, u.profile_image,
                u.bio, u.job_title, u.role, u.faculty_id, u.major_id,
                f.faculty_name, m.major_name
            FROM Users u
            LEFT JOIN Faculty f ON u.faculty_id = f.faculty_id
            LEFT JOIN Major m ON u.major_id = m.major_id
            WHERE u.user_id = ?
        ";
        
        $result = $this->queryOne($sql, [$userId]);
        
        // Normalize role if user found
        if ($result && isset($result['role'])) {
            $result['role'] = $this->normalizeRole($result['role']);
        }
        
        return $result;
    }

    /**
     * Find user by ID
     * 
     * @param int $id User ID
     * @return array|null User data
     */
    public function find(int $id, bool $includeSoftDeleted = false): ?array {
        return parent::find($id, $includeSoftDeleted);
    }
    
    /**
     * Find all users
     * 
     * @return array Array of users
     */
    public function findAll(?int $limit = null, int $offset = 0, string $orderBy = ''): array {
        return parent::findAll($limit, $offset, $orderBy ?: 'user_id ASC');
    }
    
    /**
     * Create new user
     * 
     * @param array $data User data
     * @return int User ID
     */
    public function create(array $data): int {
        // ... (Existing create implementation but using $this->db from parent)
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
        // Note: Using custom update because BaseRepository::update builds dynamic query
        // but here we have specific fields potentially.
        // Actually, let's keep the existing custom SQL for safety with the specific fields
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
        return parent::delete($id);
    }
    
    /**
     * Find user by email
     * 
     * @param string $email Email
     * @return array|null User data
     */
    public function findByEmail(string $email): ?array {
        return $this->findOneBy('email', $email);
    }
    
    /**
     * Find user by username
     * 
     * @param string $username Username
     * @return array|null User data
     */
    public function findByUsername(string $username): ?array {
        return $this->findOneBy('username', $username);
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
     * All role information is stored in the Users table, no separate role tables needed
     * 
     * @param int $id User ID
     * @return array|null Complete user data
     */
    public function findWithRoleData(int $id): ?array {
        $user = $this->find($id);
        if (!$user) {
            return null;
        }
        
        // Normalize role from database (uppercase: ADMIN/PROFESSOR/STUDENT) to mixed case (Admin/Professor/Student)
        $dbRole = $user['role'] ?? '';
        $normalizedRole = $this->normalizeRole($dbRole);
        $user['role'] = $normalizedRole;
        
        // All role information is in the Users table, no need to query separate tables
        // Return user data with normalized role
        return $user;
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

    // Transaction methods inherited from BaseRepository
}
