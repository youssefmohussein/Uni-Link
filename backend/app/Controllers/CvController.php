<?php
namespace App\Controllers;

use App\Models\User;

class CvController extends BaseController {
    
    /**
     * Upload CV for the current user
     */
    public function upload(): void {
        try {
            // Get current user ID from session/token (BaseController method)
            $userId = $this->getCurrentUserId();
            
            if (!isset($_FILES['cv_file'])) {
                $this->error('No file uploaded', 400);
            }
            
            $file = $_FILES['cv_file'];
            
            // Validate file type (PDF only)
            $mimeType = mime_content_type($file['tmp_name']);
            if ($mimeType !== 'application/pdf') {
                $this->error('Only PDF files are allowed', 400);
            }
            
            // Validate file size (max 5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                $this->error('File size exceeds 5MB limit', 400);
            }
            
            // Create upload directory if exists
            $uploadDir = __DIR__ . '/../../uploads/cvs/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'cv_' . $userId . '_' . time() . '.' . $extension;
            $targetPath = $uploadDir . $filename;
            
            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                throw new \Exception('Failed to move uploaded file');
            }
            
            // Save path to database (relative path)
            $relativePath = 'uploads/cvs/' . $filename;
            
            // Update user record with CV path
            // For now using direct query since we don't have a specialized CvService yet
            // In a full refactor, this logic belongs in UserService or CvService
            $db = new \PDO("mysql:host={$this->dbConfig['host']};dbname={$this->dbConfig['dbname']}", $this->dbConfig['username'], $this->dbConfig['password']);
            $stmt = $db->prepare("INSERT INTO cv (user_id, file_path, created_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE file_path = ?, created_at = NOW()");
            $stmt->execute([$userId, $relativePath, $relativePath]);
            
            $this->success([
                'file_path' => $relativePath,
                'message' => 'CV uploaded successfully'
            ]);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), 500);
        }
    }
    
    /**
     * Download CV for a user
     */
    public function download(int $userId): void {
        try {
            // Logic to get CV path from DB
            $db = new \PDO("mysql:host={$this->dbConfig['host']};dbname={$this->dbConfig['dbname']}", $this->dbConfig['username'], $this->dbConfig['password']);
            $stmt = $db->prepare("SELECT file_path FROM cv WHERE user_id = ?");
            $stmt->execute([$userId]);
            $cv = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$cv || !file_exists(__DIR__ . '/../../' . $cv['file_path'])) {
                $this->error('CV not found', 404);
            }
            
            $filePath = __DIR__ . '/../../' . $cv['file_path'];
            $filename = basename($filePath);
            
            // Serve file
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Get CV metadata
     */
    public function getCV(): void {
        try {
            $userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : $this->getCurrentUserId();
            
            $db = new \PDO("mysql:host={$this->dbConfig['host']};dbname={$this->dbConfig['dbname']}", $this->dbConfig['username'], $this->dbConfig['password']);
            $stmt = $db->prepare("SELECT file_path, created_at FROM cv WHERE user_id = ?");
            $stmt->execute([$userId]);
            $cv = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$cv) {
                // Not an error, just no CV
                $this->error('CV not found', 404);
                return;
            }
            
            $this->success($cv);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Delete CV
     */
    public function delete(): void {
        try {
            $userId = $this->getCurrentUserId();
            
            // Get file path first
            $db = new \PDO("mysql:host={$this->dbConfig['host']};dbname={$this->dbConfig['dbname']}", $this->dbConfig['username'], $this->dbConfig['password']);
            $stmt = $db->prepare("SELECT file_path FROM cv WHERE user_id = ?");
            $stmt->execute([$userId]);
            $cv = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($cv && file_exists(__DIR__ . '/../../' . $cv['file_path'])) {
                unlink(__DIR__ . '/../../' . $cv['file_path']);
            }
            
            // Delete from DB
            $stmt = $db->prepare("DELETE FROM cv WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            $this->success(null, 'CV deleted successfully');
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), 500);
        }
    }
}
