<?php
namespace App\Controllers;

use App\Services\PostInteractionService;

/**
 * Post Interaction Controller
 * 
 * Handles post interactions (like, love, etc.)
 */
class PostInteractionController extends BaseController {
    private PostInteractionService $interactionService;
    
    public function __construct(PostInteractionService $interactionService) {
        $this->interactionService = $interactionService;
    }
    
    /**
     * Add interaction
     */
    public function add(): void {
        try {
            $this->requireAuth();
            
            $data = $this->getJsonInput();
            $this->validateRequired($data, ['post_id', 'type']);
            
            $userId = $this->getCurrentUserId();
            $this->interactionService->addInteraction(
                (int)$data['post_id'],
                $userId,
                $data['type']
            );
            
            $this->success(null, 'Interaction added successfully');
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Delete interaction
     */
    public function delete(): void {
        try {
            $this->requireAuth();
            
            $data = $this->getJsonInput();
            $this->validateRequired($data, ['post_id', 'type']);
            
            $userId = $this->getCurrentUserId();
            $this->interactionService->removeInteraction(
                (int)$data['post_id'],
                $userId,
                $data['type']
            );
            
            $this->success(null, 'Interaction removed successfully');
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Get interactions by post
     */
    public function getByPost(): void {
        try {
            $data = $this->getJsonInput();
            $this->validateRequired($data, ['post_id']);
            
            $interactions = $this->interactionService->getByPost((int)$data['post_id']);
            $this->success($interactions);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Get user reaction
     */
    public function getUserReaction(): void {
        try {
            $this->requireAuth();
            
            $data = $this->getJsonInput();
            $this->validateRequired($data, ['post_id']);
            
            $userId = $this->getCurrentUserId();
            $reaction = $this->interactionService->getUserReaction((int)$data['post_id'], $userId);
            
            $this->success($reaction);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Get reaction counts
     */
    public function getReactionCounts(): void {
        try {
            $data = $this->getJsonInput();
            $this->validateRequired($data, ['post_id']);
            
            $counts = $this->interactionService->getReactionCounts((int)$data['post_id']);
            $this->success($counts);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
