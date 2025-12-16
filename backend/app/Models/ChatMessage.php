<?php
namespace App\Models;

/**
 * ChatMessage Model
 * 
 * Represents a message in a chat room
 * Part of the Chat Domain (UML Design)
 * Processed through Chain of Responsibility pattern
 */
class ChatMessage {
    private int $messageId;
    private int $roomId;
    private int $senderId;
    private string $content;
    private string $messageType;
    private ?string $filePath;
    private ?string $createdAt;
    private ?string $updatedAt;
    
    // Related entities
    private array $mentions = [];
    
    public function __construct(array $data) {
        $this->messageId = $data['message_id'] ?? 0;
        $this->roomId = $data['room_id'] ?? 0;
        $this->senderId = $data['sender_id'] ?? 0;
        $this->content = $data['content'] ?? '';
        $this->messageType = $data['message_type'] ?? 'TEXT';
        $this->filePath = $data['file_path'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
    }
    
    // Getters
    public function getMessageId(): int { return $this->messageId; }
    public function getRoomId(): int { return $this->roomId; }
    public function getSenderId(): int { return $this->senderId; }
    public function getContent(): string { return $this->content; }
    public function getMessageType(): string { return $this->messageType; }
    public function getFilePath(): ?string { return $this->filePath; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    public function getMentions(): array { return $this->mentions; }
    
    // Setters
    public function setMessageId(int $messageId): void { $this->messageId = $messageId; }
    public function setContent(string $content): void { $this->content = $content; }
    public function setMentions(array $mentions): void { $this->mentions = $mentions; }
    
    /**
     * Business Logic: Check if message is a text message
     */
    public function isText(): bool {
        return $this->messageType === 'TEXT';
    }
    
    /**
     * Business Logic: Check if message has a file attachment
     */
    public function hasFile(): bool {
        return !empty($this->filePath);
    }
    
    /**
     * Business Logic: Check if message is a system message
     */
    public function isSystemMessage(): bool {
        return $this->messageType === 'SYSTEM';
    }
    
    /**
     * Business Logic: Extract mentions from content
     * Returns array of usernames mentioned (e.g., @username)
     */
    public function extractMentions(): array {
        preg_match_all('/@(\w+)/', $this->content, $matches);
        return $matches[1] ?? [];
    }
    
    /**
     * Business Logic: Check if message contains mentions
     */
    public function hasMentions(): bool {
        return count($this->extractMentions()) > 0;
    }
    
    /**
     * Convert to array
     */
    public function toArray(): array {
        return [
            'message_id' => $this->messageId,
            'room_id' => $this->roomId,
            'sender_id' => $this->senderId,
            'content' => $this->content,
            'message_type' => $this->messageType,
            'file_path' => $this->filePath,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'mentions' => $this->mentions
        ];
    }
}
