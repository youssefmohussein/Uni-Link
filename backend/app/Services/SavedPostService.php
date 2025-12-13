<?php
namespace App\Services;

use App\Repositories\PostRepository;

/**
 * Saved Post Service
 * 
 * Business logic for saved posts
 */
class SavedPostService extends BaseService {
    private PostRepository $postRepo;
    
    public function __construct(PostRepository $postRepo) {
        $this->postRepo = $postRepo;
    }
    
    /**
     * Save post for user
     * 
     * @param int $userId User ID
     * @param int $postId Post ID
     * @return bool Success status
     */
    public function savePost(int $userId, int $postId): bool {
        // Verify post exists
        if (!$this->postRepo->exists($postId)) {
            throw new \Exception('Post not found', 404);
        }
        
        $sql = "INSERT INTO SavedPost (user_id, post_id, saved_at) VALUES (?, ?, ?)";
        
        try {
            $this->postRepo->execute($sql, [$userId, $postId, date('Y-m-d H:i:s')]);
            return true;
        } catch (\Exception $e) {
            // Handle duplicate entry
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                return true; // Already saved
            }
            throw $e;
        }
    }
    
    /**
     * Unsave post for user
     * 
     * @param int $userId User ID
     * @param int $postId Post ID
     * @return bool Success status
     */
    public function unsavePost(int $userId, int $postId): bool {
        $sql = "DELETE FROM SavedPost WHERE user_id = ? AND post_id = ?";
        return $this->postRepo->execute($sql, [$userId, $postId]) > 0;
    }
    
    /**
     * Get user's saved posts
     * 
     * @param int $userId User ID
     * @return array Array of saved posts
     */
    public function getUserSavedPosts(int $userId): array {
        $sql = "
            SELECT p.*, u.username as author_name, sp.saved_at
            FROM SavedPost sp
            JOIN Post p ON sp.post_id = p.post_id
            LEFT JOIN Users u ON p.user_id = u.user_id
            WHERE sp.user_id = ?
            ORDER BY sp.saved_at DESC
        ";
        return $this->postRepo->query($sql, [$userId]);
    }
    
    /**
     * Check if post is saved by user
     * 
     * @param int $userId User ID
     * @param int $postId Post ID
     * @return bool Is saved
     */
    public function isSaved(int $userId, int $postId): bool {
        $sql = "SELECT 1 FROM SavedPost WHERE user_id = ? AND post_id = ? LIMIT 1";
        $result = $this->postRepo->queryOne($sql, [$userId, $postId]);
        return $result !== null;
    }
}
