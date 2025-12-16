<?php
namespace App\Models;

/**
 * PostMedia Model
 * 
 * Represents media attached to a post (images, videos, files)
 * Part of the Posts Domain (UML Design)
 */
class PostMedia {
    private int $mediaId;
    private int $postId;
    private string $type;
    private string $path;
    private ?string $filename;
    private ?string $mimeType;
    private ?int $size;
    private ?string $createdAt;
    
    public function __construct(array $data) {
        $this->mediaId = $data['media_id'] ?? 0;
        $this->postId = $data['post_id'] ?? 0;
        $this->type = $data['type'] ?? 'FILE';
        $this->path = $data['path'] ?? '';
        $this->filename = $data['filename'] ?? null;
        $this->mimeType = $data['mime_type'] ?? null;
        $this->size = $data['size'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
    }
    
    // Getters
    public function getMediaId(): int { return $this->mediaId; }
    public function getPostId(): int { return $this->postId; }
    public function getType(): string { return $this->type; }
    public function getPath(): string { return $this->path; }
    public function getFilename(): ?string { return $this->filename; }
    public function getMimeType(): ?string { return $this->mimeType; }
    public function getSize(): ?int { return $this->size; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    
    /**
     * Business Logic: Check if media is an image
     */
    public function isImage(): bool {
        return $this->type === 'IMAGE' || 
               ($this->mimeType && str_starts_with($this->mimeType, 'image/'));
    }
    
    /**
     * Business Logic: Check if media is a video
     */
    public function isVideo(): bool {
        return $this->type === 'VIDEO' || 
               ($this->mimeType && str_starts_with($this->mimeType, 'video/'));
    }
    
    /**
     * Business Logic: Get human-readable file size
     */
    public function getFormattedSize(): string {
        if (!$this->size) return 'Unknown';
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->size;
        $unitIndex = 0;
        
        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }
        
        return round($size, 2) . ' ' . $units[$unitIndex];
    }
    
    /**
     * Convert to array
     */
    public function toArray(): array {
        return [
            'media_id' => $this->mediaId,
            'post_id' => $this->postId,
            'type' => $this->type,
            'path' => $this->path,
            'filename' => $this->filename,
            'mime_type' => $this->mimeType,
            'size' => $this->size,
            'formatted_size' => $this->getFormattedSize(),
            'created_at' => $this->createdAt
        ];
    }
}
