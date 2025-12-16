<?php
namespace App\Models;

/**
 * Comment Model
 * 
 * Represents a comment on a post
 * Part of the Posts Domain (UML Design)
 */
class Comment {
    private int $commentId;
    private int $postId;
    private int $userId;
    private string $content;
    private ?int $parentCommentId;
    private ?string $createdAt;
    private ?string $updatedAt;
    
    public function __construct(array $data) {
        $this->commentId = $data['comment_id'] ?? 0;
        $this->postId = $data['post_id'] ?? 0;
        $this->userId = $data['user_id'] ?? 0;
        $this->content = $data['content'] ?? '';
        $this->parentCommentId = $data['parent_comment_id'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
    }
    
    // Getters
    public function getCommentId(): int { return $this->commentId; }
    public function getPostId(): int { return $this->postId; }
    public function getUserId(): int { return $this->userId; }
    public function getContent(): string { return $this->content; }
    public function getParentCommentId(): ?int { return $this->parentCommentId; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    
    // Setters
    public function setCommentId(int $commentId): void { $this->commentId = $commentId; }
    public function setContent(string $content): void { $this->content = $content; }
    
    /**
     * Business Logic: Check if this is a reply to another comment
     */
    public function isReply(): bool {
        return $this->parentCommentId !== null;
    }
    
    /**
     * Convert to array
     */
    public function toArray(): array {
        return [
            'comment_id' => $this->commentId,
            'post_id' => $this->postId,
            'user_id' => $this->userId,
            'content' => $this->content,
            'parent_comment_id' => $this->parentCommentId,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}
