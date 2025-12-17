<?php
namespace App\Controllers;

use App\Services\ProjectService;

/**
 * Project Controller
 * 
 * Handles project operations
 */
class ProjectController extends BaseController {
    private ProjectService $projectService;
    
    public function __construct(ProjectService $projectService) {
        $this->projectService = $projectService;
    }
    
    /**
     * Upload a new project
     */
    public function uploadProject(): void {
        try {
            $this->requireAuth();
            
            // Handle file upload
            $filePath = null;
            if (isset($_FILES['project_file'])) {
                $filePath = $this->handleFileUpload(
                    'project_file',
                    __DIR__ . '/../../uploads/projects',
                    [],
                    10485760 // 10MB
                );
            }
            
            // Get data from POST
            $data = [
                'student_id' => $_POST['user_id'] ?? $this->getCurrentUserId(),
                'title' => $_POST['title'] ?? null,
                'description' => $_POST['description'] ?? null,
                'status' => $_POST['status'] ?? 'Pending',
                'supervisor_id' => $_POST['supervisor_id'] ?? null
            ];
            
            $project = $this->projectService->uploadProject($data, $filePath);
            $this->success($project, 'Project uploaded successfully', 201);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Get user projects
     */
    public function getUserProjects(): void {
        try {
            $userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : $this->getCurrentUserId();
            
            if (!$userId) {
                throw new \Exception('User ID is required', 400);
            }
            
            $projects = $this->projectService->getUserProjects($userId);
            $this->success(['data' => $projects]);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), (int)($e->getCode() ?: 400));
        }
    }
    
    /**
     * Delete project
     */
    public function deleteProject(): void {
        try {
            $this->requireAuth();
            
            $data = $this->getJsonInput();
            $this->validateRequired($data, ['project_id']);
            
            $this->projectService->deleteProject((int)$data['project_id']);
            $this->success(null, 'Project deleted successfully');
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Update project
     */
    public function updateProject(): void {
        try {
            $this->requireAuth();
            
            $projectId = $_POST['project_id'] ?? null;
            
            if (!$projectId) {
                throw new \Exception('Project ID is required', 400);
            }
            
            // Handle file upload if present
            $filePath = null;
            if (isset($_FILES['project_file'])) {
                $filePath = $this->handleFileUpload(
                    'project_file',
                    __DIR__ . '/../../uploads/projects',
                    [],
                    10485760 // 10MB
                );
            }
            
            $data = [
                'title' => $_POST['title'] ?? null,
                'description' => $_POST['description'] ?? null
            ];
            
            if ($filePath) {
                $data['file_path'] = $filePath;
            }
            
            $project = $this->projectService->updateProject((int)$projectId, $data);
            $this->success($project, 'Project updated successfully');
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Get all projects
     */
    public function getAll(): void {
        try {
            $this->requireAuth();
            
            $pagination = $this->getPagination();
            $projects = $this->projectService->getAllProjects($pagination['limit'], $pagination['offset']);
            
            $this->success([
                'count' => count($projects),
                'data' => $projects
            ]);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Approve project
     */
    public function approveProject(): void {
        try {
            $this->requireRole('Professor');
            $professorId = $this->getCurrentUserId();

            $data = $this->getJsonInput();
            $this->validateRequired($data, ['project_id', 'score']);

            $project = $this->projectService->approveProject(
                (int)$data['project_id'], 
                $professorId, 
                (float)$data['score'], 
                $data['comment'] ?? null
            );
            
            $this->success($project, 'Project approved successfully');

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    /**
     * Reject project
     */
    public function rejectProject(): void {
        try {
            $this->requireRole('Professor');
            $professorId = $this->getCurrentUserId();

            $data = $this->getJsonInput();
            $this->validateRequired($data, ['project_id']);

            $project = $this->projectService->rejectProject(
                (int)$data['project_id'], 
                $professorId, 
                $data['comment'] ?? null
            );
            
            $this->success($project, 'Project rejected successfully');

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
