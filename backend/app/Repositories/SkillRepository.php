<?php
namespace App\Repositories;

/**
 * Skill Repository
 * 
 * Handles database operations for skills
 */
class SkillRepository extends BaseRepository {
    protected string $table = 'skills';
    protected string $primaryKey = 'skill_id';
    
    /**
     * Find skills by category
     * 
     * @param int $categoryId Category ID
     * @return array Array of skills
     */
    public function findByCategory(int $categoryId): array {
        return $this->query("
            SELECT * FROM {$this->table} WHERE category_id = ?
        ", [$categoryId]);
    }
    
    /**
     * Find user skills
     * 
     * @param int $userId User ID
     * @return array Array of skills with categories
     */
    public function findUserSkills(int $userId): array {
        return $this->query("
            SELECT s.*, sc.name as category_name, us.proficiency_level
            FROM user_skills us
            JOIN {$this->table} s ON us.skill_id = s.skill_id
            JOIN skill_categories sc ON s.category_id = sc.category_id
            WHERE us.user_id = ?
        ", [$userId]);
    }
    
    /**
     * Add user skill
     * 
     * @param int $userId User ID
     * @param int $skillId Skill ID
     * @param string|null $proficiency Proficiency level
     * @return int Insert ID
     */
    public function addUserSkill(int $userId, int $skillId, ?string $proficiency = null): int {
        $stmt = $this->db->prepare("
            INSERT INTO user_skills (user_id, skill_id, proficiency_level)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$userId, $skillId, $proficiency]);
        return (int)$this->db->lastInsertId();
    }
    
    /**
     * Remove user skill
     * 
     * @param int $userId User ID
     * @param int $skillId Skill ID
     * @return bool Success
     */
    public function removeUserSkill(int $userId, int $skillId): bool {
        $stmt = $this->db->prepare("
            DELETE FROM user_skills WHERE user_id = ? AND skill_id = ?
        ");
        return $stmt->execute([$userId, $skillId]);
    }
    
    /**
     * Get all skill categories
     * 
     * @return array Array of categories
     */
    public function getAllCategories(): array {
        return $this->query("SELECT * FROM skill_categories ORDER BY name");
    }

    /**
     * Create a new skill
     * 
     * @param string $skillName Skill name
     * @param int $categoryId Category ID
     * @return int New skill ID
     */
    public function createSkill(string $skillName, int $categoryId): int {
        $sql = "INSERT INTO skills (name, category_id) VALUES (?, ?)";
        $this->execute($sql, [$skillName, $categoryId]);
        return (int)$this->db->lastInsertId();
    }
    
    /**
     * Create a new skill category
     * 
     * @param string $categoryName Category name
     * @return int New category ID
     */
    public function createCategory(string $categoryName): int {
        // Check if exists first
        $existing = $this->queryOne("SELECT category_id FROM skill_categories WHERE name = ?", [$categoryName]);
        if ($existing) {
            return $existing['category_id'];
        }
        
        $sql = "INSERT INTO skill_categories (name) VALUES (?)";
        $this->execute($sql, [$categoryName]);
        return (int)$this->db->lastInsertId();
    }
}
