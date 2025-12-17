<?php
namespace App\Repositories;

/**
 * Student Repository
 * 
 * Data access layer for Student entity
 */
class StudentRepository extends BaseRepository {
    protected string $table = 'students';
    protected string $primaryKey = 'student_id';
    
    /**
     * Find students by year
     * 
     * @param int $year Academic year
     * @return array Array of students
     */
    public function findByYear(int $year): array {
        return $this->findBy('year', $year);
    }
    
    /**
     * Find students by GPA range
     * 
     * @param float $minGpa Minimum GPA
     * @param float $maxGpa Maximum GPA
     * @return array Array of students
     */
    public function findByGpaRange(float $minGpa, float $maxGpa): array {
        $sql = "SELECT * FROM {$this->table} WHERE gpa >= ? AND gpa <= ? ORDER BY gpa DESC";
        return $this->query($sql, [$minGpa, $maxGpa]);
    }
    
    /**
     * Update student points
     * 
     * @param int $studentId Student ID
     * @param int $points Points to add (can be negative)
     * @return bool Success status
     */
    public function updatePoints(int $studentId, int $points): bool {
        $sql = "UPDATE {$this->table} SET points = points + ? WHERE {$this->primaryKey} = ?";
        return $this->execute($sql, [$points, $studentId]) > 0;
    }
    
    /**
     * Get student with user information
     * 
     * @param int $studentId Student ID
     * @return array|null Student with user data
     */
    public function getWithUserInfo(int $studentId): ?array {
        $sql = "
            SELECT s.*, u.username, u.email, u.phone, u.profile_picture as profile_image, u.bio, 
                   f.name as faculty_name, m.name as major_name
            FROM students s
            JOIN users u ON s.user_id = u.user_id
            LEFT JOIN faculties f ON u.faculty_id = f.faculty_id
            LEFT JOIN majors m ON u.major_id = m.major_id
            WHERE s.student_id = ?
        ";
        return $this->queryOne($sql, [$studentId]);
    }
    
    /**
     * Get top students by points
     * 
     * @param int $limit Number of students
     * @return array Array of top students
     */
    public function getTopByPoints(int $limit = 10): array {
        $sql = "
            SELECT s.*, u.username, u.profile_picture as profile_image, f.name as faculty_name, m.name as major_name
            FROM students s
            JOIN users u ON s.user_id = u.user_id
            LEFT JOIN faculties f ON u.faculty_id = f.faculty_id
            LEFT JOIN majors m ON u.major_id = m.major_id
            ORDER BY s.points DESC
            LIMIT ?
        ";
        return $this->query($sql, [$limit]);
    }
}
