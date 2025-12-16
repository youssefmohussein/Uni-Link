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
            SELECT s.skill_id, s.skill_name, sc.category_name
            FROM projectskill ps
            JOIN skills s ON ps.skill_id = s.skill_id
            LEFT JOIN skillcategory sc ON s.category_id = sc.category_id
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
                $sql = "INSERT INTO projectskill (project_id, skill_id) VALUES (?, ?)";
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
            $sql = "DELETE FROM projectskill WHERE project_id = ?";
            return $this->execute($sql, [$projectId]) >= 0;
        }
        
        $placeholders = implode(',', array_fill(0, count($skillIds), '?'));
        $sql = "DELETE FROM projectskill WHERE project_id = ? AND skill_id IN ({$placeholders})";
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
    public function getAllWithDetails(?int $limit = null, int $offset = 0): array {
        $sql = "
            SELECT 
                p.*,
                u.username as student_name,
                s.username as supervisor_name,
                f.faculty_name,
                m.major_name
            FROM projects p
            LEFT JOIN users u ON p.student_id = u.user_id
            LEFT JOIN users s ON p.supervisor_id = s.user_id
            LEFT JOIN faculty f ON u.faculty_id = f.faculty_id
            LEFT JOIN major m ON u.major_id = m.major_id
            ORDER BY p.created_at DESC
        ";
        
        if ($limit !== null) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }
        
        return $this->query($sql);
    }
}
