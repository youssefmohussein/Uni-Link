<?php
namespace App\Models;

/**
 * Mention Model
 * 
 * Represents a user mention in a chat message
 * Part of the Chat Domain (UML Design)
 */
class Mention {
    private int $mentionId;
    private int $messageId;
    private int $userId;
    private ?string $createdAt;
    
    public function __construct(array $data) {
        $this->mentionId = $data['mention_id'] ?? 0;
        $this->messageId = $data['message_id'] ?? 0;
        $this->userId = $data['user_id'] ?? 0;
        $this->createdAt = $data['created_at'] ?? null;
    }
    
    // Getters
    public function getMentionId(): int { return $this->mentionId; }
    public function getMessageId(): int { return $this->messageId; }
    public function getUserId(): int { return $this->userId; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    
    // Setters
    public function setMentionId(int $mentionId): void { $this->mentionId = $mentionId; }
    
    /**
     * Convert to array
     */
    public function toArray(): array {
        return [
            'mention_id' => $this->mentionId,
            'message_id' => $this->messageId,
            'user_id' => $this->userId,
            'created_at' => $this->createdAt
        ];
    }
}
