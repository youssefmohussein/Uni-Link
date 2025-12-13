<?php
namespace App\Strategies\PostInteraction;

use App\Strategies\Interfaces\PostInteractionStrategyInterface;
use App\Utils\Database;
use PDO;

/**
 * Love Strategy
 * 
 * Implements "Love" interaction for posts
 */
class LoveStrategy implements PostInteractionStrategyInterface {
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
                if ($existing['type'] === 'Love') {
                    $deleteStmt = $this->db->prepare("
                        DELETE FROM PostInteraction WHERE interaction_id = ?
                    ");
                    $deleteStmt->execute([$existing['interaction_id']]);
                    
                    return ['action' => 'removed', 'type' => 'Love'];
                } else {
                    $updateStmt = $this->db->prepare("
                        UPDATE PostInteraction 
                        SET type = ?, created_at = NOW() 
                        WHERE interaction_id = ?
                    ");
                    $updateStmt->execute(['Love', $existing['interaction_id']]);
                    
                    return ['action' => 'updated', 'type' => 'Love', 'interaction_id' => $existing['interaction_id']];
                }
            }
            
            $insertStmt = $this->db->prepare("
                INSERT INTO PostInteraction (post_id, user_id, type, created_at)
                VALUES (?, ?, 'Love', NOW())
            ");
            $insertStmt->execute([$postId, $userId]);
            
            return ['action' => 'added', 'type' => 'Love', 'interaction_id' => $this->db->lastInsertId()];
            
        } catch (\Exception $e) {
            throw new \Exception("Failed to execute love: " . $e->getMessage());
        }
    }
    
    public function getType(): string {
        return 'Love';
    }
    
    public function canExecute(int $postId, int $userId): bool {
        return true;
    }
}
