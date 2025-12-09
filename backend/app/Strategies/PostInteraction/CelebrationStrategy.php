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
                FROM PostInteraction 
                WHERE post_id = ? AND user_id = ?
            ");
            $stmt->execute([$postId, $userId]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existing) {
                if ($existing['type'] === 'celberation') {
                    $deleteStmt = $this->db->prepare("
                        DELETE FROM PostInteraction WHERE interaction_id = ?
                    ");
                    $deleteStmt->execute([$existing['interaction_id']]);
                    
                    return ['action' => 'removed', 'type' => 'celberation'];
                } else {
                    $updateStmt = $this->db->prepare("
                        UPDATE PostInteraction 
                        SET type = ?, created_at = NOW() 
                        WHERE interaction_id = ?
                    ");
                    $updateStmt->execute(['celberation', $existing['interaction_id']]);
                    
                    return ['action' => 'updated', 'type' => 'celberation', 'interaction_id' => $existing['interaction_id']];
                }
            }
            
            $insertStmt = $this->db->prepare("
                INSERT INTO PostInteraction (post_id, user_id, type, created_at)
                VALUES (?, ?, 'celberation', NOW())
            ");
            $insertStmt->execute([$postId, $userId]);
            
            return ['action' => 'added', 'type' => 'celberation', 'interaction_id' => $this->db->lastInsertId()];
            
        } catch (\Exception $e) {
            throw new \Exception("Failed to celebrate: " . $e->getMessage());
        }
    }
    
    public function getType(): string {
        return 'celberation';
    }
    
    public function canExecute(int $postId, int $userId): bool {
        return true;
    }
}
