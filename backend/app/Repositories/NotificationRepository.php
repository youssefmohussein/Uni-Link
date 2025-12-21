<?php
namespace App\Repositories;

/**
 * NotificationRepository
 * 
 * Repository Pattern Implementation
 * Handles all database operations for notifications
 * Part of the Notification System (UML Design)
 */
class NotificationRepository extends BaseRepository
{
    protected string $table = 'notifications';
    protected string $primaryKey = 'notification_id';

    /**
     * Get all notifications for a user
     */
    public function getUserNotifications(int $userId, int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC 
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get unread notifications for a user
     */
    public function getUnreadNotifications(int $userId): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = :user_id AND is_read = 0 
                ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get unread notification count for a user
     */
    public function getUnreadCount(int $userId): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE user_id = :user_id AND is_read = 0";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int) ($result['count'] ?? 0);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId): bool
    {
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE notification_id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $notificationId, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(int $userId): bool
    {
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE user_id = :user_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Delete old notifications (older than specified days)
     */
    public function deleteOldNotifications(int $days = 30): bool
    {
        $sql = "DELETE FROM {$this->table} 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':days', $days, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Get notifications by type
     */
    public function getNotificationsByType(int $userId, string $type): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = :user_id AND type = :type 
                ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':type', $type, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Fix duplicate mentions (Temporary)
     */
    public function fixDuplicateMentions(): void
    {
        try {
            $this->db->exec("DROP TRIGGER IF EXISTS notify_user_on_mention");
            $this->db->exec("DELETE FROM {$this->table} WHERE message = 'You were mentioned in a chat message'");
        } catch (\Exception $e) {
            // Ignore errors
        }
    }
}
