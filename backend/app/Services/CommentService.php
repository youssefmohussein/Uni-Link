<?php
namespace App\Services;

use App\Repositories\CommentRepository;
use App\Repositories\PostRepository;

/**
 * Comment Service
 * 
 * Business logic for comment management
 */
class CommentService extends BaseService {
    private CommentRepository $commentRepo;
    private PostRepository $postRepo;
    private ContentModerationService $moderationService;
    
    public function __construct(CommentRepository $commentRepo, PostRepository $postRepo) {
        $this->commentRepo = $commentRepo;
        $this->postRepo = $postRepo;
        $this->moderationService = new ContentModerationService();
    }
    
    /**
     * Create comment
     * 
     * @param array $data Comment data
     * @return array Created comment
     */
    public function createComment(array $data): array {
        // Validate
        $errors = $this->validate($data, [
            'post_id' => ['required', 'numeric'],
            'user_id' => ['required', 'numeric'],
            'content' => ['required']
        ]);
        
        if (!empty($errors)) {
            throw new \Exception($this->formatValidationErrors($errors), 400);
        }
        
        // Verify post exists
        if (!$this->postRepo->exists((int)$data['post_id'])) {
            throw new \Exception('Post not found', 404);
        }
        
        // Content Moderation - Check for toxicity/negative sentiment
        try {
            $this->moderationService->validateContent($data['content']);
        } catch (ContentBlockedException $e) {
            throw new \Exception(
                'Comment blocked: ' . $e->getMessage() . 
                ' (Toxicity score: ' . round($e->getToxicityScore() * 100) . '%)',
                400
            );
        }
        
        // Sanitize
        $sanitized = $this->sanitizeData($data, ['content']);
        
        $commentId = $this->commentRepo->create([
            'post_id' => (int)$data['post_id'],
            'user_id' => (int)$data['user_id'],
            'content' => $sanitized['content'],
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        return $this->commentRepo->find($commentId);
    }
    
    /**
     * Get comments by post
     * 
     * @param int $postId Post ID
     * @return array Array of comments
     */
    public function getByPost(int $postId): array {
        return $this->commentRepo->findByPost($postId);
    }
    
    /**
     * Delete comment
     * 
     * @param int $commentId Comment ID
     * @return bool Success status
     */
    public function deleteComment(int $commentId): bool {
        if (!$this->commentRepo->exists($commentId)) {
            throw new \Exception('Comment not found', 404);
        }
        
        return $this->commentRepo->delete($commentId);
    }
}
