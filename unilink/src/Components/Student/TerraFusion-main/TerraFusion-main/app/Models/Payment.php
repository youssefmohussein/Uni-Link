<?php

namespace App\Models;

class Payment extends Model
{
    protected static string $table = 'payments';
    protected array $fillable = [
        'order_id',
        'method',
        'amount',
        'transaction_id',
        'status'
    ];

    // Payment methods
    public const METHOD_CASH = 'cash';
    public const METHOD_CARD = 'card';
    public const METHOD_MOBILE = 'mobile';

    // Payment statuses
    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';

    /**
     * Get the order
     */
    public function order(): ?Order
    {
        return Order::find($this->order_id);
    }

    /**
     * Mark payment as completed
     */
    public function markCompleted(): bool
    {
        $this->status = self::STATUS_COMPLETED;
        return $this->save();
    }

    /**
     * Mark payment as failed
     */
    public function markFailed(): bool
    {
        $this->status = self::STATUS_FAILED;
        return $this->save();
    }
}

