<?php
namespace App\Repositories;

use PDO;

/**
 * Subject Repository
 * 
 * Handles database operations for subjects
 */
class SubjectRepository extends BaseRepository {
    protected string $table = 'subjects';
    protected string $primaryKey = 'subject_id';
    
    /**
     * Find subjects by faculty
     * 
     * @param int $facultyId Faculty ID
     * @return array Array of subjects
     */
    public function findByFaculty(int $facultyId): array {
        return $this->findAll(['faculty_id' => $facultyId]);
    }
    
    /**
     * Find registered subjects for a student
     * 
     * @param int $userId Student User ID
     * @return array Array of subjects
     */
    public function findStudentSubjects(int $userId): array {
        return $this->query("
            SELECT s.*
            FROM {$this->table} s
            JOIN student_subjects ss ON s.subject_id = ss.subject_id
            WHERE ss.user_id = ?
            ORDER BY s.name ASC
        ", [$userId]);
    }

    /**
     * Register a student to a subject
     * 
     * @param int $userId Student User ID
     * @param int $subjectId Subject ID
     * @return bool
     */
    public function registerStudent(int $userId, int $subjectId): bool {
        // Check if already registered
        $exists = $this->queryOne("
            SELECT 1 FROM student_subjects 
            WHERE user_id = ? AND subject_id = ?
        ", [$userId, $subjectId]);

        if ($exists) return true;

        $stmt = $this->db->prepare("
            INSERT INTO student_subjects (user_id, subject_id, created_at)
            VALUES (?, ?, NOW())
        ");
        return $stmt->execute([$userId, $subjectId]);
    }
}
