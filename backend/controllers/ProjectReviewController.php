<?php
require_once __DIR__ . '/../utils/DbConnection.php';

class ProjectReviewController
{
    
    public static function addReview()
    {
        global $pdo;
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['project_id'], $data['professor_id'], $data['comment'], $data['mark'])) {
            echo json_encode(["status" => "error", "message" => "Missing fields"]);
            return;
        }

        try {
            $stmt = $pdo->prepare("
                INSERT INTO ProjectReview (project_id, professor_id, comment, mark, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $data['project_id'],
                $data['professor_id'],
                $data['comment'],
                $data['mark']
            ]);

            echo json_encode(["status" => "success", "message" => "Review added"]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public static function getReviewsByProject($projectId)
    {
        global $pdo;

        try {
            $stmt = $pdo->prepare("
                SELECT r.*, u.username AS professor_name
                FROM ProjectReview r
                JOIN Users u ON u.user_id = r.professor_id
                WHERE r.project_id = ?
                ORDER BY r.created_at DESC
            ");
            $stmt->execute([$projectId]);
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(["status" => "success", "reviews" => $reviews]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public static function getReview($reviewId)
    {
        global $pdo;

        try {
            $stmt = $pdo->prepare("SELECT * FROM ProjectReview WHERE review_id = ?");
            $stmt->execute([$reviewId]);
            $review = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($review) {
                echo json_encode(["status" => "success", "review" => $review]);
            } else {
                echo json_encode(["status" => "error", "message" => "Review not found"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public static function updateReview()
    {
        global $pdo;
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['review_id'], $data['comment'], $data['mark'])) {
            echo json_encode(["status" => "error", "message" => "Missing fields"]);
            return;
        }

        try {
            $stmt = $pdo->prepare("
                UPDATE ProjectReview
                SET comment = ?, mark = ?
                WHERE review_id = ?
            ");
            $stmt->execute([
                $data['comment'],
                $data['mark'],
                $data['review_id']
            ]);

            echo json_encode(["status" => "success", "message" => "Review updated"]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public static function deleteReview()
    {
        global $pdo;
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['review_id'])) {
            echo json_encode(["status" => "error", "message" => "Missing review_id"]);
            return;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM ProjectReview WHERE review_id = ?");
            $stmt->execute([$data['review_id']]);

            echo json_encode(["status" => "success", "message" => "Review deleted"]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
}
