<?php

namespace App\Models;

class MenuItem extends Model
{
    protected static string $table = 'meals';
    protected string $primaryKey = 'meal_id';
    protected array $fillable = [
        'meal_name',
        'meal_type',
        'description',
        'price',
        'image',
        'availability',
        'quantity'
    ];

    /**
     * Get the category/meal type name (kept for backward compatibility)
     */
    public function getCategoryAttribute(): string
    {
        return $this->meal_type ?? '';
    }

    /**
     * Check if item is available
     */
    public function isAvailable(): bool
    {
        return $this->availability === 'Available' && $this->quantity > 0;
    }

    /**
     * Get items by category (meal_type)
     */
    public static function getByCategory(string $category): array
    {
        return self::where('meal_type', $category);
    }

    /**
     * Get available items only
     */
    public static function getAvailable(): array
    {
        return self::where('availability', 'Available');
    }
}

