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
class MentionHandler extends MessageHandler {
    private UserRepository $userRepo;
    private NotificationService $notificationService;
    
    public function __construct(UserRepository $userRepo, NotificationService $notificationService) {
        $this->userRepo = $userRepo;
        $this->notificationService = $notificationService;
    }
    
    public function handle(array $message): array {
        // Extract mentions from content
        if (isset($message['content'])) {
            preg_match_all('/@(\w+)/', $message['content'], $matches);
            $mentionedUsernames = $matches[1] ?? [];
            
            if (!empty($mentionedUsernames)) {
                $mentionedUsers = [];
                
                // Find user IDs for mentioned usernames
                foreach ($mentionedUsernames as $username) {
                    $user = $this->userRepo->findByUsername($username);
                    if ($user) {
                        $mentionedUsers[] = $user['user_id'];
                    }
                }
                
                // Store mentioned user IDs in message data
                $message['mentioned_users'] = $mentionedUsers;
            }
        }
        
        // Pass to next handler
        if ($this->next) {
            return $this->next->handle($message);
        }
        
        return $message;
    }
}
