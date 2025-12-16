<?php
namespace App\Observers;

use App\Repositories\NotificationRepository;

/**
 * PostNotificationObserver
 * 
 * Observer Pattern Implementation
 * Handles post-related notifications (likes, comments, shares)
 * Part of the Notification System (UML Design)
 */
class PostNotificationObserver implements NotificationObserver {
    private NotificationRepository $notificationRepo;
    
    public function __construct(NotificationRepository $notificationRepo) {
        $this->notificationRepo = $notificationRepo;
    }
    
    /**
     * Handle post-related events and create notifications
     */
    public function update(string $eventType, array $payload): void {
        switch ($eventType) {
            case 'POST_LIKED':
            case 'POST_LOVED':
            case 'POST_CELEBRATED':
                $this->handlePostReaction($eventType, $payload);
                break;
                
            case 'POST_COMMENTED':
                $this->handlePostComment($payload);
                break;
                
            case 'POST_SHARED':
                $this->handlePostShare($payload);
                break;
        }
    }
    
    private function handlePostReaction(string $reactionType, array $payload): void {
        // Don't notify if user reacted to their own post
        if ($payload['post_author_id'] === $payload['user_id']) {
            return;
        }
        
        $reactionName = strtolower(str_replace('POST_', '', $reactionType));
        
        $this->notificationRepo->create([
            'user_id' => $payload['post_author_id'],
            'type' => 'POST_LIKE',
            'title' => 'Post Reaction',
            'message' => "Someone reacted to your post with {$reactionName}",
            'related_entity_type' => 'POST',
            'related_entity_id' => $payload['post_id']
        ]);
    }
    
    private function handlePostComment(array $payload): void {
        // Don't notify if user commented on their own post
        if ($payload['post_author_id'] === $payload['user_id']) {
            return;
        }
        
        $this->notificationRepo->create([
            'user_id' => $payload['post_author_id'],
            'type' => 'POST_COMMENT',
            'title' => 'New Comment',
            'message' => 'Someone commented on your post',
            'related_entity_type' => 'POST',
            'related_entity_id' => $payload['post_id']
        ]);
    }
    
    private function handlePostShare(array $payload): void {
        // Don't notify if user shared their own post
        if ($payload['post_author_id'] === $payload['user_id']) {
            return;
        }
        
        $this->notificationRepo->create([
            'user_id' => $payload['post_author_id'],
            'type' => 'POST_SHARE',
            'title' => 'Post Shared',
            'message' => 'Someone shared your post',
            'related_entity_type' => 'POST',
            'related_entity_id' => $payload['post_id']
        ]);
    }
}
