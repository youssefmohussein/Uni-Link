<?php
namespace App\Mediators;

use App\Mediators\Interfaces\MediatorInterface;
use App\Utils\Database;
use PDO;

/**
 * Notification Mediator
 * 
 * Coordinates notification creation across different system events
 * Implements Mediator Pattern to decouple components
 */
class NotificationMediator implements MediatorInterface {
    private PDO $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Handle notification events
     * 
     * @param object $sender Component triggering the event
     * @param string $event Event type
     * @param array $data Event data
     */
    public function notify(object $sender, string $event, array $data = []): void {
        switch ($event) {
            case 'post.commented':
                $this->handlePostComment($data);
                break;
                
            case 'user.mentioned':
                $this->handleUserMention($data);
                break;
                
            case 'project.reviewed':
                $this->handleProjectReview($data);
                break;
                
            case 'room.joined':
                $this->handleRoomJoin($data);
                break;
                
            case 'room.message':
                $this->handleRoomMessage($data);
                break;
                
            case 'post.interaction':
                $this->handlePostInteraction($data);
                break;
        }
    }
    
    /**
     * Handle post comment notification
     */
    private function handlePostComment(array $data): void {
        // Don't notify if user commented on their own post
        if ($data['post_author_id'] === $data['commenter_id']) {
            return;
        }
        
        $this->createNotification([
            'user_id' => $data['post_author_id'],
            'type' => 'comment',
            'message' => "{$data['commenter_name']} commented on your post",
            'related_id' => $data['post_id'],
            'related_type' => 'post'
        ]);
    }
    
    /**
     * Handle user mention notification
     */
    private function handleUserMention(array $data): void {
        foreach ($data['mentioned_users'] as $userId) {
            // Don't notify if user mentioned themselves
            if ($userId === $data['author_id']) {
                continue;
            }
            
            $this->createNotification([
                'user_id' => $userId,
                'type' => 'mention',
                'message' => "{$data['author_name']} mentioned you",
                'related_id' => $data['content_id'],
                'related_type' => $data['content_type']
            ]);
        }
    }
    
    /**
     * Handle project review notification
     */
    private function handleProjectReview(array $data): void {
        $statusMessage = match($data['status']) {
            'Approved' => 'approved',
            'Rejected' => 'rejected',
            default => 'reviewed'
        };
        
        $this->createNotification([
            'user_id' => $data['student_id'],
            'type' => 'project_review',
            'message' => "Your project '{$data['project_title']}' has been {$statusMessage}",
            'related_id' => $data['project_id'],
            'related_type' => 'project'
        ]);
    }
    
    /**
     * Handle room join notification
     */
    private function handleRoomJoin(array $data): void {
        // Notify room owner
        if ($data['room_owner_id'] !== $data['user_id']) {
            $this->createNotification([
                'user_id' => $data['room_owner_id'],
                'type' => 'room_join',
                'message' => "{$data['user_name']} joined your room '{$data['room_name']}'",
                'related_id' => $data['room_id'],
                'related_type' => 'room'
            ]);
        }
    }
    
    /**
     * Handle room message notification
     */
    private function handleRoomMessage(array $data): void {
        $this->createNotification([
            'user_id' => $data['user_id'],
            'type' => 'room_message',
            'message' => "New message in '{$data['room_name']}' from {$data['sender_name']}",
            'related_id' => $data['room_id'],
            'related_type' => 'room'
        ]);
    }
    
    /**
     * Handle post interaction notification
     */
    private function handlePostInteraction(array $data): void {
        // Don't notify if user interacted with their own post
        if ($data['post_author_id'] === $data['user_id']) {
            return;
        }
        
        $interactionText = match($data['interaction_type']) {
            'Like' => 'liked',
            'Love' => 'loved',
            default => 'reacted to'
        };
        
        $this->createNotification([
            'user_id' => $data['post_author_id'],
            'type' => 'post_interaction',
            'message' => "{$data['user_name']} {$interactionText} your post",
            'related_id' => $data['post_id'],
            'related_type' => 'post'
        ]);
    }
    
    /**
     * Create a notification in the database
     * Note: This assumes a notifications table exists
     */
    private function createNotification(array $data): void {
        try {
            // Check if notifications table exists, if not, just log
            $stmt = $this->db->prepare("
                SHOW TABLES LIKE 'notifications'
            ");
            $stmt->execute();
            
            if ($stmt->rowCount() === 0) {
                // Table doesn't exist yet, just log for now
                error_log("Notification would be created: " . json_encode($data));
                return;
            }
            
            $stmt = $this->db->prepare("
                INSERT INTO notifications (user_id, type, message, related_id, related_type, created_at, is_read)
                VALUES (?, ?, ?, ?, ?, NOW(), 0)
            ");
            
            $stmt->execute([
                $data['user_id'],
                $data['type'],
                $data['message'],
                $data['related_id'] ?? null,
                $data['related_type'] ?? null
            ]);
        } catch (\Exception $e) {
            // Log error but don't throw - notifications shouldn't break main functionality
            error_log("Failed to create notification: " . $e->getMessage());
        }
    }
}
