<?php
namespace App\Strategies\PostInteraction;

use App\Strategies\Interfaces\PostInteractionStrategyInterface;
use App\Utils\Database;
use PDO;

/**
 * Like Strategy
 * 
 * Implements "Like" interaction for posts
 * Strategy Pattern implementation
 */
class LikeStrategy implements PostInteractionStrategyInterface {
    private PDO $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Execute like interaction
     * 
     * @param int $postId Post ID
     * @param int $userId User ID
     * @return array Result
     */
    public function execute(int $postId, int $userId): array {
        try {
            // Check if already liked
            $stmt = $this->db->prepare("
                SELECT interaction_id, type 
                FROM PostInteraction 
                WHERE post_id = ? AND user_id = ?
            ");
            $stmt->execute([$postId, $userId]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existing) {
                if ($existing['type'] === 'Like') {
                    // Unlike (remove)
                    $deleteStmt = $this->db->prepare("
                        DELETE FROM PostInteraction WHERE interaction_id = ?
                    ");
                    $deleteStmt->execute([$existing['interaction_id']]);
                    
                    return [
                        'action' => 'removed',
                        'type' => 'Like',
                        'message' => 'Like removed'
                    ];
                } else {
                    // Update to Like
                    $updateStmt = $this->db->prepare("
                        UPDATE PostInteraction 
                        SET type = ?, created_at = NOW() 
                        WHERE interaction_id = ?
                    ");
                    $updateStmt->execute(['Like', $existing['interaction_id']]);
                    
                    return [
                        'action' => 'updated',
                        'type' => 'Like',
                        'interaction_id' => $existing['interaction_id'],
                        'message' => 'Reaction updated to Like'
                    ];
                }
            }
            
            // Create new like
            $insertStmt = $this->db->prepare("
                INSERT INTO PostInteraction (post_id, user_id, type, created_at)
                VALUES (?, ?, 'Like', NOW())
            ");
            $insertStmt->execute([$postId, $userId]);
            
            return [
                'action' => 'added',
                'type' => 'Like',
                'interaction_id' => $this->db->lastInsertId(),
                'message' => 'Post liked'
            ];
            
        } catch (\Exception $e) {
            throw new \Exception("Failed to execute like: " . $e->getMessage());
        }
    }
    
    /**
     * Get interaction type
     * 
     * @return string
     */
    public function getType(): string {
        return 'Like';
    }
    
    /**
     * Check if can execute
     * 
     * @param int $postId Post ID
     * @param int $userId User ID
     * @return bool
     */
    public function canExecute(int $postId, int $userId): bool {
        // Anyone can like a post
        return true;
    }
}
