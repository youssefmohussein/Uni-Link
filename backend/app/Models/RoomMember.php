<?php
namespace App\Models;

/**
 * RoomMember Model
 * 
 * Represents a user's membership in a chat room
 * Part of the Chat Domain (UML Design)
 */
class RoomMember {
    private int $membershipId;
    private int $roomId;
    private int $userId;
    private string $role;
    private ?string $joinedAt;
    
    public function __construct(array $data) {
        $this->membershipId = $data['membership_id'] ?? 0;
        $this->roomId = $data['room_id'] ?? 0;
        $this->userId = $data['user_id'] ?? 0;
        $this->role = $data['role'] ?? 'MEMBER';
        $this->joinedAt = $data['joined_at'] ?? null;
    }
    
    // Getters
    public function getMembershipId(): int { return $this->membershipId; }
    public function getRoomId(): int { return $this->roomId; }
    public function getUserId(): int { return $this->userId; }
    public function getRole(): string { return $this->role; }
    public function getJoinedAt(): ?string { return $this->joinedAt; }
    
    // Setters
    public function setMembershipId(int $membershipId): void { $this->membershipId = $membershipId; }
    public function setRole(string $role): void { $this->role = $role; }
    
    /**
     * Business Logic: Check if member is admin
     */
    public function isAdmin(): bool {
        return $this->role === 'ADMIN';
    }
    
    /**
     * Business Logic: Check if member is moderator
     */
    public function isModerator(): bool {
        return $this->role === 'MODERATOR';
    }
    
    /**
     * Business Logic: Check if member has moderation privileges
     */
    public function canModerate(): bool {
        return in_array($this->role, ['ADMIN', 'MODERATOR']);
    }
    
    /**
     * Convert to array
     */
    public function toArray(): array {
        return [
            'membership_id' => $this->membershipId,
            'room_id' => $this->roomId,
            'user_id' => $this->userId,
            'role' => $this->role,
            'joined_at' => $this->joinedAt
        ];
    }
}
