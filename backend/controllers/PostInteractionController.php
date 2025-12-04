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
                "message" => "Invalid interaction type. Valid types: Like, Love, celberation, Share, Save"
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

            // Check if user already has ANY reaction on this post
            $checkExisting = $pdo->prepare("
                SELECT interaction_id, type FROM PostInteraction 
                WHERE post_id = ? AND user_id = ?
            ");
            $checkExisting->execute([$post_id, $user_id]);
            $existing = $checkExisting->fetch(PDO::FETCH_ASSOC);
            
            // If user already has a reaction, update it instead of creating new
            if ($existing) {
                if ($existing['type'] === $type) {
                    echo json_encode([
                        "status" => "error",
                        "message" => "You already reacted with this type",
                        "interaction_id" => $existing['interaction_id']
                    ]);
                    return;
                }
                
                // Update existing reaction to new type
                $updateStmt = $pdo->prepare("
                    UPDATE PostInteraction SET type = ?, created_at = NOW()
                    WHERE interaction_id = ?
                ");
                $updateStmt->execute([$type, $existing['interaction_id']]);
                
                echo json_encode([
                    "status" => "success",
                    "message" => "Reaction updated successfully",
                    "interaction_id" => $existing['interaction_id'],
                    "action" => "updated"
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
                "message" => "Reaction added successfully",
                "interaction_id" => $pdo->lastInsertId(),
                "action" => "added"
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

    public static function getUserReaction() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input || !isset($input['post_id'], $input['user_id'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing post_id or user_id"
            ]);
            return;
        }

        $post_id = (int)$input['post_id'];
        $user_id = (int)$input['user_id'];

        try {
            $stmt = $pdo->prepare("
                SELECT interaction_id, type, created_at
                FROM PostInteraction
                WHERE post_id = ? AND user_id = ?
            ");
            $stmt->execute([$post_id, $user_id]);
            $reaction = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($reaction) {
                echo json_encode([
                    "status" => "success",
                    "data" => $reaction
                ]);
            } else {
                echo json_encode([
                    "status" => "success",
                    "data" => null
                ]);
            }

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    public static function getReactionCounts() {
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
                SELECT type, COUNT(*) as count
                FROM PostInteraction
                WHERE post_id = ?
                GROUP BY type
            ");
            $stmt->execute([$post_id]);
            $counts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Format as object with reaction types as keys
            $result = [
                'Like' => 0,
                'Love' => 0,
                'celberation' => 0,
                'Share' => 0,
                'Save' => 0
            ];

            foreach ($counts as $row) {
                if (isset($result[$row['type']])) {
                    $result[$row['type']] = (int)$row['count'];
                }
            }

            echo json_encode([
                "status" => "success",
                "data" => $result,
                "total" => array_sum($result)
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
