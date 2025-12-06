<?php

require_once __DIR__ . '/../utils/DbConnection.php';

class ProjectRoomController
{

    // ===============================
    // 🆕 Create a Room
    // ===============================
    public static function createRoom()
    {
        global $pdo;

        // Determine input source: JSON or POST (form-data)
        $input = [];
        $jsonData = json_decode(file_get_contents("php://input"), true);

        if (!empty($_POST)) {
            $input = $_POST;
        } elseif (is_array($jsonData)) {
            $input = $jsonData;
        }

        // Validate required fields
        $missingFields = [];
        if (!isset($input['owner_id']) || $input['owner_id'] === '') {
            $missingFields[] = 'owner_id';
        }
        if (!isset($input['name']) || trim($input['name']) === '') {
            $missingFields[] = 'name';
        }
        if (!isset($input['password']) || trim($input['password']) === '') {
            $missingFields[] = 'password';
        }

        if (!empty($missingFields)) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields: " . implode(', ', $missingFields),
                "debug_input" => $input,
                "debug_post" => $_POST,
                "debug_files" => array_keys($_FILES)
            ]);
            return;
        }

        $owner_id = (int) $input['owner_id'];
        $name = $input['name'];
        $description = $input['description'] ?? null;
        $password = $input['password'];

        // Handle Photo Upload
        $photo_url = $input['photo_url'] ?? null; // Fallback to URL if provided

        if (isset($_FILES['room_photo']) && $_FILES['room_photo']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = mime_content_type($_FILES['room_photo']['tmp_name']);

            if (!in_array($fileType, $allowedTypes)) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Invalid file type. Only JPG, PNG, GIF, and WebP are allowed."
                ]);
                return;
            }

            // Ensure upload directory exists
            $uploadDir = __DIR__ . '/../uploads/room_photos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Generate unique filename
            $extension = pathinfo($_FILES['room_photo']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('room_', true) . '.' . $extension;
            $targetPath = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['room_photo']['tmp_name'], $targetPath)) {
                // Store relative path (accessible via web)
                // Assuming index.php routing or web server points /uploads to root or backend/uploads
                // Let's us standard relative from API root for now, user might need to prepend base URL in frontend
                // Or we store 'uploads/room_photos/filename' and frontend serves it from API_BASE_URL + path
                $photo_url = 'uploads/room_photos/' . $filename;
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Failed to upload photo"
                ]);
                return;
            }
        }

        // Hash the password for security
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("
                INSERT INTO project_rooms (owner_id, name, description, photo_url, password)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$owner_id, $name, $description, $photo_url, $password_hash]);

            $room_id = $pdo->lastInsertId();

            // owner becomes admin
            $stmt = $pdo->prepare("
                INSERT INTO room_memberships (room_id, user_id, role)
                VALUES (?, ?, 'Admin')
            ");
            $stmt->execute([$room_id, $owner_id]);

            echo json_encode([
                "status" => "success",
                "message" => "Room created successfully",
                "room_id" => $room_id,
                "photo_url" => $photo_url
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    // ===============================
    // 📜 Get All Rooms
    // ===============================
    public static function getAllRooms()
    {
        global $pdo;

        try {
            $stmt = $pdo->prepare("
                SELECT r.*, u.username as creator_name 
                FROM project_rooms r
                JOIN Users u ON r.owner_id = u.user_id
                ORDER BY r.created_at DESC
            ");
            $stmt->execute();
            $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "count" => count($rooms),
                "data" => $rooms
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    // ===============================
    // 🔍 Get Room by ID
    // ===============================
    public static function getRoom()
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
                SELECT r.*, u.username as creator_name
                FROM project_rooms r
                JOIN Users u ON r.owner_id = u.user_id
                WHERE r.room_id = ?
            ");
            $stmt->execute([$room_id]);

            $room = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$room) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Room not found"
                ]);
                return;
            }

            echo json_encode([
                "status" => "success",
                "data" => $room
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    // ===============================
    // 👤 Get User's Rooms (where user is a member)
    // ===============================
    public static function getUserRooms()
    {
        global $pdo;

        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Get user ID from session
        if (!isset($_SESSION['user']['id'])) {
            echo json_encode([
                "status" => "error",
                "message" => "User not authenticated"
            ]);
            return;
        }

        $user_id = (int) $_SESSION['user']['id'];

        try {
            $stmt = $pdo->prepare("
                SELECT DISTINCT r.*, u.username as creator_name 
                FROM project_rooms r
                JOIN Users u ON r.owner_id = u.user_id
                JOIN room_memberships rm ON r.room_id = rm.room_id
                WHERE rm.user_id = ?
                ORDER BY r.created_at DESC
            ");
            $stmt->execute([$user_id]);
            $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "count" => count($rooms),
                "data" => $rooms
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