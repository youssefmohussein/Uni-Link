<?php
namespace App\Controllers;

use App\Services\SavedPostService;

/**
 * Saved Post Controller
 * 
 * Handles saved post operations
 */
class SavedPostController extends BaseController {
    private SavedPostService $savedPostService;
    
    public function __construct(SavedPostService $savedPostService) {
        $this->savedPostService = $savedPostService;
    }
    
    /**
     * Save post
     */
    public function save(): void {
        try {
            $this->requireAuth();
            
            $data = $this->getJsonInput();
            $this->validateRequired($data, ['post_id']);
            
            $userId = $this->getCurrentUserId();
            $this->savedPostService->savePost($userId, (int)$data['post_id']);
            
            $this->success(null, 'Post saved successfully');
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Unsave post
     */
    public function unsave(): void {
        try {
            $this->requireAuth();
            
            $data = $this->getJsonInput();
            $this->validateRequired($data, ['post_id']);
            
            $userId = $this->getCurrentUserId();
            $this->savedPostService->unsavePost($userId, (int)$data['post_id']);
            
            $this->success(null, 'Post unsaved successfully');
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Get user's saved posts
     */
    public function getUserSaved(): void {
        try {
            $this->requireAuth();
            
            $userId = $this->getCurrentUserId();
            $posts = $this->savedPostService->getUserSavedPosts($userId);
            
            $this->success([
                'count' => count($posts),
                'data' => $posts
            ]);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
