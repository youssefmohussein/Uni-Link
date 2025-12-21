<?php
namespace App\Controllers;

use App\Services\SkillService;

/**
 * User Skill Controller
 * 
 * Handles user skill operations
 */
class UserSkillController extends BaseController {
    private SkillService $skillService;
    
    public function __construct(SkillService $skillService) {
        $this->skillService = $skillService;
    }
    
    /**
     * Get user skills
     */
    public function getUserSkills(): void {
        try {
            $userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : $this->getCurrentUserId();
            
            if (!$userId) {
                throw new \Exception('User ID is required', 400);
            }
            
            $skills = $this->skillService->getUserSkills($userId);
            $skills = $this->skillService->getUserSkills($userId);
            // Return skills directly as array, as expected by SkillsSection.jsx
            $this->success($skills);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), (int)($e->getCode() ?: 400));
        }
    }
    
    /**
     * Add skill to user
     */
    public function add(): void {
        try {
            $this->requireAuth();
            
            $data = $this->getJsonInput();
            $this->validateRequired($data, ['skill_id']);
            
            $userId = $this->getCurrentUserId();
            $proficiency = $data['proficiency'] ?? 3;
            
            $this->skillService->addUserSkill($userId, (int)$data['skill_id'], (int)$proficiency);
            $this->success(null, 'Skill added successfully');
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), (int)($e->getCode() ?: 400));
        }
    }
    
    /**
     * Remove skill from user
     */
    public function delete(): void {
        try {
            $this->requireAuth();
            
            $data = $this->getJsonInput();
            $this->validateRequired($data, ['skill_id']);
            
            $userId = $this->getCurrentUserId();
            $this->skillService->removeUserSkill($userId, (int)$data['skill_id']);
            
            $this->success(null, 'Skill removed successfully');
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), (int)($e->getCode() ?: 400));
        }
    }
}
