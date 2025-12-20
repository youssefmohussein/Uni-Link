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
            // For GET requests, use query parameters instead of JSON body
            $data = [];
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                if (isset($_GET['post_id'])) {
                    $data['post_id'] = $_GET['post_id'];
                }
            } else {
                // For POST/PUT requests, try to get JSON input
                try {
                    $data = $this->getJsonInput();
                } catch (\Exception $e) {
                    // If JSON parsing fails, fall back to empty array
                    $data = [];
                }
            }
            
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
