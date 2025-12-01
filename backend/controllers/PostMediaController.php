<?php
require_once __DIR__ . '/../utils/DbConnection.php';

class PostMediaController  {

    /**
     * Upload media files (images/videos) for a post
     * Handles multipart/form-data file uploads
     */
    public static function uploadMedia() {
        global $pdo;

        // Validate post_id from POST data
        if (!isset($_POST['post_id'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing post_id"
            ]);
            return;
        }

        $post_id = (int)$_POST['post_id'];

        // Check if post exists
        try {
            $checkPost = $pdo->prepare("SELECT * FROM Post WHERE post_id = ?");
            $checkPost->execute([$post_id]);
            if (!$checkPost->fetch()) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Post not found"
                ]);
                return;
            }
        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
            return;
        }

        // Check if files were uploaded
        if (!isset($_FILES['media']) || empty($_FILES['media']['name'][0])) {
            echo json_encode([
                "status" => "error",
                "message" => "No files uploaded"
            ]);
            return;
        }

        // Create uploads directory if it doesn't exist
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $uploadedMedia = [];
        $files = $_FILES['media'];
        $fileCount = count($files['name']);

        // Process each uploaded file
        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                continue; // Skip files with errors
            }

            $fileName = $files['name'][$i];
            $fileTmpPath = $files['tmp_name'][$i];
            $fileSize = $files['size'][$i];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Validate file type
            $allowedImageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $allowedVideoTypes = ['mp4', 'webm', 'mov', 'avi'];
            
            $mediaType = null;
            if (in_array($fileExtension, $allowedImageTypes)) {
                $mediaType = 'Image';
                $maxSize = 10 * 1024 * 1024; // 10MB for images
            } elseif (in_array($fileExtension, $allowedVideoTypes)) {
                $mediaType = 'Video';
                $maxSize = 50 * 1024 * 1024; // 50MB for videos
            } else {
                continue; // Skip unsupported file types
            }

            // Validate file size
            if ($fileSize > $maxSize) {
                continue; // Skip files that are too large
            }

            // Generate unique filename
            $newFileName = uniqid('media_', true) . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            // Move uploaded file
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Store relative path in database
                $relativePath = 'uploads/' . $newFileName;

                try {
                    // Insert into Media table
                    $stmt = $pdo->prepare("
                        INSERT INTO Media (post_id, media_type, media_path, uploaded_at)
                        VALUES (?, ?, ?, NOW())
                    ");
                    $stmt->execute([$post_id, $mediaType, $relativePath]);

                    $uploadedMedia[] = [
                        'media_id' => $pdo->lastInsertId(),
                        'media_type' => $mediaType,
                        'media_path' => $relativePath,
                        'original_name' => $fileName
                    ];
                } catch (PDOException $e) {
                    // If database insert fails, delete the uploaded file
                    unlink($destPath);
                }
            }
        }

        if (empty($uploadedMedia)) {
            echo json_encode([
                "status" => "error",
                "message" => "No valid files were uploaded"
            ]);
            return;
        }

        echo json_encode([
            "status" => "success",
            "message" => "Media uploaded successfully",
            "count" => count($uploadedMedia),
            "data" => $uploadedMedia
        ]);
    }

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
