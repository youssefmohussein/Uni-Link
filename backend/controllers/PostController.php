<?php

require_once __DIR__ . '/../utils/DbConnection.php';

class PostController {
    public static function addPost() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);

        // ✅ Validate input
        if (
            !$input ||
            !isset($input['author_id'], $input['faculty_id'], $input['category'], $input['content'], $input['status'])
        ) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields"
            ]);
            return;
        }

        $author_id = (int)$input['author_id'];
        $faculty_id = (int)$input['faculty_id'];
        $category = trim($input['category']);
        $content = trim($input['content']);
        $status = trim($input['status']);

        // ✅ Validate enums
        $validCategories = ['Questions', 'Events', 'Projects'];
        $validStatuses = ['Published', 'Draft'];

        if (!in_array($category, $validCategories) || !in_array($status, $validStatuses)) {
            echo json_encode([
                "status" => "error",
                "message" => "Invalid category or status value"
            ]);
            return;
        }

        try {
            // 🧠 Insert new post
            $stmt = $pdo->prepare("
                INSERT INTO Post (author_id, category, faculty_id, content, status, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$author_id, $category, $faculty_id, $content, $status]);

            $post_id = $pdo->lastInsertId();

            echo json_encode([
                "status" => "success",
                "message" => "Post added successfully",
                "post_id" => $post_id
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    public static function getAllPosts() {
        global $pdo;

        try {
            $stmt = $pdo->query("
                SELECT 
                    p.*, 
                    u.username AS author_name,
                    f.faculty_name,
                    COUNT(DISTINCT pi.interaction_id) AS likes_count
                FROM Post p
                JOIN Users u ON p.author_id = u.user_id
                JOIN Faculty f ON p.faculty_id = f.faculty_id
                LEFT JOIN PostInteraction pi ON p.post_id = pi.post_id AND pi.type = 'Like'
                GROUP BY p.post_id
                ORDER BY p.created_at DESC
            ");

            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Fetch media for each post
            foreach ($posts as &$post) {
                $mediaStmt = $pdo->prepare("SELECT * FROM Media WHERE post_id = ?");
                $mediaStmt->execute([$post['post_id']]);
                $post['media'] = $mediaStmt->fetchAll(PDO::FETCH_ASSOC);
            }

            echo json_encode([
                "status" => "success",
                "count" => count($posts),
                "data" => $posts
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    public static function getPostById($post_id) {
        global $pdo;

        try {
            $stmt = $pdo->prepare("
                SELECT 
                    p.*, 
                    u.username AS author_name,
                    f.faculty_name
                FROM Post p
                JOIN Users u ON p.author_id = u.user_id
                JOIN Faculty f ON p.faculty_id = f.faculty_id
                WHERE p.post_id = ?
            ");
            $stmt->execute([$post_id]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$post) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Post not found"
                ]);
                return;
            }

            // Get related media
            $mediaStmt = $pdo->prepare("SELECT * FROM PostMedia WHERE post_id = ?");
            $mediaStmt->execute([$post_id]);
            $media = $mediaStmt->fetchAll(PDO::FETCH_ASSOC);

            // Get comments count
            $commentStmt = $pdo->prepare("SELECT COUNT(*) AS total_comments FROM Comment WHERE post_id = ?");
            $commentStmt->execute([$post_id]);
            $commentsCount = $commentStmt->fetch(PDO::FETCH_ASSOC)['total_comments'];

            // Get likes count
            $likeStmt = $pdo->prepare("SELECT COUNT(*) AS total_likes FROM PostInteraction WHERE post_id = ?");
            $likeStmt->execute([$post_id]);
            $likesCount = $likeStmt->fetch(PDO::FETCH_ASSOC)['total_likes'];

            $post['media'] = $media;
            $post['comments_count'] = $commentsCount;
            $post['likes_count'] = $likesCount;

            echo json_encode([
                "status" => "success",
                "data" => $post
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    public static function updatePost() {
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
        $content = isset($input['content']) ? trim($input['content']) : null;
        $status = isset($input['status']) ? trim($input['status']) : null;

        try {
            // Check if post exists
            $check = $pdo->prepare("SELECT * FROM Post WHERE post_id = ?");
            $check->execute([$post_id]);
            if (!$check->fetch()) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Post not found"
                ]);
                return;
            }

            // Update post
            $stmt = $pdo->prepare("
                UPDATE Post
                SET content = COALESCE(?, content),
                    status = COALESCE(?, status)
                WHERE post_id = ?
            ");
            $stmt->execute([$content, $status, $post_id]);

            echo json_encode([
                "status" => "success",
                "message" => "Post updated successfully"
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    public static function deletePost() {
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
            // Delete related media, comments, interactions
            $pdo->prepare("DELETE FROM PostMedia WHERE post_id = ?")->execute([$post_id]);
            $pdo->prepare("DELETE FROM Comment WHERE post_id = ?")->execute([$post_id]);
            $pdo->prepare("DELETE FROM PostInteraction WHERE post_id = ?")->execute([$post_id]);

            // Delete post itself
            $stmt = $pdo->prepare("DELETE FROM Post WHERE post_id = ?");
            $stmt->execute([$post_id]);

            echo json_encode([
                "status" => "success",
                "message" => "Post deleted successfully"
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    public static function searchPosts() {
        global $pdo;

        // Get search query from GET parameter
        $query = $_GET['q'] ?? '';

        // If query is empty, return empty results
        if (empty(trim($query))) {
            echo json_encode([
                "status" => "success",
                "count" => 0,
                "data" => []
            ]);
            return;
        }

        try {
            // Prepare search term with wildcards for LIKE query
            $searchTerm = "%{$query}%";

            // Search in post content only
            $stmt = $pdo->prepare("
                SELECT 
                    p.*, 
                    u.username AS author_name,
                    f.faculty_name,
                    COUNT(DISTINCT pi.interaction_id) AS likes_count
                FROM Post p
                JOIN Users u ON p.author_id = u.user_id
                JOIN Faculty f ON p.faculty_id = f.faculty_id
                LEFT JOIN PostInteraction pi ON p.post_id = pi.post_id AND pi.type = 'Like'
                WHERE p.content LIKE ?
                GROUP BY p.post_id
                ORDER BY p.created_at DESC
            ");
            $stmt->execute([$searchTerm]);
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Fetch media for each post
            foreach ($posts as &$post) {
                $mediaStmt = $pdo->prepare("SELECT * FROM Media WHERE post_id = ?");
                $mediaStmt->execute([$post['post_id']]);
                $post['media'] = $mediaStmt->fetchAll(PDO::FETCH_ASSOC);
            }

            echo json_encode([
                "status" => "success",
                "count" => count($posts),
                "data" => $posts
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
