<?php

namespace App\Models;

class Category extends Model
{
    protected static string $table = 'categories';
    protected array $fillable = [
        'name',
        'description',
        'image_path',
        'is_active'
    ];

    /**
     * Get menu items in this category
     */
    public function menuItems(): array
    {
        return MenuItem::where('category_id', $this->id);
    }

    /**
     * Get active categories
     */
    public static function getActive(): array
    {
        $instance = new static();
        $table = $instance->getTable();
        $sql = "SELECT * FROM `{$table}` WHERE `is_active` = 1 ORDER BY `name`";
        $stmt = $instance->prepare($sql);
        $stmt->execute();

        $results = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $model = new static();
            $model->fill($row);
            $model->exists = true;
            $results[] = $model;
        }

        return $results;
    }
}

