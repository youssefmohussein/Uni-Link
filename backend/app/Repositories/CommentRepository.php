<?php
namespace App\Repositories;

/**
 * Comment Repository
 * 
 * Handles database operations for comments
 */
class CommentRepository extends BaseRepository {
    protected string $table = 'comments';
    protected string $primaryKey = 'comment_id';
    
    /**
     * Find comments by post
     * 
     * @param int $postId Post ID
     * @return array Array of comments
     */
    public function findByPost(int $postId): array {
        return $this->query("
            SELECT c.*, u.username, u.profile_picture as profile_image
            FROM {$this->table} c
            LEFT JOIN users u ON c.user_id = u.user_id
            WHERE c.post_id = ?
            ORDER BY c.created_at ASC
        ", [$postId]);
    }
    
    /**
     * Create comment with notification
     * 
     * @param array $data Comment data
     * @return int Comment ID
     */
    public function createWithNotification(array $data): int {
        $this->beginTransaction();
        
        try {
            $commentId = $this->create($data);
            
            // Get post author for notification
            $post = $this->queryOne("SELECT user_id FROM posts WHERE post_id = ?", [$data['post_id']]);
            
            if ($post && $post['user_id'] != $data['user_id']) {
                // Create notification for post author
                $this->query("
                    INSERT INTO notifications (user_id, type, content, created_at)
                    VALUES (?, 'comment', ?, NOW())
                ", [
                    $post['user_id'],
                    "New comment on your post"
                ]);
            }
            
            $this->commit();
            return $commentId;
            
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
}
