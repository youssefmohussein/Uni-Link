<?php
namespace App\Models;

/**
 * Notification Model
 * 
 * Represents a notification for a user
 * Part of the Notification Domain (UML Design)
 * Created by Observer Pattern implementations
 */
class Notification {
    private int $notificationId;
    private int $userId;
    private string $type;
    private string $title;
    private string $message;
    private ?string $relatedEntityType;
    private ?int $relatedEntityId;
    private bool $isRead;
    private ?string $createdAt;
    
    public function __construct(array $data) {
        $this->notificationId = $data['notification_id'] ?? 0;
        $this->userId = $data['user_id'] ?? 0;
        $this->type = $data['type'] ?? 'SYSTEM';
        $this->title = $data['title'] ?? '';
        $this->message = $data['message'] ?? '';
        $this->relatedEntityType = $data['related_entity_type'] ?? null;
        $this->relatedEntityId = $data['related_entity_id'] ?? null;
        $this->isRead = (bool)($data['is_read'] ?? false);
        $this->createdAt = $data['created_at'] ?? null;
    }
    
    // Getters
    public function getNotificationId(): int { return $this->notificationId; }
    public function getUserId(): int { return $this->userId; }
    public function getType(): string { return $this->type; }
    public function getTitle(): string { return $this->title; }
    public function getMessage(): string { return $this->message; }
    public function getRelatedEntityType(): ?string { return $this->relatedEntityType; }
    public function getRelatedEntityId(): ?int { return $this->relatedEntityId; }
    public function isRead(): bool { return $this->isRead; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    
    // Setters
    public function setNotificationId(int $notificationId): void { $this->notificationId = $notificationId; }
    public function markAsRead(): void { $this->isRead = true; }
    public function markAsUnread(): void { $this->isRead = false; }
    
    /**
     * Business Logic: Check if notification is about a post
     */
    public function isPostNotification(): bool {
        return in_array($this->type, ['POST_LIKE', 'POST_COMMENT', 'POST_SHARE']);
    }
    
    /**
     * Business Logic: Check if notification is about a project
     */
    public function isProjectNotification(): bool {
        return in_array($this->type, ['PROJECT_REVIEW', 'PROJECT_APPROVED', 'PROJECT_REJECTED', 'PROJECT_GRADED']);
    }
    
    /**
     * Business Logic: Check if notification is about a chat
     */
    public function isChatNotification(): bool {
        return in_array($this->type, ['CHAT_MENTION', 'CHAT_MESSAGE']);
    }
    
    /**
     * Convert to array
     */
    public function toArray(): array {
        return [
            'notification_id' => $this->notificationId,
            'user_id' => $this->userId,
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'related_entity_type' => $this->relatedEntityType,
            'related_entity_id' => $this->relatedEntityId,
            'is_read' => $this->isRead,
            'created_at' => $this->createdAt
        ];
    }
}
