<?php
namespace App\Strategies\PostInteraction;

use App\Strategies\Interfaces\PostInteractionStrategyInterface;
use App\Utils\Database;
use PDO;

/**
 * Celebration Strategy
 * 
 * Implements "Celebration" interaction for posts
 */
class CelebrationStrategy implements PostInteractionStrategyInterface {
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
            
            if ($existing) {
                if ($existing['type'] === 'CELEBRATE') {
                    $deleteStmt = $this->db->prepare("
                        DELETE FROM post_interactions WHERE interaction_id = ?
                    ");
                    $deleteStmt->execute([$existing['interaction_id']]);
                    
                    return ['action' => 'removed', 'type' => 'celebration'];
                } else {
                    $updateStmt = $this->db->prepare("
                        UPDATE post_interactions 
                        SET type = ?, created_at = NOW() 
                        WHERE interaction_id = ?
                    ");
                    $updateStmt->execute(['CELEBRATE', $existing['interaction_id']]);
                    
                    return ['action' => 'updated', 'type' => 'celebration', 'interaction_id' => $existing['interaction_id']];
                }
            }
            
            $insertStmt = $this->db->prepare("
                INSERT INTO post_interactions (post_id, user_id, type, created_at)
                VALUES (?, ?, 'CELEBRATE', NOW())
            ");
            $insertStmt->execute([$postId, $userId]);
            
            return ['action' => 'added', 'type' => 'celebration', 'interaction_id' => $this->db->lastInsertId()];
            
        } catch (\Exception $e) {
            throw new \Exception("Failed to celebrate: " . $e->getMessage());
        }
    }
    
    public function getType(): string {
        return 'celebration';
    }
    
    public function canExecute(int $postId, int $userId): bool {
        return true;
    }
}
