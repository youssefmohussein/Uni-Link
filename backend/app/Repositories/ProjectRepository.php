<?php
namespace App\Repositories;

/**
 * Project Repository
 * 
 * Data access layer for Project entity
 */
class ProjectRepository extends BaseRepository {
    protected string $table = 'projects';
    protected string $primaryKey = 'project_id';
    
    /**
     * Find projects by user
     * 
     * @param int $userId User ID
     * @return array Array of projects
     */
    public function findByUser(int $userId): array {
        $sql = "
            SELECT p.*, u.username as supervisor_name
            FROM projects p
            LEFT JOIN users u ON p.supervisor_id = u.user_id
            WHERE p.student_id = ?
            ORDER BY p.created_at DESC
        ";
        return $this->query($sql, [$userId]);
    }
    
    /**
     * Find projects by grade
     * 
     * @param string $grade Grade value
     * @return array Array of projects
     */
    public function findByGrade(string $grade): array {
        return $this->findBy('grade', $grade);
    }
    
    /**
     * Find projects by supervisor
     * 
     * @param int $supervisorId Supervisor ID
     * @return array Array of projects
     */
    public function findBySupervisor(int $supervisorId): array {
        return $this->findBy('supervisor_id', $supervisorId);
    }
    
    /**
     * Get project with skills
     * 
     * @param int $projectId Project ID
     * @return array|null Project with skills
     */
    public function getWithSkills(int $projectId): ?array {
        $project = $this->find($projectId);
        
        if (!$project) {
            return null;
        }
        
        $sql = "
            SELECT s.skill_id, s.name as skill_name, sc.name as category_name
            FROM project_skills ps
            JOIN skills s ON ps.skill_id = s.skill_id
            LEFT JOIN skill_categories sc ON s.category_id = sc.category_id
            WHERE ps.project_id = ?
        ";
        $project['skills'] = $this->query($sql, [$projectId]);
        
        return $project;
    }
    
