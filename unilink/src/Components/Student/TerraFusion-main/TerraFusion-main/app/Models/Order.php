<?php

namespace App\Models;

use PDO;
use PDOStatement;
use DateTime;

class Order extends Model
{
    protected string $primaryKey = 'order_id';
    protected array $fillable = [
        'customer_name',
        'table_number',
        'total_amount',
        'status',
        'order_date',
        'served_by_fk'
    ];

    // Order status constants
    public const STATUS_NEW = 'New';
    public const STATUS_PREPARING = 'Preparing';
    public const STATUS_READY = 'Ready';
    public const STATUS_SERVED = 'Served';
    public const STATUS_PAID = 'Paid';

    // Order type constants - These are no longer in the schema as a column, but kept for logic if needed
    public const TYPE_DINE_IN = 'dine_in';
    public const TYPE_TAKEAWAY = 'takeaway';
    public const TYPE_DELIVERY = 'delivery';

    // Get the staff member who served the order
    public function servedBy(): ?User
    {
        return $this->served_by_fk ? User::find($this->served_by_fk) : null;
    }

    // Get all items in the order
    public function items(): array
    {
        return OrderItem::where('order_fk', $this->order_id);
    }

    // Get the table number
    public function tableNumber(): ?int
    {
        return $this->table_number;
    }

    // Add an item to the order
    public function addItem(MenuItem $item, int $quantity = 1): bool
    {
        $orderItem = new OrderItem([
            'order_fk' => $this->order_id,
            'item_fk' => $item->meal_id,
            'quantity' => $quantity,
            'price_at_sale' => $item->price
        ]);

        return $orderItem->save();
    }

    // Update order status
    public function updateStatus(string $status): bool
    {
        $validStatuses = [
            self::STATUS_NEW,
            self::STATUS_PREPARING,
            self::STATUS_READY,
            self::STATUS_SERVED,
            self::STATUS_PAID
        ];

        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException('Invalid order status');
        }

        $this->status = $status;
        return $this->save();
    }

    // Calculate total amount based on order items
    public function calculateTotal(): float
    {
        $total = 0;
        foreach ($this->items() as $item) {
            $total += $item->quantity * $item->price_at_sale;
        }
        return $total;
    }

    // Get orders by status
    public static function getByStatus(string $status): array
    {
        return self::where('status', $status);
    }

    // Get orders for a specific customer
    public static function getByCustomer(string $customerName): array
    {
        return self::where('customer_name', $customerName);
    }

    // Get today's orders
    public static function getTodaysOrders(): array
    {
        $instance = new static();
        $table = $instance->getTable();
        
        $today = (new DateTime())->format('Y-m-d');
        $sql = "SELECT * FROM `{$table}` WHERE DATE(order_date) = :today";
        $stmt = $instance->prepare($sql);
        $stmt->execute([':today' => $today]);
        
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $model = new static();
            $model->fill($row);
            $model->exists = true;
            $results[] = $model;
        }
        
        return $results;
    }

    // Check if order can be cancelled
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            self::STATUS_NEW
        ]);
    }
}
