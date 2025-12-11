<?php
namespace App\Repositories;

/**
 * Post Repository
 * 
 * Handles database operations for posts
 */
class PostRepository extends BaseRepository {
    protected string $table = 'posts';
    protected string $primaryKey = 'post_id';
    
    /**
     * Find posts by user
     * 
     * @param int $userId User ID
     * @return array Array of posts
     */
    public function findByUser(int $userId): array {
        return $this->query("
            SELECT p.*, u.username, u.profile_image,
                   COUNT(DISTINCT c.comment_id) as comment_count
            FROM {$this->table} p
            LEFT JOIN users u ON p.user_id = u.user_id
            LEFT JOIN comments c ON p.post_id = c.post_id
            WHERE p.user_id = ?
            GROUP BY p.post_id
            ORDER BY p.created_at DESC
        ", [$userId]);
    }
    
    /**
     * Find posts by category
     * 
     * @param string $category Category name
     * @return array Array of posts
     */
    public function findByCategory(string $category): array {
        return $this->query("
            SELECT p.*, u.username, u.profile_image,
                   COUNT(DISTINCT c.comment_id) as comment_count
            FROM {$this->table} p
            LEFT JOIN users u ON p.user_id = u.user_id
            LEFT JOIN comments c ON p.post_id = c.post_id
            WHERE p.category = ?
            GROUP BY p.post_id
            ORDER BY p.created_at DESC
        ", [$category]);
    }
    
    /**
     * Search posts
     * 
     * @param string $query Search query
     * @return array Array of posts
     */
    public function search(string $query): array {
        $searchTerm = "%{$query}%";
        return $this->query("
            SELECT p.*, u.username, u.profile_image
            FROM {$this->table} p
            LEFT JOIN users u ON p.user_id = u.user_id
            WHERE p.content LIKE ? OR p.category LIKE ?
            ORDER BY p.created_at DESC
        ", [$searchTerm, $searchTerm]);
    }
    
    /**
     * Get post with media
     * 
     * @param int $postId Post ID
     * @return array|null Post with media
     */
    public function getWithMedia(int $postId): ?array {
        $post = $this->find($postId);
        
        if (!$post) {
            return null;
        }
        
        // Get media
        $media = $this->query("
            SELECT * FROM postmedia WHERE post_id = ?
        ", [$postId]);
        
        $post['media'] = $media;
        
        return $post;
    }
    
    /**
     * Get all posts with user info
     * 
     * @param int|null $limit Limit
     * @param int $offset Offset
     * @return array Array of posts
     */
    public function getAllWithUserInfo(?int $limit = null, int $offset = 0): array {
        $sql = "
            SELECT p.*, u.username, u.profile_image,
                   COUNT(DISTINCT c.comment_id) as comment_count
            FROM {$this->table} p
            LEFT JOIN users u ON p.user_id = u.user_id
            LEFT JOIN comments c ON p.post_id = c.post_id
            GROUP BY p.post_id
            ORDER BY p.created_at DESC
        ";
        
        if ($limit !== null) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }
        
        return $this->query($sql);
    }
}
