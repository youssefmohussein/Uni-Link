<?php
namespace App\Handlers;

use App\Repositories\UserRepository;
use App\Services\NotificationService;

/**
 * MentionHandler
 * 
 * Chain of Responsibility Pattern Implementation
 * Processes @mentions in messages and creates mention records
 * Part of the Chat System (UML Design)
 */
class MentionHandler extends MessageHandler
{
    private UserRepository $userRepo;
    private NotificationService $notificationService;

    public function __construct(UserRepository $userRepo, NotificationService $notificationService)
    {
        $this->userRepo = $userRepo;
        $this->notificationService = $notificationService;
    }

    public function handle(array $message): array
    {
        // Fetch sender's username for notifications
        if (isset($message['sender_id'])) {
            $sender = $this->userRepo->find($message['sender_id']);
            $message['sender_username'] = $sender ? $sender['username'] : 'Someone';
            error_log("MentionHandler: Sender username: " . $message['sender_username']);
        }

        // Extract mentions from content
        if (isset($message['content'])) {
            error_log("MentionHandler: Processing message content: " . $message['content']);

            // Support alphanumeric, dots, hyphens and underscores in mentions
            preg_match_all('/@([a-zA-Z0-9._-]+)/', $message['content'], $matches);
            $mentionedUsernames = array_unique($matches[1] ?? []);

            error_log("MentionHandler: Found " . count($mentionedUsernames) . " potential mentions: " . json_encode($mentionedUsernames));

            if (!empty($mentionedUsernames)) {
                $mentionedUsers = [];

                // Find user IDs for mentioned usernames
                foreach ($mentionedUsernames as $username) {
                    error_log("MentionHandler: Looking up username: " . $username);

                    // Try to find the user. Database collation usually handles case-insensitivity.
                    $user = $this->userRepo->findByUsername($username);
                    if ($user) {
                        $mentionedUsers[] = (int) $user['user_id'];
                        error_log("MentionHandler: Found user_id " . $user['user_id'] . " for username: " . $username);
                    } else {
                        error_log("MentionHandler: Username not found: " . $username);
                    }
                }

                // Store mentioned user IDs in message data
                $message['mentioned_users'] = $mentionedUsers;
                error_log("MentionHandler: Final mentioned_users array: " . json_encode($mentionedUsers));
            } else {
                error_log("MentionHandler: No mentions found in message");
            }
        }

        // Pass to next handler
        if ($this->next) {
            return $this->next->handle($message);
        }

        return $message;
    }
}
