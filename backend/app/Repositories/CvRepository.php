<?php
namespace App\Repositories;

/**
 * CV Repository
 * 
 * Handles database operations for CVs
 */
class CvRepository extends BaseRepository {
    protected string $table = 'cvs';
    protected string $primaryKey = 'cv_id';
    
    /**
     * Find CV by user
     * 
     * @param int $userId User ID
     * @return array|null CV data
     */
    public function findByUser(int $userId): ?array {
        return $this->queryOne("
            SELECT * FROM {$this->table} WHERE user_id = ?
        ", [$userId]);
    }
    
    /**
     * Upload CV (create or update)
     * 
     * @param int $userId User ID
     * @param string $filePath File path
     * @return bool Success
     */
    public function upload(int $userId, string $filePath): bool {
        $existing = $this->findByUser($userId);
        
        if ($existing) {
            // Update existing
            return $this->query("
                UPDATE {$this->table} 
                SET file_path = ?, uploaded_at = NOW() 
                WHERE user_id = ?
            ", [$filePath, $userId]) !== false;
        } else {
            // Create new
            $this->create([
                'user_id' => $userId,
                'file_path' => $filePath,
                'uploaded_at' => date('Y-m-d H:i:s')
            ]);
            return true;
        }
    }
    
    /**
     * Delete CV by user
     * 
     * @param int $userId User ID
     * @return bool Success
     */
    public function deleteByUser(int $userId): bool {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }
}
