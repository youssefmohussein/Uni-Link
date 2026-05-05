<?php

namespace App\Models;

use PDO;

class MenuItemModel extends BaseModel
{
    protected $table = 'meals';
    protected $primaryKey = 'meal_id';

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (meal_name, description, price, meal_type, image, availability, quantity) VALUES (:meal_name, :description, :price, :meal_type, :image, :availability, :quantity)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'meal_name' => $data['meal_name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'meal_type' => $data['meal_type'] ?? 'Main Courses',
            'image' => $data['image'] ?? null,
            'availability' => $data['availability'] ?? 'Available',
            'quantity' => $data['quantity'] ?? 0
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET meal_name = :meal_name, description = :description, price = :price, meal_type = :meal_type, image = :image, availability = :availability, quantity = :quantity WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'meal_name' => $data['meal_name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'meal_type' => $data['meal_type'],
            'image' => $data['image'] ?? null,
            'availability' => $data['availability'] ?? 'Available',
            'quantity' => $data['quantity'] ?? 0
        ]);
    }

    public function getUniqueMealTypes()
    {
        $sql = "SELECT DISTINCT meal_type FROM {$this->table} WHERE meal_type IS NOT NULL AND meal_type != ''";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getGroupedByMealType()
    {
        $items = $this->getAll();
        $grouped = [];
        foreach ($items as $item) {
            $grouped[$item['meal_type']][] = $item;
        }
        return $grouped;
    }

    public function getMealTypeCounts()
    {
        $sql = "SELECT meal_type as label, COUNT(*) as value FROM {$this->table} GROUP BY meal_type";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
