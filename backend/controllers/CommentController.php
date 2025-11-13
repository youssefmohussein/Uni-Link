<?php
require_once __DIR__ . '/../utils/DbConnection.php';

class CommentController {

    public static function getCommentsByPost() {
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
                SELECT c.*, u.username AS user_name
                FROM Comment c
                JOIN Users u ON c.user_id = u.user_id
                WHERE c.post_id = ?
                ORDER BY c.created_at ASC
            ");
            $stmt->execute([$post_id]);
            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "count" => count($comments),
                "data" => $comments
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    public static function addComment() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input || !isset($input['post_id'], $input['user_id'], $input['content'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields"
            ]);
            return;
        }

        $post_id = (int)$input['post_id'];
        $user_id = (int)$input['user_id'];
        $parent_id = isset($input['parent_id']) ? (int)$input['parent_id'] : null;
        $content = trim($input['content']);

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

            // Check if parent comment exists (if parent_id provided)
            if ($parent_id !== null) {
                $checkParent = $pdo->prepare("SELECT * FROM Comment WHERE comment_id = ?");
                $checkParent->execute([$parent_id]);
                if (!$checkParent->fetch()) {
                    echo json_encode([
                        "status" => "error",
                        "message" => "Parent comment not found"
                    ]);
                    return;
                }
            }

            $stmt = $pdo->prepare("
                INSERT INTO Comment (post_id, user_id, parent_id, content, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$post_id, $user_id, $parent_id, $content]);

            echo json_encode([
                "status" => "success",
                "message" => "Comment added successfully",
                "comment_id" => $pdo->lastInsertId()
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    public static function updateComment() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input || !isset($input['comment_id'], $input['content'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing comment_id or content"
            ]);
            return;
        }

        $comment_id = (int)$input['comment_id'];
        $content = trim($input['content']);

        try {
            $check = $pdo->prepare("SELECT * FROM Comment WHERE comment_id = ?");
            $check->execute([$comment_id]);
            if (!$check->fetch()) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Comment not found"
                ]);
                return;
            }

            $stmt = $pdo->prepare("
                UPDATE Comment
                SET content = ?
                WHERE comment_id = ?
            ");
            $stmt->execute([$content, $comment_id]);

            echo json_encode([
                "status" => "success",
                "message" => "Comment updated successfully"
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    public static function deleteComment() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input || !isset($input['comment_id'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing comment_id"
            ]);
            return;
        }

        $comment_id = (int)$input['comment_id'];

        try {
            // Optional: delete child comments first to avoid FK issues
            $pdo->prepare("DELETE FROM Comment WHERE parent_id = ?")->execute([$comment_id]);

            $stmt = $pdo->prepare("DELETE FROM Comment WHERE comment_id = ?");
            $stmt->execute([$comment_id]);

            echo json_encode([
                "status" => "success",
                "message" => "Comment deleted successfully"
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
