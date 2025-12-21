<?php
namespace App\Repositories;

/**
 * Project Room Repository
 * 
 * Handles database operations for project rooms
 */
class ProjectRoomRepository extends BaseRepository
{
    protected string $table = 'chat_rooms';
    protected string $primaryKey = 'room_id';

    /**
     * Find record by ID
     */
    public function find(int $id, bool $includeSoftDeleted = false): ?array
    {
        $sql = "SELECT pr.*, pr.name as room_name, u.username as owner_name 
                FROM {$this->table} pr 
                LEFT JOIN users u ON pr.owner_id = u.user_id 
                WHERE pr.{$this->primaryKey} = ?";
        return $this->queryOne($sql, [$id]);
    }

    /**
     * Find all records
     */
    public function findAll(?int $limit = null, int $offset = 0, string $orderBy = ''): array
    {
        $sql = "SELECT pr.*, pr.name as room_name, u.username as owner_name 
                FROM {$this->table} pr
                LEFT JOIN users u ON pr.owner_id = u.user_id";

        if ($orderBy) {
            $sql .= " ORDER BY pr.{$orderBy}";
        }

        if ($limit !== null) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }

        return $this->query($sql);
    }

    /**
     * Find user rooms
     * 
     * @param int $userId User ID
     * @return array Array of rooms
     */
    public function findUserRooms(int $userId): array
    {
        return $this->query("
            SELECT pr.*, pr.name as room_name, rm.role as user_role, u.username as owner_name
            FROM {$this->table} pr
            JOIN room_members rm ON pr.room_id = rm.room_id
            LEFT JOIN users u ON pr.owner_id = u.user_id
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
    public function findMembers(int $roomId): array
    {
        return $this->query("
            SELECT u.user_id, u.username, u.profile_picture as profile_image, rm.role
            FROM room_members rm
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
    public function addMember(int $roomId, int $userId, string $role = 'Member'): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO room_members (room_id, user_id, role, joined_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$roomId, $userId, $role]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Check if user is member
     * 
     * @param int $roomId Room ID
     * @param int $userId User ID
     * @return bool
     */
    public function isMember(int $roomId, int $userId): bool
    {
        $result = $this->queryOne("
            SELECT 1 FROM room_members 
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
    public function verifyPassword(int $roomId, string $password): bool
    {
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
    public function deleteMemberships(int $roomId): int
    {
        $stmt = $this->db->prepare("DELETE FROM room_members WHERE room_id = ?");
        $stmt->execute([$roomId]);
        return $stmt->rowCount();
    }
    /**
     * Check if user is room admin or owner
     * 
     * @param int $roomId Room ID
     * @param int $userId User ID
     * @return bool
     */
    public function isUserRoomAdmin(int $roomId, int $userId): bool
    {
        // 1. Check if user is owner
        $room = $this->queryOne("SELECT 1 FROM {$this->table} WHERE room_id = ? AND owner_id = ?", [$roomId, $userId]);
        if ($room)
            return true;

        // 2. Check if user has Admin role in room_members
        $member = $this->queryOne("
            SELECT 1 FROM room_members 
            WHERE room_id = ? AND user_id = ? AND role = 'Admin'
        ", [$roomId, $userId]);

        return $member !== null;
    }

    /**
     * Get total count of project rooms
     * 
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->count();
    }
}
