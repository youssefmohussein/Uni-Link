<?php

namespace App\Services;

use App\Models\Order;

/**
 * Order Type Factory
 * Factory Pattern for creating different order types
 */
class OrderTypeFactory
{
    /**
     * Create order based on type
     * @param string $type Order type (dine_in, takeaway, delivery)
     * @param array $data Order data
     * @return Order
     * @throws \InvalidArgumentException
     */
    public static function createOrder(string $type, array $data): Order
    {
        switch (strtolower($type)) {
            case 'dine_in':
                return self::createDineInOrder($data);
            
            case 'takeaway':
                return self::createTakeawayOrder($data);
            
            case 'delivery':
                return self::createDeliveryOrder($data);
            
            default:
                throw new \InvalidArgumentException("Invalid order type: {$type}");
        }
    }

    /**
     * Create dine-in order
     */
    private static function createDineInOrder(array $data): Order
    {
        $orderData = [
            'customer_name' => $data['customer_name'] ?? 'Guest',
            'table_number' => $data['table_number'] ?? null,
            'total_amount' => $data['total_amount'] ?? 0,
            'status' => Order::STATUS_NEW,
            'served_by_fk' => $data['served_by_fk'] ?? null
        ];

        return new Order($orderData);
    }

    /**
     * Create takeaway order
     */
    private static function createTakeawayOrder(array $data): Order
    {
        $orderData = [
            'customer_name' => $data['customer_name'] ?? 'Guest',
            'table_number' => null,
            'total_amount' => $data['total_amount'] ?? 0,
            'status' => Order::STATUS_NEW,
            'served_by_fk' => $data['served_by_fk'] ?? null
        ];

        return new Order($orderData);
    }

    /**
     * Create delivery order
     */
    private static function createDeliveryOrder(array $data): Order
    {
        $orderData = [
            'customer_name' => $data['customer_name'] ?? 'Guest',
            'table_number' => null,
            'total_amount' => $data['total_amount'] ?? 0,
            'status' => Order::STATUS_NEW,
            'served_by_fk' => $data['served_by_fk'] ?? null
        ];

        return new Order($orderData);
    }
}

