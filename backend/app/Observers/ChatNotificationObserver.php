<?php
namespace App\Observers;

use App\Repositories\NotificationRepository;

/**
 * ChatNotificationObserver
 * 
 * Observer Pattern Implementation
 * Handles chat-related notifications (mentions, new messages)
 * Part of the Notification System (UML Design)
 */
class ChatNotificationObserver implements NotificationObserver {
    private NotificationRepository $notificationRepo;
    
    public function __construct(NotificationRepository $notificationRepo) {
        $this->notificationRepo = $notificationRepo;
    }
    
    /**
     * Handle chat-related events and create notifications
     */
    public function update(string $eventType, array $payload): void {
        switch ($eventType) {
            case 'CHAT_MENTION':
                $this->handleChatMention($payload);
                break;
                
            case 'CHAT_MESSAGE':
                $this->handleChatMessage($payload);
                break;
        }
    }
    
    private function handleChatMention(array $payload): void {
        // Create notification for each mentioned user
        if (isset($payload['mentioned_users']) && is_array($payload['mentioned_users'])) {
            foreach ($payload['mentioned_users'] as $userId) {
                $this->notificationRepo->create([
                    'user_id' => $userId,
                    'type' => 'CHAT_MENTION',
                    'title' => 'You were mentioned',
                    'message' => "You were mentioned in {$payload['room_name']}",
                    'related_entity_type' => 'CHAT_MESSAGE',
                    'related_entity_id' => $payload['message_id']
                ]);
            }
        }
    }
    
    private function handleChatMessage(array $payload): void {
        // This could be used for notifying room members of new messages
        // Currently handled by database triggers, but can be extended here
    }
}
