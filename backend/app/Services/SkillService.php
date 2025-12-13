<?php
namespace App\Services;

use App\Repositories\SkillRepository;
use App\Repositories\UserRepository;

/**
 * Skill Service
 * 
 * Business logic for skill management
 */
class SkillService extends BaseService {
    private SkillRepository $skillRepo;
    private UserRepository $userRepo;
    
    public function __construct(SkillRepository $skillRepo, UserRepository $userRepo) {
        $this->skillRepo = $skillRepo;
        $this->userRepo = $userRepo;
    }
    
    /**
     * Add skill to user
     * 
     * @param int $userId User ID
     * @param int $skillId Skill ID
     * @param int $proficiency Proficiency level (1-5)
     * @return bool Success status
     */
    public function addUserSkill(int $userId, int $skillId, int $proficiency = 3): bool {
        // Validate user exists
        if (!$this->userRepo->exists($userId)) {
            throw new \Exception('User not found', 404);
        }
        
        // Validate skill exists
        if (!$this->skillRepo->exists($skillId)) {
            throw new \Exception('Skill not found', 404);
        }
        
        // Validate proficiency
        if ($proficiency < 1 || $proficiency > 5) {
            throw new \Exception('Proficiency must be between 1 and 5', 400);
        }
        
        return $this->skillRepo->addUserSkill($userId, $skillId, $proficiency);
    }
    
    /**
     * Remove skill from user
     * 
     * @param int $userId User ID
     * @param int $skillId Skill ID
     * @return bool Success status
     */
    public function removeUserSkill(int $userId, int $skillId): bool {
        return $this->skillRepo->removeUserSkill($userId, $skillId);
    }
    
    /**
     * Get user skills
     * 
     * @param int $userId User ID
     * @return array Array of user skills
     */
    public function getUserSkills(int $userId): array {
        return $this->skillRepo->findUserSkills($userId);
    }
    
    /**
     * Get all skills
     * 
     * @return array Array of skills
     */
    public function getAllSkills(): array {
        return $this->skillRepo->findAll();
    }
    
    /**
     * Get skills by category
     * 
     * @param int $categoryId Category ID
     * @return array Array of skills
     */
    public function getSkillsByCategory(int $categoryId): array {
        return $this->skillRepo->findBy('category_id', $categoryId);
    }
    
    /**
     * Create a new skill
     */
    public function createSkill(string $skillName, int $categoryId): int {
        return $this->skillRepo->createSkill($skillName, $categoryId);
    }
    
    /**
     * Create a new category
     */
    public function createCategory(string $categoryName): int {
        return $this->skillRepo->createCategory($categoryName);
    }

    /**
     * Get all categories
     */
    public function getAllCategories(): array {
        return $this->skillRepo->getAllCategories();
    }
}
