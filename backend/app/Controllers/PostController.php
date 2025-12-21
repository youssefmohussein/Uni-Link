<?php
namespace App\Controllers;

use App\Services\PostService;

/**
 * Post Controller
 * 
 * Handles post operations
 */
class PostController extends BaseController
{
    private PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Create post
     */
    public function create(): void
    {
        try {
            $this->requireAuth();

            $data = $this->getJsonInput();

            // Use author_id if provided, otherwise use current user
            if (!isset($data['author_id'])) {
                $data['author_id'] = $this->getCurrentUserId();
            }

            $post = $this->postService->createPost($data);
            $this->success($post, 'Post created successfully', 201);

        } catch (\Exception $e) {
            $code = is_int($e->getCode()) ? $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 500);
        }
    }

    /**
     * Update post
     */
    public function update(): void
    {
        try {
            $this->requireAuth();

            $data = $this->getJsonInput();
            $this->validateRequired($data, ['post_id']);

            $userId = $this->getCurrentUserId();
            $this->postService->updatePost((int) $data['post_id'], $userId, $data);
            $this->success(null, 'Post updated successfully');

        } catch (\Exception $e) {
            $code = is_int($e->getCode()) ? $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 500);
        }
    }

    /**
     * Delete post
     */
    public function delete(): void
    {
        try {
            $this->requireAuth();

            $data = $this->getJsonInput();
            $this->validateRequired($data, ['post_id']);

            $userId = $this->getCurrentUserId();
            $this->postService->deletePost((int) $data['post_id'], $userId);
            $this->success(null, 'Post deleted successfully');

        } catch (\Exception $e) {
            $code = is_int($e->getCode()) ? $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 500);
        }
    }

    /**
     * Get all posts
     */
    public function getAll(): void
    {
        try {
            $pagination = $this->getPagination();
            $posts = $this->postService->getAllPosts($pagination['limit'], $pagination['offset']);

            // Return in format expected by frontend
            echo json_encode([
                'status' => 'success',
                'count' => count($posts),
                'data' => $posts
            ]);

        } catch (\Exception $e) {
            $code = is_int($e->getCode()) ? $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 500);
        }
    }

    /**
     * Get user posts
     */
    public function getUserPosts(): void
    {
        try {
            $userId = isset($_GET['user_id']) ? (int) $_GET['user_id'] : $this->getCurrentUserId();

            if (!$userId) {
                throw new \Exception('User ID is required', 400);
            }

            $posts = $this->postService->getUserPosts($userId);
            $this->success([
                'count' => count($posts),
                'data' => $posts
            ]);

        } catch (\Exception $e) {
            $code = is_int($e->getCode()) ? $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 500);
        }
    }

    /**
     * Search posts
     */
    public function search(): void
    {
        try {
            $query = $_GET['q'] ?? '';

            if (empty($query)) {
                throw new \Exception('Search query is required', 400);
            }

            $posts = $this->postService->searchPosts($query);
            $this->success([
                'count' => count($posts),
                'data' => $posts
            ]);

        } catch (\Exception $e) {
            $code = is_int($e->getCode()) ? $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 500);
        }
    }

    /**
     * Upload media for a post
     */
    public function uploadMedia(): void
    {
        try {
            $this->requireAuth();

            // Get post_id from POST data and cast to int
            $postId = isset($_POST['post_id']) ? (int) $_POST['post_id'] : null;

            if (!$postId) {
                throw new \Exception('Post ID is required', 400);
            }

            // Verify post exists
            $post = $this->postService->getPostRepo()->find($postId);
            if (!$post) {
                throw new \Exception('Post not found', 404);
            }

            // Check if files were uploaded
            if (!isset($_FILES['media']) || empty($_FILES['media']['name'][0])) {
                throw new \Exception('No media files provided', 400);
            }

            $uploadedFiles = [];
            $uploadDir = __DIR__ . '/../../uploads/media';

            // Create upload directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Process each uploaded file
            $fileCount = count($_FILES['media']['name']);
            for ($i = 0; $i < $fileCount; $i++) {
                if ($_FILES['media']['error'][$i] !== UPLOAD_ERR_OK) {
                    continue;
                }

                $fileName = $_FILES['media']['name'][$i];
                $fileTmpName = $_FILES['media']['tmp_name'][$i];
                $fileType = $_FILES['media']['type'][$i];

                // Determine media type (uppercase for ENUM)
                $mediaType = strpos($fileType, 'image') !== false ? 'IMAGE' : 'VIDEO';

                // Generate unique filename
                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                $uniqueFileName = 'media_' . uniqid() . '.' . $extension;
                $targetPath = $uploadDir . '/' . $uniqueFileName;

                // Move uploaded file
                if (move_uploaded_file($fileTmpName, $targetPath)) {
                    // Save to database using repository
                    $relativePath = 'uploads/media/' . $uniqueFileName;

                    $db = $this->postService->getPostRepo()->getDb();
                    $stmt = $db->prepare("
                        INSERT INTO post_media (post_id, type, path, created_at) 
                        VALUES (?, ?, ?, NOW())
                    ");
                    $stmt->execute([$postId, $mediaType, $relativePath]);

                    $uploadedFiles[] = [
                        'media_id' => $db->lastInsertId(),
                        'media_type' => $mediaType,
                        'media_path' => $relativePath
                    ];
                }
            }

            $this->success([
                'uploaded' => count($uploadedFiles),
                'data' => $uploadedFiles
            ], 'Media uploaded successfully');

        } catch (\Exception $e) {
            error_log("Media upload error: " . $e->getMessage());
            $code = is_int($e->getCode()) ? $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 500);
        }
    }

    /**
     * Get post counts by category
     * GET /api/posts/category-counts
     */
    public function getCategoryCounts(): void
    {
        try {
            $counts = $this->postService->getCategoryCounts();
            $this->success($counts);
        } catch (\Exception $e) {
            $this->error($e->getMessage(), 500);
        }
    }
}
