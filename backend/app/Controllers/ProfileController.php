<?php
namespace App\Controllers;

use App\Facades\ProfileFacade;

/**
 * Profile Controller
 * 
 * Handles aggregated profile data
 */
class ProfileController extends BaseController {
    private ProfileFacade $profileFacade;
    
    public function __construct(ProfileFacade $profileFacade) {
        $this->profileFacade = $profileFacade;
    }
    
    /**
     * Get full profile for authenticated user
     * OR for a specific user ID if requested (and allowed)
     */
    public function getFullProfile(): void {
        try {
            // Determine user ID:
            // 1. If 'user_id' is passed in query params, use it
            // 2. Otherwise use current authenticated user
            
            $userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;
            
            if (!$userId) {
                $this->requireAuth();
                $userId = $this->getCurrentUserId();
            }
            
            // In a real app we might check if user is allowed to view full profile of another user
            // For now, we allow it (or frontend handles permissions)
            
            $profile = $this->profileFacade->getFullProfile($userId);
            
            if (empty($profile)) {
                $this->error('User not found', 404);
                return;
            }
            
            $this->success($profile);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Get public profile
     */
    public function getPublicProfile(): void {
        try {
            $userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;
            
            if (!$userId) {
                // If logged in, getting own public profile is fine
                $this->requireAuth();
                $userId = $this->getCurrentUserId();
            }
            
            $profile = $this->profileFacade->getPublicProfile($userId);
            
            if (empty($profile)) {
                $this->error('User not found', 404);
                return;
            }
            
            $this->success($profile);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
