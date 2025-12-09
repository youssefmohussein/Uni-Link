<?php
namespace App\Controllers;

use App\Utils\Database;
use PDO;

/**
 * Project Controller
 * 
 * Handles project upload, retrieval, update, and deletion
 */
class ProjectController {
    private PDO $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Upload a new project
     * POST /uploadProject
     */
    public function uploadProject() {
        try {
            // Get user_id from POST data
            $userId = $_POST['user_id'] ?? null;
            $title = $_POST['title'] ?? null;
            $description = $_POST['description'] ?? null;
            $status = $_POST['status'] ?? 'Pending';
            
            // Validate required fields
            if (!$userId || !$title || !$description) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Missing required fields: user_id, title, description'
                ]);
                return;
            }
            
            // Handle file upload if present
            $filePath = null;
            if (isset($_FILES['project_file']) && $_FILES['project_file']['error'] === UPLOAD_ERR_OK) {
                $filePath = $this->handleFileUpload($_FILES['project_file']);
                if (!$filePath) {
                    http_response_code(500);
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Failed to upload file'
                    ]);
                    return;
                }
            }
            
            // Insert project into database
            $stmt = $this->db->prepare("
                INSERT INTO projects (user_id, title, description, status, file_path, created_at)
                VALUES (:user_id, :title, :description, :status, :file_path, NOW())
            ");
            
            $stmt->execute([
                'user_id' => $userId,
                'title' => $title,
                'description' => $description,
                'status' => $status,
                'file_path' => $filePath
            ]);
            
            $projectId = $this->db->lastInsertId();
            
            // Fetch the created project with all details
            $project = $this->getProjectById($projectId);
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Project uploaded successfully',
                'data' => $project
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get all projects for a user
     * GET /getUserProjects?user_id=X
     */
    public function getUserProjects() {
        try {
            $userId = $_GET['user_id'] ?? null;
            
            if (!$userId) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Missing user_id parameter'
                ]);
                return;
            }
            
            $stmt = $this->db->prepare("
                SELECT 
                    p.project_id,
                    p.title,
                    p.description,
                    p.status,
                    p.grade,
                    p.file_path,
                    p.created_at,
                    COALESCE(u.username, 'N/A') as supervisor_name
                FROM projects p
                LEFT JOIN users u ON p.supervisor_id = u.user_id
                WHERE p.user_id = :user_id
                ORDER BY p.created_at DESC
            ");
            
            $stmt->execute(['user_id' => $userId]);
            $projects = $stmt->fetchAll();
            
            echo json_encode([
                'status' => 'success',
                'data' => $projects
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Delete a project
     * POST /deleteProject
     */
    public function deleteProject() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $projectId = $input['project_id'] ?? null;
            $ownerId = $input['owner_id'] ?? null;
            
            if (!$projectId || !$ownerId) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Missing project_id or owner_id'
                ]);
                return;
            }
            
            // Verify ownership
            $stmt = $this->db->prepare("SELECT user_id, file_path FROM projects WHERE project_id = :project_id");
            $stmt->execute(['project_id' => $projectId]);
            $project = $stmt->fetch();
            
            if (!$project) {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Project not found'
                ]);
                return;
            }
            
            if ($project['user_id'] != $ownerId) {
                http_response_code(403);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'You do not have permission to delete this project'
                ]);
                return;
            }
            
            // Delete file if exists
            if ($project['file_path'] && file_exists(__DIR__ . '/../../' . $project['file_path'])) {
                unlink(__DIR__ . '/../../' . $project['file_path']);
            }
            
            // Delete project from database
            $stmt = $this->db->prepare("DELETE FROM projects WHERE project_id = :project_id");
            $stmt->execute(['project_id' => $projectId]);
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Project deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Update a project
     * POST /updateProject
     */
    public function updateProject() {
        try {
            $userId = $_POST['user_id'] ?? null;
            $projectId = $_POST['project_id'] ?? null;
            $title = $_POST['title'] ?? null;
            $description = $_POST['description'] ?? null;
            
            if (!$userId || !$projectId) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Missing user_id or project_id'
                ]);
                return;
            }
            
            // Verify ownership
            $stmt = $this->db->prepare("SELECT user_id, file_path FROM projects WHERE project_id = :project_id");
            $stmt->execute(['project_id' => $projectId]);
            $project = $stmt->fetch();
            
            if (!$project) {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Project not found'
                ]);
                return;
            }
            
            if ($project['user_id'] != $userId) {
                http_response_code(403);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'You do not have permission to update this project'
                ]);
                return;
            }
            
            // Handle file upload if present
            $filePath = $project['file_path'];
            if (isset($_FILES['project_file']) && $_FILES['project_file']['error'] === UPLOAD_ERR_OK) {
                // Delete old file
                if ($filePath && file_exists(__DIR__ . '/../../' . $filePath)) {
                    unlink(__DIR__ . '/../../' . $filePath);
                }
                $filePath = $this->handleFileUpload($_FILES['project_file']);
            }
            
            // Build update query dynamically
            $updates = [];
            $params = ['project_id' => $projectId];
            
            if ($title) {
                $updates[] = "title = :title";
                $params['title'] = $title;
            }
            if ($description) {
                $updates[] = "description = :description";
                $params['description'] = $description;
            }
            if ($filePath !== $project['file_path']) {
                $updates[] = "file_path = :file_path";
                $params['file_path'] = $filePath;
            }
            
            if (empty($updates)) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'No fields to update'
                ]);
                return;
            }
            
            $sql = "UPDATE projects SET " . implode(', ', $updates) . " WHERE project_id = :project_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            // Fetch updated project
            $updatedProject = $this->getProjectById($projectId);
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Project updated successfully',
                'data' => $updatedProject
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Handle file upload
     * @param array $file - $_FILES array element
     * @return string|false - File path or false on failure
     */
    private function handleFileUpload($file) {
        $uploadDir = __DIR__ . '/../../uploads/projects/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('project_') . '_' . time() . '.' . $extension;
        $targetPath = $uploadDir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return 'uploads/projects/' . $filename;
        }
        
        return false;
    }
    
    /**
     * Get project by ID with all details
     * @param int $projectId
     * @return array|null
     */
    private function getProjectById($projectId) {
        $stmt = $this->db->prepare("
            SELECT 
                p.project_id,
                p.title,
                p.description,
                p.status,
                p.grade,
                p.file_path,
                p.created_at,
                COALESCE(u.username, 'N/A') as supervisor_name
            FROM projects p
            LEFT JOIN users u ON p.supervisor_id = u.user_id
            WHERE p.project_id = :project_id
        ");
        
        $stmt->execute(['project_id' => $projectId]);
        return $stmt->fetch();
    }
}
