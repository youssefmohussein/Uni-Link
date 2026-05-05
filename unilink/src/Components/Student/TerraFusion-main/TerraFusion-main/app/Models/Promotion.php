<?php

namespace App\Models;

use DateTime;

class Promotion extends Model
{
    protected static string $table = 'promotions';
    protected array $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'max_discount',
        'start_date',
        'end_date',
        'max_uses',
        'uses_count',
        'is_active'
    ];

    // Discount types
    public const TYPE_PERCENT = 'percent';
    public const TYPE_FIXED = 'fixed';

    /**
     * Find promotion by code
     */
    public static function findByCode(string $code): ?self
    {
        $instance = new static();
        $table = $instance->getTable();
        $stmt = $instance->prepare("SELECT * FROM `{$table}` WHERE `code` = :code AND `is_active` = 1 LIMIT 1");
        $stmt->execute([':code' => $code]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        $instance->fill($result);
        $instance->exists = true;
        return $instance;
    }

    /**
     * Check if promotion is valid
     */
    public function isValid(float $orderAmount = 0): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = new DateTime();
        $startDate = new DateTime($this->start_date);
        $endDate = new DateTime($this->end_date);

        if ($now < $startDate || $now > $endDate) {
            return false;
        }

        if ($this->max_uses !== null && $this->uses_count >= $this->max_uses) {
            return false;
        }

        if ($orderAmount > 0 && $this->min_order_amount > $orderAmount) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount(float $orderAmount): float
    {
        if (!$this->isValid($orderAmount)) {
            return 0;
        }

        if ($this->discount_type === self::TYPE_PERCENT) {
            $discount = ($orderAmount * $this->discount_value) / 100;
            if ($this->max_discount && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }
            return round($discount, 2);
        } else {
            return min($this->discount_value, $orderAmount);
        }
    }

    /**
     * Increment usage count
     */
    public function incrementUsage(): bool
    {
        $this->uses_count = ($this->uses_count ?? 0) + 1;
        return $this->save();
    }
}

