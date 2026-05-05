<?php

namespace App\Models;

class Table extends Model
{
    protected static string $table = 'tables';
    protected array $fillable = [
        'table_number',
        'capacity',
        'status'
    ];

    // Table statuses
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_OCCUPIED = 'occupied';
    public const STATUS_RESERVED = 'reserved';

    /**
     * Get available tables
     */
    public static function getAvailable(): array
    {
        return self::where('status', self::STATUS_AVAILABLE);
    }

    /**
     * Check if table is available
     */
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    /**
     * Occupy table
     */
    public function occupy(): bool
    {
        $this->status = self::STATUS_OCCUPIED;
        return $this->save();
    }

    /**
     * Free table
     */
    public function free(): bool
    {
        $this->status = self::STATUS_AVAILABLE;
        return $this->save();
    }
}