    /**
     * Add skills to project
     * 
     * @param int $projectId Project ID
     * @param array $skillIds Array of skill IDs
     * @return bool Success status
     */
    public function addSkills(int $projectId, array $skillIds): bool {
        if (empty($skillIds)) {
            return true;
        }
        
        $this->beginTransaction();
        
        try {
            foreach ($skillIds as $skillId) {
                $sql = "INSERT INTO project_skills (project_id, skill_id) VALUES (?, ?)";
                $this->execute($sql, [$projectId, $skillId]);
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
    
    /**
     * Remove skills from project
     * 
     * @param int $projectId Project ID
     * @param array $skillIds Array of skill IDs (empty to remove all)
     * @return bool Success status
     */
    public function removeSkills(int $projectId, array $skillIds = []): bool {
        if (empty($skillIds)) {
            $sql = "DELETE FROM project_skills WHERE project_id = ?";
            return $this->execute($sql, [$projectId]) >= 0;
        }
        
        $placeholders = implode(',', array_fill(0, count($skillIds), '?'));
        $sql = "DELETE FROM project_skills WHERE project_id = ? AND skill_id IN ({$placeholders})";
        $params = array_merge([$projectId], $skillIds);
        
        return $this->execute($sql, $params) >= 0;
    }
    
    /**
     * Get all projects with complete information
     * 
     * @param int|null $limit Limit
     * @param int $offset Offset
     * @return array Array of projects
     */
    /**
     * Add project review
     * 
     * @param array $data Review data
     * @return int Review ID
     */
    public function addReview(array $data): int {
        $sql = "INSERT INTO project_reviews (project_id, professor_id, comment, score, status) VALUES (?, ?, ?, ?, ?)";
        $this->execute($sql, [
            $data['project_id'],
            $data['professor_id'],
            $data['comment'] ?? null,
            $data['score'] ?? null,
            $data['status']
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Get all projects with complete information
     * 
     * @param int|null $limit Limit
     * @param int $offset Offset
     * @return array Array of projects
     */
    public function getAllWithDetails(?int $limit = null, int $offset = 0): array {
        $sql = "
            SELECT 
                p.*,
                u.username as student_name,
                s.username as supervisor_name,
                f.name as faculty_name,
                m.name as major_name
            FROM projects p
            LEFT JOIN users u ON p.student_id = u.user_id
            LEFT JOIN users s ON p.supervisor_id = s.user_id
            LEFT JOIN faculties f ON u.faculty_id = f.faculty_id
            LEFT JOIN majors m ON u.major_id = m.major_id
            ORDER BY p.created_at DESC
        ";
        
        if ($limit !== null) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }
        
        return $this->query($sql);
    }

    /**
     * Get project grade distribution by faculty
     * 
     * @param int|null $facultyId Optional faculty ID to filter
     * @return array Grade distribution
     */
    public function getProjectGradeDistribution(?int $facultyId = null): array {
        $sql = "
            SELECT 
                CASE 
                    WHEN p.grade >= 90 THEN 'A (90-100)'
                    WHEN p.grade >= 80 THEN 'B (80-89)'
                    WHEN p.grade >= 70 THEN 'C (70-79)'
                    WHEN p.grade >= 60 THEN 'D (60-69)'
                    WHEN p.grade IS NOT NULL THEN 'F (Below 60)'
                    ELSE 'Not Graded'
                END as grade_range,
                COUNT(*) as project_count
            FROM projects p
            JOIN users u ON p.student_id = u.user_id
        ";

        $params = [];
        if ($facultyId) {
            $sql .= " WHERE u.faculty_id = ?";
            $params[] = $facultyId;
        }

        $sql .= "
            GROUP BY grade_range
            ORDER BY 
                CASE grade_range
                    WHEN 'A (90-100)' THEN 1
                    WHEN 'B (80-89)' THEN 2
                    WHEN 'C (70-79)' THEN 3
                    WHEN 'D (60-69)' THEN 4
                    WHEN 'F (Below 60)' THEN 5
                    ELSE 6
                END
        ";

        return $this->query($sql, $params);
    }

    /**
     * Get all projects with grading info filtered by faculty
     * 
     * @param int|null $facultyId Optional faculty filter
     * @param string|null $status Filter: 'graded', 'not_graded', 'all'
     * @return array Projects with grading status
     */
    public function getProjectsWithGradingStatus(?int $facultyId = null, ?string $status = 'all'): array {
        $sql = "
            SELECT 
                p.*,
                u.username as student_name,
                u.email as student_email,
                COALESCE(f.name, 'No Faculty') as faculty_name,
                COALESCE(m.name, 'No Major') as major_name,
                CASE 
                    WHEN p.grade IS NOT NULL THEN 'graded'
                    ELSE 'not_graded'
                END as grading_status
            FROM projects p
            JOIN users u ON p.student_id = u.user_id
            LEFT JOIN faculties f ON u.faculty_id = f.faculty_id
            LEFT JOIN majors m ON u.major_id = m.major_id
        ";

        $params = [];
        $conditions = [];

        // Only filter by faculty if facultyId is provided AND not null
        if ($facultyId !== null) {
            $conditions[] = "(u.faculty_id = ? OR u.faculty_id IS NULL)";
            $params[] = $facultyId;
        }

        if ($status === 'graded') {
            $conditions[] = "p.grade IS NOT NULL";
        } elseif ($status === 'not_graded') {
            $conditions[] = "p.grade IS NULL";
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY p.submitted_at DESC";

        return $this->query($sql, $params);
    }

    /**
     * Update project grade
     * 
     * @param int $projectId Project ID
     * @param float $grade Grade value
     * @return bool Success
     */
    public function updateGrade(int $projectId, float $grade): bool {
        $sql = "UPDATE projects SET grade = ?, updated_at = NOW() WHERE project_id = ?";
        return $this->execute($sql, [$grade, $projectId]) > 0;
    }

    /**
     * Update project status
     * 
     * @param int $projectId Project ID
     * @param string $status Status (PENDING, APPROVED, REJECTED)
     * @return bool Success
     */
    public function updateProjectStatus(int $projectId, string $status): bool {
        $sql = "UPDATE projects SET status = ?, updated_at = NOW() WHERE project_id = ?";
        return $this->execute($sql, [$status, $projectId]) > 0;
    }

    /**
     * Get top skills by faculty
     * 
     * @param int|null $facultyId Optional faculty ID to filter
     * @return array Top skills
     */
    public function getTopSkillsByFaculty(?int $facultyId = null): array {
        $sql = "
            SELECT s.name as skill_name, COUNT(ps.project_id) as project_count
            FROM project_skills ps
            JOIN skills s ON ps.skill_id = s.skill_id
            JOIN projects p ON ps.project_id = p.project_id
            JOIN users u ON p.student_id = u.user_id
        ";

        $params = [];
        if ($facultyId) {
            $sql .= " WHERE u.faculty_id = ? ";
            $params[] = $facultyId;
        }

        $sql .= "
            GROUP BY s.skill_id, s.name
            ORDER BY project_count DESC
            LIMIT 5
        ";

        return $this->query($sql, $params);
    }
}


