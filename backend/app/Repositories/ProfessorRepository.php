<?php
namespace App\Repositories;

/**
 * Professor Repository
 * 
 * Data access layer for Professor entity
 */
class ProfessorRepository extends BaseRepository
{
    protected string $table = 'professors';
    protected string $primaryKey = 'user_id';

    /**
     * Get professor with user information
     * 
     * @param int $userId User/Professor ID
     * @return array|null Professor with user data
     */
    public function getWithUserInfo(int $userId): ?array
    {
        $sql = "
            SELECT p.*, u.username, u.email, u.phone, u.profile_picture as profile_image, u.bio,
                   f.name as faculty_name, m.name as major_name
            FROM professors p
            JOIN users u ON p.user_id = u.user_id
            LEFT JOIN faculties f ON u.faculty_id = f.faculty_id
            LEFT JOIN majors m ON u.major_id = m.major_id
            WHERE p.user_id = ?
        ";
        return $this->queryOne($sql, [$userId]);
    }

    /**
     * Get all professors with user information
     * 
     * @return array Array of professors
     */
    public function getAllWithUserInfo(): array
    {
        $sql = "SELECT 
                    u.user_id, u.username, u.email, u.phone, u.profile_picture, u.role,
                    p.*,
                    f.name as faculty_name, f.faculty_id,
                    m.name as major_name, m.major_id
                FROM users u
                LEFT JOIN professors p ON u.user_id = p.user_id
                LEFT JOIN faculties f ON u.faculty_id = f.faculty_id
                LEFT JOIN majors m ON u.major_id = m.major_id
                WHERE u.role = 'PROFESSOR'
                ORDER BY u.user_id DESC";

        return $this->query($sql);
    }

    /**
     * Find professors by department/faculty
     * 
     * @param int $facultyId Faculty ID
     * @return array Array of professors
     */
    public function findByFaculty(int $facultyId): array
    {
        $sql = "SELECT 
                    u.user_id, u.username, u.email, u.profile_picture, u.role,
                    p.*,
                    f.name as faculty_name
                FROM users u
                INNER JOIN professors p ON u.user_id = p.user_id
                LEFT JOIN faculties f ON u.faculty_id = f.faculty_id
                WHERE u.faculty_id = ?
                ORDER BY u.username ASC";

        return $this->query($sql, [$facultyId]);
    }

    /**
     * Get supervised projects count
     * 
     * @param int $professorId Professor ID (which is User ID)
     * @return int Number of supervised projects
     */
    public function getSupervisedProjectsCount(int $professorId): int
    {
        $sql = "SELECT COUNT(*) as count FROM projects WHERE supervisor_id = ?";
        $result = $this->queryOne($sql, [$professorId]);
        return (int) ($result['count'] ?? 0);
    }
}
