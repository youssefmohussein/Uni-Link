<?php
namespace App\Repositories;

/**
 * Project Room Repository
 * 
 * Handles database operations for project rooms
 */
class ProjectRoomRepository extends BaseRepository {
    protected string $table = 'projectrooms';
    protected string $primaryKey = 'room_id';
    
    /**
     * Find user rooms
     * 
     * @param int $userId User ID
     * @return array Array of rooms
     */
    public function findUserRooms(int $userId): array {
        return $this->query("
            SELECT pr.*, rm.role as user_role
            FROM {$this->table} pr
            JOIN room_memberships rm ON pr.room_id = rm.room_id
            WHERE rm.user_id = ?
            ORDER BY pr.created_at DESC
        ", [$userId]);
    }
    
    /**
     * Find room members
     * 
     * @param int $roomId Room ID
     * @return array Array of members
     */
    public function findMembers(int $roomId): array {
        return $this->query("
            SELECT u.user_id, u.username, u.profile_image, rm.role
            FROM room_memberships rm
            JOIN users u ON rm.user_id = u.user_id
            WHERE rm.room_id = ?
        ", [$roomId]);
    }
    
    /**
     * Add member to room
     * 
     * @param int $roomId Room ID
     * @param int $userId User ID
     * @param string $role Member role
     * @return int Insert ID
     */
    public function addMember(int $roomId, int $userId, string $role = 'Member'): int {
        $stmt = $this->db->prepare("
            INSERT INTO room_memberships (room_id, user_id, role, joined_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$roomId, $userId, $role]);
        return (int)$this->db->lastInsertId();
    }
    
    /**
     * Check if user is member
     * 
     * @param int $roomId Room ID
     * @param int $userId User ID
     * @return bool
     */
    public function isMember(int $roomId, int $userId): bool {
        $result = $this->queryOne("
            SELECT 1 FROM room_memberships 
            WHERE room_id = ? AND user_id = ?
        ", [$roomId, $userId]);
        
        return $result !== null;
    }
    
    /**
     * Verify room password
     * 
     * @param int $roomId Room ID
     * @param string $password Plain password
     * @return bool
     */
    public function verifyPassword(int $roomId, string $password): bool {
        $room = $this->find($roomId);
        
        if (!$room || !isset($room['password_hash'])) {
            return false;
        }
        
        return password_verify($password, $room['password_hash']);
    }
    
    /**
     * Delete all memberships for a room
     * 
     * @param int $roomId Room ID
     * @return int Number of deleted memberships
     */
    public function deleteMemberships(int $roomId): int {
        $stmt = $this->db->prepare("DELETE FROM room_memberships WHERE room_id = ?");
        $stmt->execute([$roomId]);
        return $stmt->rowCount();
    }
}
