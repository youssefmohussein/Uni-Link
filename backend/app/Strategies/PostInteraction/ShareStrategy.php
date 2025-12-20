<?php
namespace App\Strategies\PostInteraction;

use App\Strategies\Interfaces\PostInteractionStrategyInterface;
use App\Utils\Database;
use PDO;

/**
 * Share Strategy
 * 
 * Implements "Share" interaction for posts
 */
class ShareStrategy implements PostInteractionStrategyInterface {
    private PDO $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function execute(int $postId, int $userId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT interaction_id, type 
                FROM post_interactions 
                WHERE post_id = ? AND user_id = ?
            ");
            $stmt->execute([$postId, $userId]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existing && $existing['type'] === 'SHARE') {
                return ['action' => 'already_shared', 'type' => 'SHARE'];
            }
            
            if ($existing) {
                $updateStmt = $this->db->prepare("
                    UPDATE post_interactions 
                    SET type = ?, created_at = NOW() 
                    WHERE interaction_id = ?
                ");
                $updateStmt->execute(['SHARE', $existing['interaction_id']]);
                
                return ['action' => 'updated', 'type' => 'SHARE', 'interaction_id' => $existing['interaction_id']];
            }
            
            $insertStmt = $this->db->prepare("
                INSERT INTO post_interactions (post_id, user_id, type, created_at)
                VALUES (?, ?, 'SHARE', NOW())
            ");
            $insertStmt->execute([$postId, $userId]);
            
            return ['action' => 'added', 'type' => 'SHARE', 'interaction_id' => $this->db->lastInsertId()];
            
        } catch (\Exception $e) {
            throw new \Exception("Failed to share: " . $e->getMessage());
        }
    }
    
    public function getType(): string {
        return 'Share';
    }
    
    public function canExecute(int $postId, int $userId): bool {
        return true;
    }
}
