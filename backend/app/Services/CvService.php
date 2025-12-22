<?php
namespace App\Services;

use App\Repositories\CvRepository;

/**
 * CV Service
 * 
 * Business logic for CV management
 */
class CvService extends BaseService {
    private CvRepository $cvRepo;
    
    public function __construct(CvRepository $cvRepo) {
        $this->cvRepo = $cvRepo;
    }
    
    /**
     * Upload CV
     * 
     * @param int $userId User ID
     * @param array $file $_FILES array element
     * @return array CV data
     */
    public function uploadCv(int $userId, array $file): array {
        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('File upload error', 400);
        }
        
        // Validate file type
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            throw new \Exception('Only PDF files are allowed', 400);
        }
        
        // Validate file size (5MB max)
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new \Exception('File size must be less than 5MB', 400);
        }
        
        // Delete old CV if exists
        $oldCv = $this->cvRepo->findByUser($userId);
        if ($oldCv && $oldCv['file_path']) {
            $oldPath = __DIR__ . '/../../' . $oldCv['file_path'];
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }
        
        // Upload new file
        $uploadDir = __DIR__ . '/../../public/uploads/cvs/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $filename = 'user_' . $userId . '_cv_' . time() . '.pdf';
        $targetPath = $uploadDir . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new \Exception('Failed to upload file', 500);
        }
        
        $filePath = 'public/uploads/cvs/' . $filename;
        
        // Save to database
        $this->cvRepo->upload($userId, $filePath);
        
        return [
            'user_id' => $userId,
            'file_path' => $filePath
        ];
    }
    
    /**
     * Download CV
     * 
     * @param int $userId User ID
     * @return array CV file info
     */
    public function downloadCv(int $userId): array {
        $cv = $this->cvRepo->findByUser($userId);
        
        if (!$cv) {
            throw new \Exception('CV not found', 404);
        }
        
        $filePath = __DIR__ . '/../../' . $cv['file_path'];
        
        if (!file_exists($filePath)) {
            throw new \Exception('CV file not found', 404);
        }
        
        return [
            'path' => $filePath,
            'filename' => 'CV_' . $userId . '.pdf'
        ];
    }
}
