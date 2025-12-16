<?php
namespace App\Controllers;

use App\Services\SkillService;

/**
 * Skill Category Controller
 */
class SkillCategoryController extends BaseController {
    private SkillService $skillService;
    
    public function __construct(SkillService $skillService) {
        $this->skillService = $skillService;
    }
    
    /**
     * Get all categories
     */
    public function getAll(): void {
        try {
            $categories = $this->skillService->getAllCategories();
            $this->success([
                'count' => count($categories),
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Create a new category
     */
    public function create(): void {
        try {
            $data = $this->getJsonInput();
            
            if (!isset($data['category_name'])) {
                $this->error('Category name is required', 400);
            }
            
            $categoryId = $this->skillService->createCategory($data['category_name']);
            
            $this->success([
                'category_id' => $categoryId,
                'message' => 'Category created successfully'
            ]);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), 500);
        }
    }
}
