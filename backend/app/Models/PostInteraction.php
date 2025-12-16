<?php
namespace App\Models;

/**
 * PostInteraction Model
 * 
 * Represents user interactions with posts (like, love, celebrate, save, share)
 * Part of the Posts Domain (UML Design)
 * Works with Strategy Pattern for different interaction types
 */
class PostInteraction {
    private int $interactionId;
    private int $postId;
    private int $userId;
    private string $type;
    private ?string $createdAt;
    
    public function __construct(array $data) {
        $this->interactionId = $data['interaction_id'] ?? 0;
        $this->postId = $data['post_id'] ?? 0;
        $this->userId = $data['user_id'] ?? 0;
        $this->type = $data['type'] ?? 'LIKE';
        $this->createdAt = $data['created_at'] ?? null;
    }
    
    // Getters
    public function getInteractionId(): int { return $this->interactionId; }
    public function getPostId(): int { return $this->postId; }
    public function getUserId(): int { return $this->userId; }
    public function getType(): string { return $this->type; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    
    // Setters
    public function setInteractionId(int $interactionId): void { $this->interactionId = $interactionId; }
    
    /**
     * Business Logic: Check if interaction is a reaction (like, love, celebrate)
     */
    public function isReaction(): bool {
        return in_array($this->type, ['LIKE', 'LOVE', 'CELEBRATE']);
    }
    
    /**
     * Business Logic: Check if interaction is a save
     */
    public function isSave(): bool {
        return $this->type === 'SAVE';
    }
    
    /**
     * Business Logic: Check if interaction is a share
     */
    public function isShare(): bool {
        return $this->type === 'SHARE';
    }
    
    /**
     * Convert to array
     */
    public function toArray(): array {
        return [
            'interaction_id' => $this->interactionId,
            'post_id' => $this->postId,
            'user_id' => $this->userId,
            'type' => $this->type,
            'created_at' => $this->createdAt
        ];
    }
}
