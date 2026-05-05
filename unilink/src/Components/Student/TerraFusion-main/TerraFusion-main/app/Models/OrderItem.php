<?php

namespace App\Models;

class OrderItem extends Model
{
    protected static string $table = 'order_details';
    protected string $primaryKey = 'detail_id';
    protected array $fillable = [
        'order_fk',
        'item_fk',
        'quantity',
        'price_at_sale'
    ];

    /**
     * Get the order
     */
    public function order(): ?Order
    {
        return Order::find($this->order_fk);
    }

    /**
     * Get the menu item
     */
    public function menuItem(): ?MenuItem
    {
        return MenuItem::find($this->item_fk);
    }

    /**
     * Calculate subtotal
     */
    public function getSubtotal(): float
    {
        return $this->quantity * $this->price_at_sale;
    }
}
