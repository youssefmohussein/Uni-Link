<?php
namespace App\Repositories;

/**
 * Announcement Repository
 * 
 * Data access layer for Announcement entity
 */
class AnnouncementRepository extends BaseRepository {
    protected string $table = 'announcements';
    protected string $primaryKey = 'announcement_id';
    
    /**
     * Find recent announcements
     * 
     * @param int $limit Number of announcements
     * @return array Array of recent announcements
     */
    public function findRecent(int $limit = 10): array {
        $sql = "
            SELECT a.*, u.username as author_name, u.role as author_role
            FROM announcements a
            LEFT JOIN users u ON a.author_id = u.user_id
            ORDER BY a.created_at DESC
            LIMIT ?
        ";
        return $this->query($sql, [$limit]);
    }
    
    /**
     * Find announcements by role
     * 
     * @param string $role Target role
     * @return array Array of announcements
     */
    public function findByRole(string $role): array {
        $sql = "
            SELECT a.*, u.username as author_name, u.role as author_role
            FROM announcements a
            LEFT JOIN users u ON a.author_id = u.user_id
            WHERE a.target_role = ? OR a.target_role = 'All'
            ORDER BY a.created_at DESC
        ";
        return $this->query($sql, [$role]);
    }
    
    /**
     * Find announcements by author
     * 
     * @param int $authorId Author user ID
     * @return array Array of announcements
     */
    public function findByAuthor(int $authorId): array {
        return $this->findBy('author_id', $authorId);
    }
    
    /**
     * Get announcement with author details
     * 
     * @param int $announcementId Announcement ID
     * @return array|null Announcement with author data
     */
    public function getWithAuthor(int $announcementId): ?array {
        $sql = "
            SELECT a.*, u.username as author_name, u.role as author_role, u.profile_image
            FROM announcements a
            LEFT JOIN users u ON a.author_id = u.user_id
            WHERE a.announcement_id = ?
        ";
        return $this->queryOne($sql, [$announcementId]);
    }
}
