<?php

require_once __DIR__ . '/../utils/DbConnection.php';

class SavedPostController {
    /**
     * Save a post for the current user
     * Request: { user_id, post_id }
     */
    public static function savePost() {
        header('Content-Type: application/json');
        
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['user_id']) || !isset($data['post_id'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'user_id and post_id are required'
                ]);
                return;
            }
            
            $user_id = (int)$data['user_id'];
            $post_id = (int)$data['post_id'];
            
            $conn = DbConnection::getConnection();
            
            // Check if post exists
            $postCheck = $conn->prepare("SELECT post_id FROM posts WHERE post_id = ?");
            $postCheck->bind_param("i", $post_id);
            $postCheck->execute();
            if (!$postCheck->get_result()->fetch_assoc()) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Post not found'
                ]);
                return;
            }
            
            // Insert saved post (IGNORE duplicates)
            $stmt = $conn->prepare("
                INSERT IGNORE INTO saved_posts (user_id, post_id) 
                VALUES (?, ?)
            ");
            $stmt->bind_param("ii", $user_id, $post_id);
            
            if ($stmt->execute()) {
                $affectedRows = $stmt->affected_rows;
                echo json_encode([
                    'status' => 'success',
                    'message' => $affectedRows > 0 ? 'Post saved successfully' : 'Post already saved',
                    'saved_id' => $affectedRows > 0 ? $conn->insert_id : null,
                    'already_saved' => $affectedRows === 0
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to save post'
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Unsave/remove a post from saved collection
     * Request: { user_id, post_id }
     */
    public static function unsavePost() {
        header('Content-Type: application/json');
        
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['user_id']) || !isset($data['post_id'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'user_id and post_id are required'
                ]);
                return;
            }
            
            $user_id = (int)$data['user_id'];
            $post_id = (int)$data['post_id'];
            
            $conn = DbConnection::getConnection();
            
            $stmt = $conn->prepare("
                DELETE FROM saved_posts 
                WHERE user_id = ? AND post_id = ?
            ");
            $stmt->bind_param("ii", $user_id, $post_id);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'status' => 'success',
                    'message' => $stmt->affected_rows > 0 ? 'Post unsaved successfully' : 'Post was not saved',
                    'removed' => $stmt->affected_rows > 0
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to unsave post'
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get all saved posts for a user with full post details
     * Request: GET ?user_id=X
     */
    public static function getSavedPosts() {
        header('Content-Type: application/json');
        
        try {
            if (!isset($_GET['user_id'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'user_id is required'
                ]);
                return;
            }
            
            $user_id = (int)$_GET['user_id'];
            
            $conn = DbConnection::getConnection();
            
            // Get saved posts with full details (similar to getAllPosts)
            $stmt = $conn->prepare("
                SELECT 
                    p.post_id,
                    p.content,
                    p.category,
                    p.created_at,
                    p.author_id,
                    u.username,
                    u.email,
                    f.faculty_name,
                    m.major_name,
                    sp.created_at as saved_at,
                    (SELECT COUNT(*) FROM post_interactions WHERE post_id = p.post_id AND type = 'Like') as reaction_count,
                    (SELECT COUNT(*) FROM comments WHERE entity_type = 'post' AND entity_id = p.post_id) as comment_count,
                    EXISTS(
                        SELECT 1 FROM post_interactions 
                        WHERE post_id = p.post_id AND user_id = ? AND type = 'Like'
                    ) as is_reacted
                FROM saved_posts sp
                INNER JOIN posts p ON sp.post_id = p.post_id
                INNER JOIN users u ON p.author_id = u.id
                LEFT JOIN students s ON u.id = s.user_id
                LEFT JOIN faculties f ON s.faculty_id = f.faculty_id
                LEFT JOIN majors m ON s.major_id = m.major_id
                WHERE sp.user_id = ?
                ORDER BY sp.created_at DESC
            ");
            $stmt->bind_param("ii", $user_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $posts = [];
            while ($row = $result->fetch_assoc()) {
                // Get media for this post
                $mediaStmt = $conn->prepare("
                    SELECT media_id, type, url 
                    FROM post_media 
                    WHERE post_id = ?
                ");
                $mediaStmt->bind_param("i", $row['post_id']);
                $mediaStmt->execute();
                $mediaResult = $mediaStmt->get_result();
                $media = [];
                while ($mediaRow = $mediaResult->fetch_assoc()) {
                    $media[] = $mediaRow;
                }
                
                $posts[] = [
                    'post_id' => (int)$row['post_id'],
                    'content' => $row['content'],
                    'category' => $row['category'],
                    'created_at' => $row['created_at'],
                    'saved_at' => $row['saved_at'],
                    'author_id' => (int)$row['author_id'],
                    'username' => $row['username'],
                    'email' => $row['email'],
                    'faculty_name' => $row['faculty_name'] ?? 'Unknown',
                    'major_name' => $row['major_name'] ?? 'Unknown',
                    'reactions' => (int)$row['reaction_count'],
                    'comment_count' => (int)$row['comment_count'],
                    'is_reacted' => (bool)$row['is_reacted'],
                    'is_saved' => true, // Always true for saved posts
                    'media' => $media
                ];
            }
            
            echo json_encode([
                'status' => 'success',
                'data' => $posts,
                'count' => count($posts)
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Check if a specific post is saved by user
     * Request: GET ?user_id=X&post_id=Y
     */
    public static function isPostSaved() {
        header('Content-Type: application/json');
        
        try {
            if (!isset($_GET['user_id']) || !isset($_GET['post_id'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'user_id and post_id are required'
                ]);
                return;
            }
            
            $user_id = (int)$_GET['user_id'];
            $post_id = (int)$_GET['post_id'];
            
            $conn = DbConnection::getConnection();
            
            $stmt = $conn->prepare("
                SELECT saved_id 
                FROM saved_posts 
                WHERE user_id = ? AND post_id = ?
            ");
            $stmt->bind_param("ii", $user_id, $post_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $saved = $result->fetch_assoc();
            
            echo json_encode([
                'status' => 'success',
                'is_saved' => $saved !== null,
                'saved_id' => $saved ? (int)$saved['saved_id'] : null
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }
}

?>
