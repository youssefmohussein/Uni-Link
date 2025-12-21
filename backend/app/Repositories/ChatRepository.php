<?php
namespace App\Repositories;

use App\Utils\Encryption;

/**
 * ChatRepository
 * 
 * Repository Pattern Implementation
 * Handles all database operations for chat messages and mentions
 * Part of the Chat System (UML Design)
 */
class ChatRepository extends BaseRepository
{
    protected string $table = 'chat_messages';
    protected string $primaryKey = 'message_id';

    /**
     * Create a new chat message
     */
    public function createMessage(array $data): int
    {
        if (isset($data['content']) && !empty($data['content'])) {
            $data['content'] = Encryption::encrypt($data['content']);
        }
        return $this->create($data);
    }

    /**
     * Get messages for a room
     */
    public function getRoomMessages(int $roomId, int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT m.*, u.username, u.profile_picture 
                FROM {$this->table} m
                LEFT JOIN users u ON m.sender_id = u.user_id
                WHERE m.room_id = :room_id 
                ORDER BY m.created_at DESC 
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':room_id', $roomId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Decrypt messages
        foreach ($messages as &$msg) {
            if (!empty($msg['content'])) {
                $decrypted = Encryption::decrypt($msg['content']);
                // If decryption succeeds, use it. If fails (legacy message), use original.
                if ($decrypted !== null) {
                    $msg['content'] = $decrypted;
                }
            }
        }

        return array_reverse($messages);
    }

    /**
     * Create a mention
     */
    public function createMention(array $data): int
    {
        $sql = "INSERT INTO chat_mentions (message_id, user_id) VALUES (:message_id, :user_id)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':message_id', $data['message_id'], \PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $data['user_id'], \PDO::PARAM_INT);
        $stmt->execute();

        return (int) $this->db->lastInsertId();
    }

    /**
     * Get mentions for a message
     */
    public function getMessageMentions(int $messageId): array
    {
        $sql = "SELECT m.*, u.username 
                FROM chat_mentions m
                LEFT JOIN users u ON m.user_id = u.user_id
                WHERE m.message_id = :message_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':message_id', $messageId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get room name
     */
    public function getRoomName(int $roomId): string
    {
        $sql = "SELECT name FROM chat_rooms WHERE room_id = :room_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':room_id', $roomId, \PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['name'] ?? 'Unknown Room';
    }

    /**
     * Delete message
     */
    public function deleteMessage(int $messageId): bool
    {
        return $this->delete($messageId);
    }

    /**
     * Get message count for a room
     */
    public function getRoomMessageCount(int $roomId): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE room_id = :room_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':room_id', $roomId, \PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int) ($result['count'] ?? 0);
    }
}
