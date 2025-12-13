<?php
namespace App\Strategies\PostInteraction;

use App\Strategies\Interfaces\PostInteractionStrategyInterface;
use App\Utils\Database;
use PDO;

/**
 * Save Strategy
 * 
 * Implements "Save" interaction for posts (bookmark)
 */
class SaveStrategy implements PostInteractionStrategyInterface {
    private PDO $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function execute(int $postId, int $userId): array {
        try {
            // Check if already saved
            $stmt = $this->db->prepare("
                SELECT saved_id 
                FROM saved_posts 
                WHERE post_id = ? AND user_id = ?
            ");
            $stmt->execute([$postId, $userId]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existing) {
                // Unsave
                $deleteStmt = $this->db->prepare("
                    DELETE FROM saved_posts WHERE saved_id = ?
                ");
                $deleteStmt->execute([$existing['saved_id']]);
                
                return [
                    'action' => 'removed',
                    'type' => 'Save',
                    'message' => 'Post removed from saved'
                ];
            }
            
            // Save post
            $insertStmt = $this->db->prepare("
                INSERT INTO saved_posts (post_id, user_id, created_at)
                VALUES (?, ?, NOW())
            ");
            $insertStmt->execute([$postId, $userId]);
            
            return [
                'action' => 'added',
                'type' => 'Save',
                'saved_id' => $this->db->lastInsertId(),
                'message' => 'Post saved successfully'
            ];
            
        } catch (\Exception $e) {
            throw new \Exception("Failed to save post: " . $e->getMessage());
        }
    }
    
    public function getType(): string {
        return 'Save';
    }
    
    public function canExecute(int $postId, int $userId): bool {
        return true;
    }
}
