<?php
namespace App\Models;

/**
 * ChatRoom Model
 * 
 * Represents a chat room for project collaboration
 * Part of the Chat Domain (UML Design)
 * Works with Mediator Pattern for message coordination
 */
class ChatRoom {
    private int $roomId;
    private string $name;
    private ?string $description;
    private ?string $passwordHash;
    private int $ownerId;
    private ?string $photoUrl;
    private bool $isPrivate;
    private ?string $createdAt;
    private ?string $updatedAt;
    
    // Related entities
    private array $members = [];
    private array $messages = [];
    
    public function __construct(array $data) {
        $this->roomId = $data['room_id'] ?? 0;
        $this->name = $data['name'] ?? '';
        $this->description = $data['description'] ?? null;
        $this->passwordHash = $data['password_hash'] ?? null;
        $this->ownerId = $data['owner_id'] ?? 0;
        $this->photoUrl = $data['photo_url'] ?? null;
        $this->isPrivate = (bool)($data['is_private'] ?? false);
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
    }
    
    // Getters
    public function getRoomId(): int { return $this->roomId; }
    public function getName(): string { return $this->name; }
    public function getDescription(): ?string { return $this->description; }
    public function getPasswordHash(): ?string { return $this->passwordHash; }
    public function getOwnerId(): int { return $this->ownerId; }
    public function getPhotoUrl(): ?string { return $this->photoUrl; }
    public function isPrivate(): bool { return $this->isPrivate; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    public function getMembers(): array { return $this->members; }
    public function getMessages(): array { return $this->messages; }
    
    // Setters
    public function setRoomId(int $roomId): void { $this->roomId = $roomId; }
    public function setName(string $name): void { $this->name = $name; }
    public function setDescription(?string $description): void { $this->description = $description; }
    public function setPasswordHash(?string $passwordHash): void { $this->passwordHash = $passwordHash; }
    public function setPhotoUrl(?string $photoUrl): void { $this->photoUrl = $photoUrl; }
    public function setMembers(array $members): void { $this->members = $members; }
    public function setMessages(array $messages): void { $this->messages = $messages; }
    
    /**
     * Business Logic: Check if room is password protected
     */
    public function isPasswordProtected(): bool {
        return !empty($this->passwordHash);
    }
    
    /**
     * Business Logic: Verify password
     */
    public function verifyPassword(string $password): bool {
        if (!$this->isPasswordProtected()) {
            return true;
        }
        return password_verify($password, $this->passwordHash);
    }
    
    /**
     * Business Logic: Check if user is owner
     */
    public function isOwner(int $userId): bool {
        return $this->ownerId === $userId;
    }
    
    /**
     * Business Logic: Check if user is member
     */
    public function isMember(int $userId): bool {
        foreach ($this->members as $member) {
            if (isset($member['user_id']) && $member['user_id'] === $userId) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Convert to array
     */
    public function toArray(): array {
        return [
            'room_id' => $this->roomId,
            'name' => $this->name,
            'description' => $this->description,
            'owner_id' => $this->ownerId,
            'photo_url' => $this->photoUrl,
            'is_private' => $this->isPrivate,
            'is_password_protected' => $this->isPasswordProtected(),
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'members' => $this->members,
            'messages' => $this->messages
        ];
    }
    
    /**
     * Convert to public array (without password hash)
     */
    public function toPublicArray(): array {
        $data = $this->toArray();
        unset($data['password_hash']);
        return $data;
    }
}
