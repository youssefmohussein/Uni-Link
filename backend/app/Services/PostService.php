<?php
namespace App\Services;

use App\Repositories\PostRepository;
use App\Repositories\CommentRepository;

/**
 * Post Service
 * 
 * Business logic for post management
 */
class PostService extends BaseService {
    private PostRepository $postRepo;
    private CommentRepository $commentRepo;
    
    public function __construct(PostRepository $postRepo, CommentRepository $commentRepo) {
        $this->postRepo = $postRepo;
        $this->commentRepo = $commentRepo;
    }
    
    /**
     * Create post
     * 
     * @param array $data Post data
     * @return array Created post
     */
    public function createPost(array $data): array {
        // Validate
        $errors = $this->validate($data, [
            'user_id' => ['required'],
            'content' => ['required', 'min:1'],
            'category' => ['required']
        ]);
        
        if (!empty($errors)) {
            throw new \Exception('Validation failed: ' . json_encode($errors), 400);
        }
        
        // Sanitize
        $data['content'] = $this->sanitize($data['content']);
        $data['created_at'] = date('Y-m-d H:i:s');
        
        // Create
        $postId = $this->postRepo->create($data);
        
        return $this->postRepo->getWithMedia($postId);
    }
    
    /**
     * Update post
     * 
     * @param int $postId Post ID
     * @param int $userId User ID (for ownership check)
     * @param array $data Updated data
     * @return bool Success
     */
    public function updatePost(int $postId, int $userId, array $data): bool {
        $post = $this->postRepo->find($postId);
        
        if (!$post) {
            throw new \Exception('Post not found', 404);
        }
        
        if ($post['user_id'] != $userId) {
            throw new \Exception('Unauthorized', 403);
        }
        
        if (isset($data['content'])) {
            $data['content'] = $this->sanitize($data['content']);
        }
        
        return $this->postRepo->update($postId, $data);
    }
    
    /**
     * Delete post
     * 
     * @param int $postId Post ID
     * @param int $userId User ID (for ownership check)
     * @return bool Success
     */
    public function deletePost(int $postId, int $userId): bool {
        $post = $this->postRepo->find($postId);
        
        if (!$post) {
            throw new \Exception('Post not found', 404);
        }
        
        if ($post['user_id'] != $userId) {
            throw new \Exception('Unauthorized', 403);
        }
        
        return $this->postRepo->delete($postId);
    }
}
