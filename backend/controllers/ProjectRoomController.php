<?php

require_once __DIR__ . '/../utils/DbConnection.php';

class ProjectRoomController
{
    // ============================================
    // SETUP (Run once to create tables)
    // ============================================
    public static function initDB()
    {
        global $pdo;
        try {
            // ProjectRooms Table
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS ProjectRooms (
                    room_id INT AUTO_INCREMENT PRIMARY KEY,
                    room_name VARCHAR(255) NOT NULL,
                    description TEXT,
                    created_by INT NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (created_by) REFERENCES Users(user_id) ON DELETE CASCADE
                )
            ");

            // RoomMessages Table
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS RoomMessages (
                    message_id INT AUTO_INCREMENT PRIMARY KEY,
                    room_id INT NOT NULL,
                    user_id INT NOT NULL,
                    content TEXT NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (room_id) REFERENCES ProjectRooms(room_id) ON DELETE CASCADE,
                    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
                )
            ");

            echo json_encode(["status" => "success", "message" => "Tables created successfully"]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => "DB Error: " . $e->getMessage()]);
        }
    }

    // ============================================
    // ROOMS
    // ============================================

    public static function createRoom()
    {
        global $pdo;
        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input['room_name'], $input['created_by'])) {
            echo json_encode(["status" => "error", "message" => "Missing required fields"]);
            return;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO ProjectRooms (room_name, description, created_by) VALUES (?, ?, ?)");
            $stmt->execute([$input['room_name'], $input['description'] ?? '', $input['created_by']]);

            echo json_encode([
                "status" => "success",
                "message" => "Room created",
                "room_id" => $pdo->lastInsertId()
            ]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public static function getAllRooms()
    {
        global $pdo;
        try {
            $stmt = $pdo->query("
                SELECT r.*, u.username as creator_name 
                FROM ProjectRooms r
                JOIN Users u ON r.created_by = u.user_id
                ORDER BY r.created_at DESC
            ");
            echo json_encode(["status" => "success", "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public static function getRoomById($room_id)
    {
        global $pdo;
        try {
            $stmt = $pdo->prepare("SELECT * FROM ProjectRooms WHERE room_id = ?");
            $stmt->execute([$room_id]);
            $room = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($room) {
                echo json_encode(["status" => "success", "data" => $room]);
            } else {
                echo json_encode(["status" => "error", "message" => "Room not found"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    // ============================================
    // CHAT
    // ============================================

    public static function sendMessage()
    {
        global $pdo;
        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input['room_id'], $input['user_id'], $input['content'])) {
            echo json_encode(["status" => "error", "message" => "Missing required fields"]);
            return;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO RoomMessages (room_id, user_id, content) VALUES (?, ?, ?)");
            $stmt->execute([$input['room_id'], $input['user_id'], $input['content']]);

            echo json_encode(["status" => "success", "message" => "Message sent"]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public static function getRoomMessages($room_id)
    {
        global $pdo;
        // Optional: Support fetching only messages after a certain time/ID for efficiency
        $after_id = isset($_GET['after_id']) ? (int) $_GET['after_id'] : 0;

        try {
            $stmt = $pdo->prepare("
                SELECT m.*, u.username, u.profile_image 
                FROM RoomMessages m
                JOIN Users u ON m.user_id = u.user_id
                WHERE m.room_id = ? AND m.message_id > ?
                ORDER BY m.created_at ASC
            ");
            $stmt->execute([$room_id, $after_id]);

            echo json_encode(["status" => "success", "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
}
?>