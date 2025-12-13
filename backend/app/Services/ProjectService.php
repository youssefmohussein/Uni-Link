<?php
namespace App\Services;

use App\Repositories\ProjectRepository;
use App\Repositories\SkillRepository;

/**
 * Project Service
 * 
 * Business logic for project management
 */
class ProjectService extends BaseService {
    private ProjectRepository $projectRepo;
    private SkillRepository $skillRepo;
    
    public function __construct(ProjectRepository $projectRepo, SkillRepository $skillRepo) {
        $this->projectRepo = $projectRepo;
        $this->skillRepo = $skillRepo;
    }
    
    /**
     * Upload/Create project
     * 
     * @param array $data Project data
     * @param string|null $filePath Uploaded file path
     * @return array Created project
     */
    public function uploadProject(array $data, ?string $filePath = null): array {
        // Validate
        $errors = $this->validate($data, [
            'student_id' => ['required', 'numeric'],
            'title' => ['required', 'min:3'],
            'description' => ['required']
        ]);
        
        if (!empty($errors)) {
            throw new \Exception($this->formatValidationErrors($errors), 400);
        }
        
        // Sanitize
        $sanitized = $this->sanitizeData($data, ['title', 'description']);
        
        // Create project
        $projectId = $this->projectRepo->create([
            'student_id' => (int)$data['student_id'],
            'supervisor_id' => $data['supervisor_id'] ?? null,
            'title' => $sanitized['title'],
            'description' => $sanitized['description'],
            'file_path' => $filePath,
            'status' => $data['status'] ?? 'Pending',
            'grade' => $data['grade'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        // Add skills if provided
        if (!empty($data['skills']) && is_array($data['skills'])) {
            $this->projectRepo->addSkills($projectId, $data['skills']);
        }
        
        return $this->projectRepo->getWithSkills($projectId);
    }
    
    /**
     * Update project
     * 
     * @param int $projectId Project ID
     * @param array $data Updated data
     * @return array Updated project
     */
    public function updateProject(int $projectId, array $data): array {
        if (!$this->projectRepo->exists($projectId)) {
            throw new \Exception('Project not found', 404);
        }
        
        // Sanitize
        $sanitized = $this->sanitizeData($data, ['title', 'description']);
        
        $updateData = [];
        if (isset($sanitized['title'])) $updateData['title'] = $sanitized['title'];
        if (isset($sanitized['description'])) $updateData['description'] = $sanitized['description'];
        if (isset($data['status'])) $updateData['status'] = $data['status'];
        if (isset($data['grade'])) $updateData['grade'] = $data['grade'];
        if (isset($data['file_path'])) $updateData['file_path'] = $data['file_path'];
        
        if (!empty($updateData)) {
            $this->projectRepo->update($projectId, $updateData);
        }
        
        // Update skills if provided
        if (isset($data['skills']) && is_array($data['skills'])) {
            $this->projectRepo->removeSkills($projectId);
            $this->projectRepo->addSkills($projectId, $data['skills']);
        }
        
        return $this->projectRepo->getWithSkills($projectId);
    }
    
    /**
     * Delete project
     * 
     * @param int $projectId Project ID
     * @return bool Success status
     */
    public function deleteProject(int $projectId): bool {
        if (!$this->projectRepo->exists($projectId)) {
            throw new \Exception('Project not found', 404);
        }
        
        return $this->transaction(function() use ($projectId) {
            // Remove skills first
            $this->projectRepo->removeSkills($projectId);
            
            // Delete project
            return $this->projectRepo->delete($projectId);
        }, $this->projectRepo);
    }
    
    /**
     * Add grade to project
     * 
     * @param int $projectId Project ID
     * @param string $grade Grade value
     * @return array Updated project
     */
    public function addGrade(int $projectId, string $grade): array {
        if (!$this->projectRepo->exists($projectId)) {
            throw new \Exception('Project not found', 404);
        }
        
        $this->projectRepo->update($projectId, [
            'grade' => $grade,
            'status' => 'Graded'
        ]);
        
        return $this->projectRepo->find($projectId);
    }
    
    /**
     * Get user projects
     * 
     * @param int $userId User ID
     * @return array Array of projects
     */
    public function getUserProjects(int $userId): array {
        return $this->projectRepo->findByUser($userId);
    }
    
    /**
     * Get all projects with details
     * 
     * @param int|null $limit Limit
     * @param int $offset Offset
     * @return array Array of projects
     */
    public function getAllProjects(?int $limit = null, int $offset = 0): array {
        return $this->projectRepo->getAllWithDetails($limit, $offset);
    }
}
