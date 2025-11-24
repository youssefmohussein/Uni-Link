<?php
require_once __DIR__ . '/../utils/DbConnection.php';

class CommentController
{

    public static function addComment()
    {
        global $pdo;

        $data = json_decode(file_get_contents("php://input"), true);

        $entityType = $data['entity_type'];
        $entityId   = $data['entity_id'];
        $userId     = $data['user_id'];
        $parentId   = $data['parent_id'] ?? null;
        $content    = $data['content'];

        if (!$entityType || !$entityId || !$userId || !$content) {
            echo json_encode(["status" => "error", "message" => "Missing required fields"]);
            return;
        }

        // 1️⃣ PROJECT VALIDATION
        if ($entityType === "project") {
            if (!self::validateProjectProfessor($pdo, $entityId, $userId)) return;
        }

        // INSERT
        $stmt = $pdo->prepare("
            INSERT INTO comments (entity_type, entity_id, user_id, parent_id, content)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([$entityType, $entityId, $userId, $parentId, $content]);

        echo json_encode(["status" => "success", "message" => "Comment added"]);
    }
    public static function getComments($entityType, $entityId)
    {
        global $pdo;

        $sql = "
            SELECT c.*, u.username
            FROM comments c
            JOIN users u ON u.user_id = c.user_id
            WHERE c.entity_type = ?
            AND c.entity_id = ?
            ORDER BY c.created_at ASC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$entityType, $entityId]);

        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public static function getComment($commentId)
    {
        global $pdo;

        $stmt = $pdo->prepare("
            SELECT c.*, u.username
            FROM comments c
            JOIN users u ON u.user_id = c.user_id
            WHERE c.comment_id = ?
        ");
        $stmt->execute([$commentId]);

        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    }
    public static function updateComment()
    {
        global $pdo;

        $data = json_decode(file_get_contents("php://input"), true);

        $commentId = $data['comment_id'];
        $userId    = $data['user_id'];
        $content   = $data['content'];

        if (!$commentId || !$userId || !$content) {
            echo json_encode(["status" => "error", "message" => "Missing fields"]);
            return;
        }

        // Get the existing comment
        $stmt = $pdo->prepare("SELECT * FROM comments WHERE comment_id = ?");
        $stmt->execute([$commentId]);
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$comment) {
            echo json_encode(["status" => "error", "message" => "Comment not found"]);
            return;
        }

        // Only the owner can update
        if ((int)$comment['user_id'] !== (int)$userId) {
            echo json_encode(["status" => "error", "message" => "You cannot edit someone else's comment"]);
            return;
        }

        // If it's a project comment → Professor validation
        if ($comment['entity_type'] === "project") {
            if (!self::validateProjectProfessor($pdo, $comment['entity_id'], $userId)) return;
        }

        // UPDATE
        $update = $pdo->prepare("UPDATE comments SET content = ? WHERE comment_id = ?");
        $update->execute([$content, $commentId]);

        echo json_encode(["status" => "success", "message" => "Comment updated"]);
    }
    
    public static function deleteComment()
    {
        global $pdo;
        $data = json_decode(file_get_contents("php://input"), true);

        $commentId = $data['comment_id'];
        $userId    = $data['user_id'];

        if (!$commentId || !$userId) {
            echo json_encode(["status" => "error", "message" => "Missing fields"]);
            return;
        }

        // Get old comment
        $stmt = $pdo->prepare("SELECT * FROM comments WHERE comment_id = ?");
        $stmt->execute([$commentId]);
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$comment) {
            echo json_encode(["status" => "error", "message" => "Comment not found"]);
            return;
        }

        // Only the owner can delete
        if ((int)$comment['user_id'] !== (int)$userId) {
            echo json_encode(["status" => "error", "message" => "You cannot delete someone else's comment"]);
            return;
        }

        // If it's a project → check professor
        if ($comment['entity_type'] === "project") {
            if (!self::validateProjectProfessor($pdo, $comment['entity_id'], $userId)) return;
        }

        // DELETE
        $del = $pdo->prepare("DELETE FROM comments WHERE comment_id = ?");
        $del->execute([$commentId]);

        echo json_encode(["status" => "success", "message" => "Comment deleted"]);
    }

    private static function validateProjectProfessor($pdo, $projectId, $userId)
    {
        $stmt = $pdo->prepare("SELECT professor_id FROM projects WHERE project_id = ?");
        $stmt->execute([$projectId]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$project) {
            echo json_encode(["status" => "error", "message" => "Project not found"]);
            return false;
        }

        if ((int)$project['professor_id'] !== (int)$userId) {
            echo json_encode([
                "status" => "error",
                "message" => "Only the assigned professor can interact with project comments"
            ]);
            return false;
        }
        return true;
    }
}