<?php

namespace App\Models;

class Review extends Model
{
    protected static string $table = 'reviews';
    protected array $fillable = [
        'user_id',
        'order_id',
        'rating',
        'comment',
        'is_approved'
    ];

    /**
     * Get the user who wrote the review
     */
    public function user(): ?User
    {
        return User::find($this->user_id);
    }

    /**
     * Get the order
     */
    public function order(): ?Order
    {
        return Order::find($this->order_id);
    }

    /**
     * Get reviews for a menu item (through orders)
     */
    public static function getApprovedReviews(): array
    {
        $instance = new static();
        $table = $instance->getTable();
        $sql = "SELECT * FROM `{$table}` WHERE `is_approved` = 1 ORDER BY `created_at` DESC";
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

