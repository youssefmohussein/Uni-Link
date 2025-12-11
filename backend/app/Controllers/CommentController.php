<?php
namespace App\Controllers;

use App\Services\CommentService;

/**
 * Comment Controller
 * 
 * Handles comment operations
 */
class CommentController extends BaseController {
    private CommentService $commentService;
    
    public function __construct(CommentService $commentService) {
        $this->commentService = $commentService;
    }
    
    /**
     * Create comment
     */
    public function create(): void {
        try {
            $this->requireAuth();
            
            $data = $this->getJsonInput();
            $data['user_id'] = $this->getCurrentUserId();
            
            $comment = $this->commentService->createComment($data);
            $this->success($comment, 'Comment created successfully', 201);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Get comments by post
     */
    public function getByPost(): void {
        try {
            $data = $this->getJsonInput();
            $this->validateRequired($data, ['post_id']);
            
            $comments = $this->commentService->getByPost((int)$data['post_id']);
            $this->success([
                'count' => count($comments),
                'data' => $comments
            ]);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
