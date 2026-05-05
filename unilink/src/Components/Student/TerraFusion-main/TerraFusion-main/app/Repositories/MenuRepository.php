<?php

namespace App\Repositories;

use App\Models\MenuItem;
use App\Models\Category;
use App\Libs\Database;
use PDO;

class MenuRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function find(int $id): ?MenuItem
    {
        return MenuItem::find($id);
    }

    public function all(): array
    {
        return MenuItem::all();
    }

    public function create(array $data): MenuItem
    {
        $item = new MenuItem($data);
        $item->save();
        return $item;
    }

    public function update(int $id, array $data): bool
    {
        $item = $this->find($id);
        if (!$item) {
            return false;
        }

        $item->fill($data);
        return $item->save();
    }

    public function delete(int $id): bool
    {
        $item = $this->find($id);
        return $item ? $item->delete() : false;
    }

    /**
     * Get all menu items
     */
    public function getAllAvailable(): array
    {
        $sql = "SELECT * FROM meals WHERE availability = 'Available' AND quantity > 0 ORDER BY meal_type, meal_name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $results[] = $row;
        }
        
        return $results;
    }

    /**
     * Get menu items by category
     */
    public function getByCategory(string $category): array
    {
        return MenuItem::getByCategory($category);
    }

    /**
     * Search menu items
     */
    public function search(string $query): array
    {
        $sql = "SELECT * FROM meals 
                WHERE availability = 'Available' 
                AND (meal_name LIKE :query OR description LIKE :query)
                ORDER BY meal_name";
        
        $stmt = $this->db->prepare($sql);
        $searchQuery = '%' . $query . '%';
        $stmt->execute([':query' => $searchQuery]);
        
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $results[] = $row;
        }
        
        return $results;
    }

    /**
     * Get all meal types
     */
    /**
     * Get all categories
     */
    public function getCategories(): array
    {
        $sql = "SELECT DISTINCT meal_type FROM meals ORDER BY meal_type";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}

