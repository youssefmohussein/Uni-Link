<?php
namespace App\Repositories;

/**
 * Professor Repository
 * 
 * Data access layer for Professor entity
 */
class ProfessorRepository extends BaseRepository {
    protected string $table = 'Professor';
    protected string $primaryKey = 'professor_id';
    
    /**
     * Get professor with user information
     * 
     * @param int $professorId Professor ID
     * @return array|null Professor with user data
     */
    public function getWithUserInfo(int $professorId): ?array {
        $sql = "
            SELECT p.*, u.username, u.email, u.phone, u.profile_image, u.bio, u.job_title,
                   f.faculty_name, m.major_name
            FROM Professor p
            JOIN Users u ON p.professor_id = u.user_id
            LEFT JOIN Faculty f ON u.faculty_id = f.faculty_id
            LEFT JOIN Major m ON u.major_id = m.major_id
            WHERE p.professor_id = ?
        ";
        return $this->queryOne($sql, [$professorId]);
    }
    
    /**
     * Get all professors with user information
     * 
     * @return array Array of professors
     */
    public function getAllWithUserInfo(): array {
        $sql = "
            SELECT p.*, u.username, u.email, u.phone, u.profile_image, u.job_title,
                   f.faculty_name, m.major_name
            FROM Professor p
            JOIN Users u ON p.professor_id = u.user_id
            LEFT JOIN Faculty f ON u.faculty_id = f.faculty_id
            LEFT JOIN Major m ON u.major_id = m.major_id
            ORDER BY u.username ASC
        ";
        return $this->query($sql);
    }
    
    /**
     * Find professors by department/faculty
     * 
     * @param int $facultyId Faculty ID
     * @return array Array of professors
     */
    public function findByFaculty(int $facultyId): array {
        $sql = "
            SELECT p.*, u.username, u.email, u.profile_image, u.job_title
            FROM Professor p
            JOIN Users u ON p.professor_id = u.user_id
            WHERE u.faculty_id = ?
            ORDER BY u.username ASC
        ";
        return $this->query($sql, [$facultyId]);
    }
    
    /**
     * Get supervised projects count
     * 
     * @param int $professorId Professor ID
     * @return int Number of supervised projects
     */
    public function getSupervisedProjectsCount(int $professorId): int {
        $sql = "SELECT COUNT(*) as count FROM Project WHERE supervisor_id = ?";
        $result = $this->queryOne($sql, [$professorId]);
        return (int)($result['count'] ?? 0);
    }
}
