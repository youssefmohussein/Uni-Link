<?php

require_once __DIR__ . '/../utils/DbConnection.php';

class RoomChatController {

    // ===============================
    // 💬 Send message
    // ===============================
    public static function sendMessage() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input || !isset($input['room_id'], $input['sender_id'], $input['content'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields: room_id, sender_id, content"
            ]);
            return;
        }

        $room_id = (int)$input['room_id'];
        $sender_id = (int)$input['sender_id'];
        $content = $input['content'];

        // detect mentions
        preg_match_all('/@(\w+)/', $content, $matches);
        $mentioned = array_unique($matches[1]);
        $has_mentions = count($mentioned) > 0;

        try {
            // insert message
            $stmt = $pdo->prepare("
                INSERT INTO room_chats (room_id, sender_id, content, has_mentions)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$room_id, $sender_id, $content, $has_mentions]);

            $message_id = $pdo->lastInsertId();

            // save mentions
            if ($has_mentions) {
                foreach ($mentioned as $username) {
                    $u = $pdo->prepare("SELECT user_id FROM Users WHERE username = ?");
                    $u->execute([$username]);
                    $user = $u->fetch(PDO::FETCH_ASSOC);

                    if ($user) {
                        $m = $pdo->prepare("
                            INSERT INTO chat_mentions (message_id, mentioned_user_id)
                            VALUES (?, ?)
                        ");
                        $m->execute([$message_id, $user['user_id']]);
                    }
                }
            }

            echo json_encode([
                "status" => "success",
                "message" => "Message sent",
                "message_id" => $message_id
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    // ===============================
    // 📥 Get messages
    // ===============================
    public static function getMessages() {
        global $pdo;

        if (!isset($_GET['room_id'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing room_id"
            ]);
            return;
        }

        $room_id = (int)$_GET['room_id'];

        try {
            $stmt = $pdo->prepare("
                SELECT c.*, u.username 
                FROM room_chats c
                JOIN Users u ON c.sender_id = u.user_id
                WHERE c.room_id = ?
                ORDER BY c.created_at ASC
            ");
            $stmt->execute([$room_id]);
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "count" => count($messages),
                "data" => $messages
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }
}
?>
