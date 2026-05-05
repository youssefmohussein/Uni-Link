<?php

namespace App\Models;

class Inventory extends Model
{
    protected static string $table = 'inventory';
    protected array $fillable = [
        'item_name',
        'description',
        'unit',
        'current_stock',
        'low_stock_threshold',
        'last_restocked_at'
    ];

    /**
     * Check if item is low in stock
     */
    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->low_stock_threshold;
    }

    /**
     * Get low stock items
     */
    public static function getLowStock(): array
    {
        $instance = new static();
        $table = $instance->getTable();
        $sql = "SELECT * FROM `{$table}` WHERE `current_stock` <= `low_stock_threshold`";
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

    /**
     * Update stock
     */
    public function updateStock(float $quantity): bool
    {
        $this->current_stock = $quantity;
        $this->last_restocked_at = date('Y-m-d H:i:s');
        return $this->save();
    }
}

