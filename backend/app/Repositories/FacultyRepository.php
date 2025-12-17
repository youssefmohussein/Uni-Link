<?php
namespace App\Repositories;

/**
 * Faculty Repository
 * 
 * Data access layer for Faculty entity
 */
class FacultyRepository extends BaseRepository {
    protected string $table = 'faculties';
    protected string $primaryKey = 'faculty_id';
    
    /**
     * Find faculty by name
     * 
     * @param string $name Faculty name
     * @return array|null Faculty data or null
     */
    public function findByName(string $name): ?array {
        return $this->findOneBy('name', $name);
    }
    
    /**
     * Get all majors for a faculty
     * 
     * @param int $facultyId Faculty ID
     * @return array Array of majors
     */
    public function getMajors(int $facultyId): array {
        $sql = "SELECT * FROM majors WHERE faculty_id = ? ORDER BY name ASC";
        return $this->query($sql, [$facultyId]);
    }
    
    /**
     * Get faculty with major count
     * 
     * @return array Array of faculties with major counts
     */
    public function getAllWithMajorCount(): array {
        $sql = "
            SELECT f.*, f.name as faculty_name, COUNT(m.major_id) as major_count
            FROM faculties f
            LEFT JOIN majors m ON f.faculty_id = m.faculty_id
            GROUP BY f.faculty_id
            ORDER BY f.name ASC
        ";
        return $this->query($sql);
    }
}
