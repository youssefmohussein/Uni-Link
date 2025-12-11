<?php
namespace App\Controllers;

use App\Services\SkillService;

/**
 * Skill Controller
 * 
 * Handles skill operations
 */
class SkillController extends BaseController {
    private SkillService $skillService;
    
    public function __construct(SkillService $skillService) {
        $this->skillService = $skillService;
    }
    
    /**
     * Get all skills
     */
    public function getAll(): void {
        try {
            $skills = $this->skillService->getAllSkills();
            $this->success([
                'count' => count($skills),
                'data' => $skills
            ]);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Get skill categories
     */
    public function getCategories(): void {
        try {
            // This would need a separate method in service
            // For now, return empty array
            $this->success([
                'count' => 0,
                'data' => []
            ]);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
