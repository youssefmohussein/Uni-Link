<?php
require_once __DIR__ . '/../utils/DbConnection.php';

class PostInteractionController {


    public static function getInteractionsByPost() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input || !isset($input['post_id'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing post_id"
            ]);
            return;
        }

        $post_id = (int)$input['post_id'];

        try {
            $stmt = $pdo->prepare("
                SELECT pi.*, u.username AS user_name
                FROM PostInteraction pi
                JOIN Users u ON pi.user_id = u.user_id
                WHERE pi.post_id = ?
                ORDER BY pi.created_at DESC
            ");
            $stmt->execute([$post_id]);
            $interactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "count" => count($interactions),
                "data" => $interactions
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    public static function addInteraction() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input || !isset($input['post_id'], $input['user_id'], $input['type'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields"
            ]);
            return;
        }

        $post_id = (int)$input['post_id'];
        $user_id = (int)$input['user_id'];
        $type = trim($input['type']);

        $validTypes = ['Like', 'Love', 'celberation', 'Share', 'Save'];
        if (!in_array($type, $validTypes)) {
            echo json_encode([
                "status" => "error",
                "message" => "Invalid interaction type"
            ]);
            return;
        }

        try {
            // Check if post exists
            $checkPost = $pdo->prepare("SELECT * FROM Post WHERE post_id = ?");
            $checkPost->execute([$post_id]);
            if (!$checkPost->fetch()) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Post not found"
                ]);
                return;
            }

            // Check if user exists
            $checkUser = $pdo->prepare("SELECT * FROM Users WHERE user_id = ?");
            $checkUser->execute([$user_id]);
            if (!$checkUser->fetch()) {
                echo json_encode([
                    "status" => "error",
                    "message" => "User not found"
                ]);
                return;
            }

            // Optional: prevent duplicate interaction of the same type by same user
            $checkDuplicate = $pdo->prepare("
                SELECT * FROM PostInteraction 
                WHERE post_id = ? AND user_id = ? AND type = ?
            ");
            $checkDuplicate->execute([$post_id, $user_id, $type]);
            if ($checkDuplicate->fetch()) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Interaction already exists"
                ]);
                return;
            }

            $stmt = $pdo->prepare("
                INSERT INTO PostInteraction (post_id, user_id, type, created_at)
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$post_id, $user_id, $type]);

            echo json_encode([
                "status" => "success",
                "message" => "Interaction added successfully",
                "interaction_id" => $pdo->lastInsertId()
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    public static function deleteInteraction() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input || !isset($input['interaction_id'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing interaction_id"
            ]);
            return;
        }

        $interaction_id = (int)$input['interaction_id'];

        try {
            $stmt = $pdo->prepare("DELETE FROM PostInteraction WHERE interaction_id = ?");
            $stmt->execute([$interaction_id]);

            echo json_encode([
                "status" => "success",
                "message" => "Interaction deleted successfully"
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
