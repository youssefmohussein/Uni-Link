<?php
namespace App\Repositories;

/**
 * Admin Repository
 * 
 * Data access layer for Admin entity
 */
class AdminRepository extends BaseRepository {
    protected string $table = 'admins';
    protected string $primaryKey = 'admin_id';
    
    /**
     * Find admins by status
     * 
     * @param string $status Admin status
     * @return array Array of admins
     */
    public function findByStatus(string $status): array {
        return $this->findBy('status', $status);
    }
    
    /**
     * Get admin with user information
     * 
     * @param int $adminId Admin ID
     * @return array|null Admin with user data
     */
    public function getWithUserInfo(int $adminId): ?array {
        $sql = "
            SELECT a.*, u.username, u.email, u.phone, u.profile_image, u.bio
            FROM admins a
            JOIN users u ON a.user_id = u.user_id
            WHERE a.admin_id = ?
        ";
        return $this->queryOne($sql, [$adminId]);
    }
    
    /**
     * Get all admins with user information
     * 
     * @return array Array of admins
     */
    public function getAllWithUserInfo(): array {
        $sql = "
            SELECT a.*, u.username, u.email, u.phone, u.profile_image
            FROM admins a
            JOIN users u ON a.user_id = u.user_id
            ORDER BY a.created_at DESC
        ";
        return $this->query($sql);
    }
    
    /**
     * Update admin status
     * 
     * @param int $adminId Admin ID
     * @param string $status New status
     * @return bool Success status
     */
    public function updateStatus(int $adminId, string $status): bool {
        return $this->update($adminId, ['status' => $status]);
    }
}
