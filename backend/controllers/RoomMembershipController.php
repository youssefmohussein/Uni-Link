<?php

require_once __DIR__ . '/../utils/DbConnection.php';

class RoomMembershipController
{

    // ===============================
    // ➕ Join Room (password required)
    // ===============================
    public static function joinRoom()
    {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input || !isset($input['room_id'], $input['user_id'], $input['password'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields: room_id, user_id, password"
            ]);
            return;
        }

        $room_id = (int) $input['room_id'];
        $user_id = (int) $input['user_id'];
        $password = $input['password'];

        try {
            // check room
            $stmt = $pdo->prepare("SELECT password FROM project_rooms WHERE room_id = ?");
            $stmt->execute([$room_id]);
            $room = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$room || $room['password'] !== $password) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Invalid room ID or password"
                ]);
                return;
            }

            // check existing membership
            $stmt = $pdo->prepare("
                SELECT * FROM room_memberships WHERE room_id = ? AND user_id = ?
            ");
            $stmt->execute([$room_id, $user_id]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                echo json_encode([
                    "status" => "error",
                    "message" => "User already joined the room"
                ]);
                return;
            }

            // join
            $stmt = $pdo->prepare("
                INSERT INTO room_memberships (room_id, user_id, role)
                VALUES (?, ?, 'Member')
            ");
            $stmt->execute([$room_id, $user_id]);

            echo json_encode([
                "status" => "success",
                "message" => "Joined room successfully"
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    // ===============================
    // 👥 Get room members
    // ===============================
    public static function getRoomMembers()
    {
        global $pdo;

        if (!isset($_GET['room_id'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing room_id"
            ]);
            return;
        }

        $room_id = (int) $_GET['room_id'];

        try {
            $stmt = $pdo->prepare("
                SELECT rm.user_id, rm.role, u.username, u.email
                FROM room_memberships rm
                JOIN Users u ON rm.user_id = u.user_id
                WHERE rm.room_id = ?
            ");
            $stmt->execute([$room_id]);

            $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "count" => count($members),
                "data" => $members
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