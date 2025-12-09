<?php
namespace App\Mediators;

use App\Mediators\Interfaces\MediatorInterface;
use App\Utils\Database;
use PDO;

/**
 * Project Room Mediator
 * 
 * Coordinates communication between room components:
 * - Room Chat
 * - Room Membership
 * - Notifications
 * 
 * Implements Mediator Pattern
 */
class ProjectRoomMediator implements MediatorInterface {
    private PDO $db;
    private NotificationMediator $notificationMediator;
    
    public function __construct(NotificationMediator $notificationMediator) {
        $this->db = Database::getInstance()->getConnection();
        $this->notificationMediator = $notificationMediator;
    }
    
    /**
     * Handle room-related events
     * 
     * @param object $sender Component triggering the event
     * @param string $event Event type
     * @param array $data Event data
     */
    public function notify(object $sender, string $event, array $data = []): void {
        switch ($event) {
            case 'chat.message':
                $this->handleChatMessage($data);
                break;
                
            case 'chat.mention':
                $this->handleChatMention($data);
                break;
                
            case 'member.added':
                $this->handleMemberAdded($data);
                break;
                
            case 'member.removed':
                $this->handleMemberRemoved($data);
                break;
                
            case 'room.updated':
                $this->handleRoomUpdated($data);
                break;
        }
    }
    
    /**
     * Handle chat message event
     * Notifies all room members except sender
     */
    private function handleChatMessage(array $data): void {
        try {
            // Get all room members
            $stmt = $this->db->prepare("
                SELECT rm.user_id, u.username
                FROM room_memberships rm
                JOIN Users u ON rm.user_id = u.user_id
                WHERE rm.room_id = ?
            ");
            $stmt->execute([$data['room_id']]);
            $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Notify each member except the sender
            foreach ($members as $member) {
                if ($member['user_id'] !== $data['sender_id']) {
                    $this->notificationMediator->notify($this, 'room.message', [
                        'user_id' => $member['user_id'],
                        'room_id' => $data['room_id'],
                        'room_name' => $data['room_name'],
                        'sender_name' => $data['sender_name']
                    ]);
                }
            }
        } catch (\Exception $e) {
            error_log("Failed to handle chat message: " . $e->getMessage());
        }
    }
    
    /**
     * Handle chat mention event
     * Notifies mentioned users
     */
    private function handleChatMention(array $data): void {
        $this->notificationMediator->notify($this, 'user.mentioned', [
            'mentioned_users' => $data['mentioned_users'],
            'author_id' => $data['author_id'],
            'author_name' => $data['author_name'],
            'content_id' => $data['message_id'],
            'content_type' => 'room_chat'
        ]);
    }
    
    /**
     * Handle member added event
     * Notifies room owner and the new member
     */
    private function handleMemberAdded(array $data): void {
        try {
            // Get room details
            $stmt = $this->db->prepare("
                SELECT owner_id, name FROM project_rooms WHERE room_id = ?
            ");
            $stmt->execute([$data['room_id']]);
            $room = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($room) {
                // Notify room owner
                $this->notificationMediator->notify($this, 'room.joined', [
                    'room_owner_id' => $room['owner_id'],
                    'user_id' => $data['user_id'],
                    'user_name' => $data['user_name'],
                    'room_id' => $data['room_id'],
                    'room_name' => $room['name']
                ]);
            }
        } catch (\Exception $e) {
            error_log("Failed to handle member added: " . $e->getMessage());
        }
    }
    
    /**
     * Handle member removed event
     */
    private function handleMemberRemoved(array $data): void {
        // Could notify the removed user or room owner
        // Implementation depends on business requirements
    }
    
    /**
     * Handle room updated event
     * Notifies all room members
     */
    private function handleRoomUpdated(array $data): void {
        try {
            // Get all room members
            $stmt = $this->db->prepare("
                SELECT user_id FROM room_memberships WHERE room_id = ?
            ");
            $stmt->execute([$data['room_id']]);
            $members = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Notify each member
            foreach ($members as $userId) {
                if ($userId !== $data['updated_by']) {
                    $this->createRoomUpdateNotification($userId, $data);
                }
            }
        } catch (\Exception $e) {
            error_log("Failed to handle room updated: " . $e->getMessage());
        }
    }
    
    /**
     * Create room update notification
     */
    private function createRoomUpdateNotification(int $userId, array $data): void {
        // This would create a notification about room updates
        // Implementation depends on notification system
    }
}
