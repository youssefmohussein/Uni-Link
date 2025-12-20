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
class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected string $table = 'users';
    protected string $primaryKey = 'user_id';

    // Constructor inherited from BaseRepository

    /**
     * Get user profile data with joins
     * 
     * @param int $userId User ID
     * @return array|null User profile data
     */
    public function getUserProfileData(int $userId): ?array
    {
        // All user data is in users table, no need to join with separate role tables
        $sql = "
            SELECT 
                u.user_id, u.username, u.email, u.phone, u.profile_picture as profile_image,
                u.bio, u.role, u.faculty_id, u.major_id,
                f.name as faculty_name, m.name as major_name
            FROM users u
            LEFT JOIN faculties f ON u.faculty_id = f.faculty_id
            LEFT JOIN majors m ON u.major_id = m.major_id
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
    public function find(int $id, bool $includeSoftDeleted = false): ?array
    {
        return parent::find($id, $includeSoftDeleted);
    }

    /**
     * Find all users
     * 
     * @return array Array of users
     */
    public function findAll(?int $limit = null, int $offset = 0, string $orderBy = ''): array
    {
        return parent::findAll($limit, $offset, $orderBy ?: 'user_id ASC');
    }

    /**
     * Find users by role with pagination
     * Returns user_id aliased as role-specific ID (admin_id, student_id, professor_id)
     * 
     * @param string $role Role (case insensitive)
     * @param int|null $limit Limit
     * @param int $offset Offset
     * @return array Array of users with that role and role-specific data
     */
    public function findByRole(string $role, ?int $limit = null, int $offset = 0): array
    {
        // Normalize role to uppercase for database query
        $dbRole = match (strtoupper($role)) {
            'ADMIN', 'PROFESSOR', 'STUDENT' => strtoupper($role),
            default => $role
        };

        // Build query based on role to alias user_id as role-specific ID
        switch ($dbRole) {
            case 'ADMIN':
                $sql = "
                    SELECT 
                        u.user_id,
                        u.user_id as admin_id,
                        u.username,
                        u.email,
                        u.phone,
                        u.role,
                        u.faculty_id,
                        u.major_id,
                        u.profile_picture,
                        u.bio,
                        u.created_at,
                        u.updated_at,
                        a.permissions,
                        'Active' as status
                    FROM users u
                    LEFT JOIN admins a ON u.user_id = a.user_id
                    WHERE u.role = ?
                    ORDER BY u.user_id ASC
                ";
                break;

            case 'STUDENT':
                $sql = "
                    SELECT 
                        u.user_id,
                        u.user_id as student_id,
                        u.username,
                        u.email,
                        u.phone,
                        u.role,
                        u.faculty_id,
                        u.major_id,
                        u.profile_picture,
                        u.bio,
                        u.created_at,
                        u.updated_at,
                        s.year,
                        s.gpa,
                        s.points,
                        s.enrollment_date,
                        f.name as faculty_name,
                        m.name as major_name
                    FROM users u
                    LEFT JOIN students s ON u.user_id = s.user_id
                    LEFT JOIN faculties f ON u.faculty_id = f.faculty_id
                    LEFT JOIN majors m ON u.major_id = m.major_id
                    WHERE u.role = ?
                    ORDER BY u.user_id ASC
                ";
                break;

            case 'PROFESSOR':
                $sql = "
                    SELECT 
                        u.user_id,
                        u.user_id as professor_id,
                        u.username,
                        u.email,
                        u.phone,
                        u.role,
                        u.faculty_id,
                        u.major_id,
                        u.profile_picture,
                        u.bio,
                        u.created_at,
                        u.updated_at,
                        p.academic_rank,
                        p.department,
                        p.office_location,
                        p.office_hours,
                        f.name as faculty_name,
                        m.name as major_name
                    FROM users u
                    LEFT JOIN professors p ON u.user_id = p.user_id
                    LEFT JOIN faculties f ON u.faculty_id = f.faculty_id
                    LEFT JOIN majors m ON u.major_id = m.major_id
                    WHERE u.role = ?
                    ORDER BY u.user_id ASC
                ";
                break;

            default:
                // Fallback to simple query
                return $this->findWhere(['role' => $dbRole], 'user_id ASC', $limit, $offset);
        }

        // Add pagination
        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $params = [$dbRole, $limit, $offset];
        } else {
            $params = [$dbRole];
        }

        return $this->query($sql, $params);
    }


    /**
     * Create new user
     * 
     * @param array $data User data
     * @return int User ID
     */
    public function create(array $data): int
    {
        // Use parent create method which handles the table correctly
        // Map password to password_hash if needed
        if (isset($data['password']) && !isset($data['password_hash'])) {
            $data['password_hash'] = $data['password'];
            unset($data['password']);
        }

        // Map profile_image to profile_picture if needed
        if (isset($data['profile_image']) && !isset($data['profile_picture'])) {
            $data['profile_picture'] = $data['profile_image'];
            unset($data['profile_image']);
        }

        // Remove job_title as it doesn't exist in schema
        unset($data['job_title']);

        return parent::create($data);
    }

    /**
     * Update user
     * 
     * @param int $id User ID
     * @param array $data Updated data
     * @return bool Success
     */
    public function update(int $id, array $data): bool
    {
        // Map password to password_hash if needed
        if (isset($data['password']) && !isset($data['password_hash'])) {
            $data['password_hash'] = $data['password'];
            unset($data['password']);
        }

        // Map profile_image to profile_picture if needed
        if (isset($data['profile_image']) && !isset($data['profile_picture'])) {
            $data['profile_picture'] = $data['profile_image'];
            unset($data['profile_image']);
        }

        // Remove job_title as it doesn't exist in schema
        unset($data['job_title']);

        // Use parent update method
        return parent::update($id, $data);
    }

    /**
     * Delete user
     * 
     * @param int $id User ID
     * @return bool Success
     */
    public function delete(int $id): bool
    {
        return parent::delete($id);
    }

    /**
     * Find user by email
     * 
     * @param string $email Email
     * @return array|null User data
     */
    public function findByEmail(string $email): ?array
    {
        return $this->findOneBy('email', $email);
    }

    /**
     * Find user by username
     * 
     * @param string $username Username
     * @return array|null User data
     */
    public function findByUsername(string $username): ?array
    {
        return $this->findOneBy('username', $username);
    }

    /**
     * Find user by email or username
     * 
     * @param string $identifier Email or username
     * @return array|null User data
     */
    public function findByEmailOrUsername(string $identifier): ?array
    {
        try {
            // Trim whitespace from identifier
            $identifier = trim($identifier);

            // Log the search attempt
            error_log("UserRepository: Searching for user with identifier: '{$identifier}'");

            $stmt = $this->db->prepare("
                SELECT * FROM {$this->table} 
                WHERE email = ? OR username = ? 
                LIMIT 1
            ");
            $stmt->execute([$identifier, $identifier]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                error_log("UserRepository: User found - ID: {$result['user_id']}, Username: {$result['username']}, Email: {$result['email']}, Role: {$result['role']}");
            } else {
                error_log("UserRepository: No user found with identifier: '{$identifier}'");

                // Additional debug: Check if any users exist
                $countStmt = $this->db->prepare("SELECT COUNT(*) as count FROM {$this->table}");
                $countStmt->execute();
                $count = $countStmt->fetch(PDO::FETCH_ASSOC);
                error_log("UserRepository: Total users in database: {$count['count']}");
            }

            return $result ?: null;
        } catch (\PDOException $e) {
            error_log("UserRepository: Database error in findByEmailOrUsername: " . $e->getMessage());
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    /**
     * Get user with role-specific data
     * All role information is stored in the Users table, no separate role tables needed
     * 
     * @param int $id User ID
     * @return array|null Complete user data
     */
    public function findWithRoleData(int $id): ?array
    {
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
    private function normalizeRole(string $role): string
    {
        return match (strtoupper($role)) {
            'ADMIN' => 'ADMIN',
            'PROFESSOR' => 'PROFESSOR',
            'STUDENT' => 'STUDENT',
            default => strtoupper($role) // Return as uppercase if not recognized
        };
    }

    /**
     * Get weekly user activity (registrations per day)
     * 
     * @return array Weekly activity data
     */
    public function getWeeklyActivity(): array
    {
        return $this->query("
            SELECT DATE(created_at) as date, COUNT(*) as count
            FROM {$this->table}
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
    }

    /**
     * Get faculty distribution of students
     * 
     * @return array Faculty distribution
     */
    public function getFacultyDistribution(): array
    {
        return $this->query("
            SELECT f.name as faculty_name, COUNT(DISTINCT u.user_id) as student_count
            FROM faculties f
            LEFT JOIN users u ON f.faculty_id = u.faculty_id AND u.role = 'STUDENT'
            GROUP BY f.faculty_id, f.name
            ORDER BY student_count DESC
            LIMIT 5
        ");
    }

    // Transaction methods inherited from BaseRepository
}
