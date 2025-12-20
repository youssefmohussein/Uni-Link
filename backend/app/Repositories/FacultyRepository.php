<?php
namespace App\Repositories;

/**
 * Faculty Repository
 * 
 * Data access layer for Faculty entity
 */
class FacultyRepository extends BaseRepository
{
    protected string $table = 'faculties';
    protected string $primaryKey = 'faculty_id';

    /**
     * Find faculty by name
     * 
     * @param string $name Faculty name
     * @return array|null Faculty data or null
     */
    public function findByName(string $name): ?array
    {
        return $this->findOneBy('name', $name);
    }

    /**
     * Get all majors for a faculty
     * 
     * @param int $facultyId Faculty ID
     * @return array Array of majors
     */
    public function getMajors(int $facultyId): array
    {
        $sql = "SELECT * FROM majors WHERE faculty_id = ? ORDER BY name ASC";
        return $this->query($sql, [$facultyId]);
    }

    /**
     * Get faculty with major count and student count
     * 
     * @return array Array of faculties with counts
     */
    public function getAllWithDetails(): array
    {
        $sql = "
            SELECT 
                f.*, 
                COUNT(DISTINCT m.major_id) as major_count,
                COUNT(DISTINCT CASE WHEN u.role = 'Student' THEN u.user_id END) as student_count,
                COUNT(DISTINCT CASE WHEN u.role = 'Professor' THEN u.user_id END) as professor_count,
                GROUP_CONCAT(DISTINCT m.name SEPARATOR ', ') as major_names
            FROM faculties f
            LEFT JOIN majors m ON f.faculty_id = m.faculty_id
            LEFT JOIN users u ON f.faculty_id = u.faculty_id
            GROUP BY f.faculty_id
            ORDER BY f.name ASC
        ";
        return $this->query($sql);
    }

    /**
     * Find faculty by ID with details
     * 
     * @param int $id Faculty ID
     * @return array|null Faculty data or null
     */
    public function findByIdWithDetails(int $id): ?array
    {
        $sql = "
            SELECT 
                f.*, 
                COUNT(DISTINCT m.major_id) as major_count,
                COUNT(DISTINCT CASE WHEN u.role = 'Student' THEN u.user_id END) as student_count,
                COUNT(DISTINCT CASE WHEN u.role = 'Professor' THEN u.user_id END) as professor_count,
                GROUP_CONCAT(DISTINCT m.name SEPARATOR ', ') as major_names
            FROM faculties f
            LEFT JOIN majors m ON f.faculty_id = m.faculty_id
            LEFT JOIN users u ON f.faculty_id = u.faculty_id
            WHERE f.faculty_id = ?
            GROUP BY f.faculty_id
        ";
        $result = $this->query($sql, [$id]);
        return $result ? $result[0] : null;
    }

    /**
     * Find faculty by name with details
     * 
     * @param string $name Faculty name
     * @return array|null Faculty data or null
     */
    public function findByNameWithDetails(string $name): ?array
    {
        $sql = "
            SELECT 
                f.*, 
                COUNT(DISTINCT m.major_id) as major_count,
                COUNT(DISTINCT CASE WHEN u.role = 'Student' THEN u.user_id END) as student_count,
                COUNT(DISTINCT CASE WHEN u.role = 'Professor' THEN u.user_id END) as professor_count,
                GROUP_CONCAT(DISTINCT m.name SEPARATOR ', ') as major_names
            FROM faculties f
            LEFT JOIN majors m ON f.faculty_id = m.faculty_id
            LEFT JOIN users u ON f.faculty_id = u.faculty_id
            WHERE f.name = ?
            GROUP BY f.faculty_id
        ";
        $result = $this->query($sql, [$name]);
        return $result ? $result[0] : null;
    }

    /**
     * Find faculty by name with details (Fuzzy match)
     * 
     * @param string $name Part of faculty name
     * @return array|null Faculty data or null
     */
    public function findByNameFuzzy(string $name): ?array
    {
        $sql = "
            SELECT 
                f.*, 
                COUNT(DISTINCT m.major_id) as major_count,
                COUNT(DISTINCT CASE WHEN u.role = 'Student' THEN u.user_id END) as student_count,
                COUNT(DISTINCT CASE WHEN u.role = 'Professor' THEN u.user_id END) as professor_count,
                GROUP_CONCAT(DISTINCT m.name SEPARATOR ', ') as major_names
            FROM faculties f
            LEFT JOIN majors m ON f.faculty_id = m.faculty_id
            LEFT JOIN users u ON f.faculty_id = u.faculty_id
            WHERE f.name LIKE ?
            GROUP BY f.faculty_id
            LIMIT 1
        ";
        $result = $this->query($sql, ["%$name%"]);
        return $result ? $result[0] : null;
    }
}
