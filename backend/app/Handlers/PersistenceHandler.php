<?php
namespace App\Handlers;

use App\Repositories\ChatRepository;
use App\Services\NotificationService;

/**
 * PersistenceHandler
 * 
 * Chain of Responsibility Pattern Implementation
 * Saves the message to the database (final handler in chain)
 * Part of the Chat System (UML Design)
 */
class PersistenceHandler extends MessageHandler
{
    private ChatRepository $chatRepo;
    private NotificationService $notificationService;

    public function __construct(ChatRepository $chatRepo, NotificationService $notificationService)
    {
        $this->chatRepo = $chatRepo;
        $this->notificationService = $notificationService;
    }

    public function handle(array $message): array
    {
        try {
            // Save message to database
            $messageId = $this->chatRepo->createMessage([
                'room_id' => $message['room_id'],
                'sender_id' => $message['sender_id'],
                'content' => $message['content'] ?? '',
                'message_type' => $message['message_type'] ?? 'TEXT',
                'file_path' => $message['file_path'] ?? null
            ]);

            if (!$messageId) {
                return ['error' => 'Failed to save message'];
            }

            $message['message_id'] = $messageId;

            // Save mentions if any
            if (isset($message['mentioned_users']) && !empty($message['mentioned_users'])) {
                foreach ($message['mentioned_users'] as $userId) {
                    $this->chatRepo->createMention([
                        'message_id' => $messageId,
                        'user_id' => $userId
                    ]);
                }

                // Notify mentioned users
                $roomName = $this->chatRepo->getRoomName($message['room_id']);
                $this->notificationService->notifyAll('CHAT_MENTION', [
                    'message_id' => $messageId,
                    'room_id' => $message['room_id'],
                    'room_name' => $roomName,
                    'mentioned_users' => $message['mentioned_users'],
                    'sender_username' => $message['sender_username'] ?? 'Someone'
                ]);
            }

            // Return success with message ID
            return [
                'success' => true,
                'message_id' => $messageId,
                'message' => $message
            ];

        } catch (\Exception $e) {
            error_log("PersistenceHandler failed: " . $e->getMessage());
            return ['error' => 'Failed to save message: ' . $e->getMessage()];
        }
    }
}
