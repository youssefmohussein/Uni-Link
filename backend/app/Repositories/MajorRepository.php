<?php
namespace App\Repositories;

/**
 * Major Repository
 * 
 * Data access layer for Major entity
 */
class MajorRepository extends BaseRepository {
    protected string $table = 'majors';
    protected string $primaryKey = 'major_id';
    
    /**
     * Find major by name
     * 
     * @param string $name Major name
     * @return array|null Major data or null
     */
    public function findByName(string $name): ?array {
        return $this->findOneBy('name', $name);
    }
    
    /**
     * Find majors by faculty
     * 
     * @param int $facultyId Faculty ID
     * @return array Array of majors
     */
    public function findByFaculty(int $facultyId): array {
        return $this->findBy('faculty_id', $facultyId);
    }
    
    /**
     * Get major with faculty information
     * 
     * @param int $majorId Major ID
     * @return array|null Major with faculty data
     */
    public function getWithFaculty(int $majorId): ?array {
        $sql = "
            SELECT m.*, f.name as faculty_name
            FROM majors m
            LEFT JOIN faculties f ON m.faculty_id = f.faculty_id
            WHERE m.major_id = ?
        ";
        return $this->queryOne($sql, [$majorId]);
    }
    
    /**
     * Get all majors with faculty information
     * 
     * @return array Array of majors with faculty data
     */
    public function getAllWithFaculty(): array {
        $sql = "
            SELECT m.*, f.name as faculty_name
            FROM majors m
            LEFT JOIN faculties f ON m.faculty_id = f.faculty_id
            ORDER BY f.name ASC, m.name ASC
        ";
        return $this->query($sql);
    }
}
