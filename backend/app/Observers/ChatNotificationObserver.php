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
class ChatNotificationObserver implements NotificationObserver
{
    private NotificationRepository $notificationRepo;

    public function __construct(NotificationRepository $notificationRepo)
    {
        $this->notificationRepo = $notificationRepo;
    }

    /**
     * Handle chat-related events and create notifications
     */
    public function update(string $eventType, array $payload): void
    {
        switch ($eventType) {
            case 'CHAT_MENTION':
                $this->handleChatMention($payload);
                break;

            case 'CHAT_MESSAGE':
                $this->handleChatMessage($payload);
                break;
        }
    }

    private function handleChatMention(array $payload): void
    {
        error_log("ChatNotificationObserver: handleChatMention called with payload: " . json_encode($payload));

        // Create notification for each mentioned user
        if (isset($payload['mentioned_users']) && is_array($payload['mentioned_users'])) {
            error_log("ChatNotificationObserver: Processing " . count($payload['mentioned_users']) . " mentioned users");

            foreach ($payload['mentioned_users'] as $userId) {
                try {
                    $notificationData = [
                        'user_id' => $userId,
                        'type' => 'CHAT_MENTION',
                        'title' => 'You were mentioned',
                        'message' => ($payload['sender_username'] ?? 'Someone') . " has mentioned you in " . ($payload['room_name'] ?? 'a chat room'),
                        'related_entity_type' => 'CHAT_MESSAGE',
                        'related_entity_id' => $payload['room_id'] ?? null
                    ];

                    error_log("ChatNotificationObserver: Creating notification for user_id: " . $userId);
                    error_log("ChatNotificationObserver: Notification data: " . json_encode($notificationData));

                    $notificationId = $this->notificationRepo->create($notificationData);

                    error_log("ChatNotificationObserver: Successfully created notification with ID: " . $notificationId);
                } catch (\Exception $e) {
                    error_log("ChatNotificationObserver: ERROR creating notification: " . $e->getMessage());
                    error_log("ChatNotificationObserver: Stack trace: " . $e->getTraceAsString());
                }
            }
        } else {
            error_log("ChatNotificationObserver: No mentioned_users in payload or not an array");
        }
    }

    private function handleChatMessage(array $payload): void
    {
        // This could be used for notifying room members of new messages
        // Currently handled by database triggers, but can be extended here
    }
}
