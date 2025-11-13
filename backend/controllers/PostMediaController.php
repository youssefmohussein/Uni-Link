<?php
require_once __DIR__ . '/../utils/DbConnection.php';

class PostMediaController  {

    public static function getAllMedia() {
        global $pdo;

        try {
            $stmt = $pdo->query("SELECT * FROM Media ORDER BY uploaded_at DESC");
            $media = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "count" => count($media),
                "data" => $media
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    public static function getMediaById($media_id) {
        global $pdo;

        try {
            $stmt = $pdo->prepare("SELECT * FROM Media WHERE media_id = ?");
            $stmt->execute([$media_id]);
            $media = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$media) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Media not found"
                ]);
                return;
            }

            echo json_encode([
                "status" => "success",
                "data" => $media
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    public static function addMedia() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input || !isset($input['post_id'], $input['media_type'], $input['media_path'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields"
            ]);
            return;
        }

        $post_id = (int)$input['post_id'];
        $media_type = trim($input['media_type']);
        $media_path = trim($input['media_path']);

        $validTypes = ['Image', 'Video'];
        if (!in_array($media_type, $validTypes)) {
            echo json_encode([
                "status" => "error",
                "message" => "Invalid media_type"
            ]);
            return;
        }

        try {
            // Check if post exists to avoid FK error
            $checkPost = $pdo->prepare("SELECT * FROM Post WHERE post_id = ?");
            $checkPost->execute([$post_id]);
            if (!$checkPost->fetch()) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Post not found"
                ]);
                return;
            }

            $stmt = $pdo->prepare("
                INSERT INTO Media (post_id, media_type, media_path, uploaded_at)
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$post_id, $media_type, $media_path]);

            echo json_encode([
                "status" => "success",
                "message" => "Media added successfully",
                "media_id" => $pdo->lastInsertId()
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    public static function updateMedia() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input || !isset($input['media_id'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing media_id"
            ]);
            return;
        }

        $media_id = (int)$input['media_id'];
        $post_id = isset($input['post_id']) ? (int)$input['post_id'] : null;
        $media_type = isset($input['media_type']) ? trim($input['media_type']) : null;
        $media_path = isset($input['media_path']) ? trim($input['media_path']) : null;

        try {
            // Check if media exists
            $check = $pdo->prepare("SELECT * FROM Media WHERE media_id = ?");
            $check->execute([$media_id]);
            if (!$check->fetch()) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Media not found"
                ]);
                return;
            }

            // Optional: check if post exists if post_id is provided
            if ($post_id !== null) {
                $checkPost = $pdo->prepare("SELECT * FROM Post WHERE post_id = ?");
                $checkPost->execute([$post_id]);
                if (!$checkPost->fetch()) {
                    echo json_encode([
                        "status" => "error",
                        "message" => "Post not found"
                    ]);
                    return;
                }
            }

            $validTypes = ['Image', 'Video'];
            if ($media_type !== null && !in_array($media_type, $validTypes)) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Invalid media_type"
                ]);
                return;
            }

            $stmt = $pdo->prepare("
                UPDATE Media
                SET post_id = COALESCE(?, post_id),
                    media_type = COALESCE(?, media_type),
                    media_path = COALESCE(?, media_path)
                WHERE media_id = ?
            ");
            $stmt->execute([$post_id, $media_type, $media_path, $media_id]);

            echo json_encode([
                "status" => "success",
                "message" => "Media updated successfully"
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    public static function deleteMedia() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input || !isset($input['media_id'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing media_id"
            ]);
            return;
        }

        $media_id = (int)$input['media_id'];

        try {
            $stmt = $pdo->prepare("DELETE FROM Media WHERE media_id = ?");
            $stmt->execute([$media_id]);

            echo json_encode([
                "status" => "success",
                "message" => "Media deleted successfully"
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
