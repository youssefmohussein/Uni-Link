<?php
namespace App\Models;

/**
 * Post Model
 * 
 * Represents a post in the social feed
 * Part of the Posts Domain (UML Design)
 */
class Post {
    private int $postId;
    private int $authorId;
    private string $content;
    private string $visibility;
    private string $status;
    private ?string $createdAt;
    private ?string $updatedAt;
    
    // Related entities (loaded separately)
    private array $media = [];
    private array $comments = [];
    private array $interactions = [];
    
    public function __construct(array $data) {
        $this->postId = $data['post_id'] ?? 0;
        $this->authorId = $data['author_id'] ?? 0;
        $this->content = $data['content'] ?? '';
        $this->visibility = $data['visibility'] ?? 'PUBLIC';
        $this->status = $data['status'] ?? 'PUBLISHED';
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
    }
    
    // Getters
    public function getPostId(): int { return $this->postId; }
    public function getAuthorId(): int { return $this->authorId; }
    public function getContent(): string { return $this->content; }
    public function getVisibility(): string { return $this->visibility; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    public function getMedia(): array { return $this->media; }
    public function getComments(): array { return $this->comments; }
    public function getInteractions(): array { return $this->interactions; }
    
    // Setters
    public function setPostId(int $postId): void { $this->postId = $postId; }
    public function setContent(string $content): void { $this->content = $content; }
    public function setVisibility(string $visibility): void { $this->visibility = $visibility; }
    public function setStatus(string $status): void { $this->status = $status; }
    public function setMedia(array $media): void { $this->media = $media; }
    public function setComments(array $comments): void { $this->comments = $comments; }
    public function setInteractions(array $interactions): void { $this->interactions = $interactions; }
    
    /**
     * Business Logic: Publish the post
     */
    public function publish(): void {
        $this->status = 'PUBLISHED';
    }
    
    /**
     * Business Logic: Archive the post
     */
    public function archive(): void {
        $this->status = 'ARCHIVED';
    }
    
    /**
     * Business Logic: Check if user can view this post
     */
    public function canBeViewedBy(int $userId, ?int $facultyId = null, ?int $majorId = null): bool {
        if ($this->status !== 'PUBLISHED') {
            return $this->authorId === $userId;
        }
        
        switch ($this->visibility) {
            case 'PUBLIC':
                return true;
            case 'PRIVATE':
                return $this->authorId === $userId;
            case 'FACULTY':
                return $facultyId !== null;
            case 'MAJOR':
                return $majorId !== null;
            default:
                return false;
        }
    }
    
    /**
     * Convert to array
     */
    public function toArray(): array {
        return [
            'post_id' => $this->postId,
            'author_id' => $this->authorId,
            'content' => $this->content,
            'visibility' => $this->visibility,
            'status' => $this->status,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'media' => $this->media,
            'comments' => $this->comments,
            'interactions' => $this->interactions
        ];
    }
}
