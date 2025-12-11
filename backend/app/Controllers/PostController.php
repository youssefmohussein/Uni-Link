<?php
namespace App\Controllers;

use App\Services\PostService;

/**
 * Post Controller
 * 
 * Handles post operations
 */
class PostController extends BaseController {
    private PostService $postService;
    
    public function __construct(PostService $postService) {
        $this->postService = $postService;
    }
    
    /**
     * Create post
     */
    public function create(): void {
        try {
            $this->requireAuth();
            
            $data = $this->getJsonInput();
            $data['user_id'] = $this->getCurrentUserId();
            
            $post = $this->postService->createPost($data);
            $this->success($post, 'Post created successfully', 201);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Update post
     */
    public function update(): void {
        try {
            $this->requireAuth();
            
            $data = $this->getJsonInput();
            $this->validateRequired($data, ['post_id']);
            
            $post = $this->postService->updatePost((int)$data['post_id'], $data);
            $this->success($post, 'Post updated successfully');
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Delete post
     */
    public function delete(): void {
        try {
            $this->requireAuth();
            
            $data = $this->getJsonInput();
            $this->validateRequired($data, ['post_id']);
            
            $this->postService->deletePost((int)$data['post_id']);
            $this->success(null, 'Post deleted successfully');
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Get all posts
     */
    public function getAll(): void {
        try {
            $pagination = $this->getPagination();
            $posts = $this->postService->getAllPosts($pagination['limit'], $pagination['offset']);
            
            $this->success([
                'count' => count($posts),
                'data' => $posts
            ]);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Get user posts
     */
    public function getUserPosts(): void {
        try {
            $userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : $this->getCurrentUserId();
            
            if (!$userId) {
                throw new \Exception('User ID is required', 400);
            }
            
            $posts = $this->postService->getUserPosts($userId);
            $this->success([
                'count' => count($posts),
                'data' => $posts
            ]);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Search posts
     */
    public function search(): void {
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
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
