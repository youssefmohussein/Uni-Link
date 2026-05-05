<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Libs\Database;
use PDO;

class OrderRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function find(int $id): ?Order
    {
        return Order::find($id);
    }

    public function all(): array
    {
        return Order::all();
    }

    public function create(array $data): Order
    {
        $order = new Order($data);
        $order->save();
        return $order;
    }

    public function update(int $id, array $data): bool
    {
        $order = $this->find($id);
        if (!$order) {
            return false;
        }

        $order->fill($data);
        return $order->save();
    }

    public function delete(int $id): bool
    {
        $order = $this->find($id);
        return $order ? $order->delete() : false;
    }

    /**
     * Get order with items and customer info
     */
    public function getOrderWithDetails(int $orderId): ?object
    {
        // New schema: orders has customer_name, table_number, served_by_fk
        $sql = "SELECT o.*, u.full_name as server_name
                FROM orders o
                LEFT JOIN users u ON o.served_by_fk = u.user_id
                WHERE o.order_id = :id
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $orderId]);
        $order = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($order) {
            $order->items = $this->getOrderItems($orderId);
        }
        
        return $order;
    }

    /**
     * Get order items with menu item details
     */
    public function getOrderItems(int $orderId): array
    {
        $sql = "SELECT od.*, m.meal_name as item_name, m.image as image_path
                FROM order_details od
                INNER JOIN meals m ON od.item_fk = m.meal_id
                WHERE od.order_fk = :order_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':order_id' => $orderId]);
        
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $results[] = $row;
        }
        
        return $results;
    }

    /**
     * Get orders by status
     */
    public function getByStatus(string $status): array
    {
        return Order::getByStatus($status);
    }

    /**
     * Get orders for a specific customer name
     */
    public function getByCustomer(string $customerName): array
    {
        $sql = "SELECT o.*, COUNT(od.detail_id) as item_count
                FROM orders o
                LEFT JOIN order_details od ON o.order_id = od.order_fk
                WHERE o.customer_name = :customer_name
                GROUP BY o.order_id
                ORDER BY o.order_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':customer_name' => $customerName]);
        
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $results[] = $row;
        }
        
        return $results;
    }

    /**
     * Get pending orders for kitchen
     */
    public function getPendingOrders(): array
    {
        $sql = "SELECT o.*, 
                COUNT(od.detail_id) as item_count
                FROM orders o
                LEFT JOIN order_details od ON o.order_id = od.order_fk
                WHERE o.status IN ('New', 'Preparing')
                GROUP BY o.order_id
                ORDER BY o.order_date ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $row->items = $this->getOrderItems($row->order_id);
            $results[] = $row;
        }
        
        return $results;
    }

    /**
     * Update order status
     */
    public function updateStatus(int $orderId, string $status): bool
    {
        $order = $this->find($orderId);
        if (!$order) {
            return false;
        }

        return $order->updateStatus($status);
    }

    /**
     * Add item to order
     */
    public function addItemToOrder(int $orderId, int $mealId, int $quantity): bool
    {
        $menuItem = MenuItem::find($mealId);
        if (!$menuItem) {
            return false;
        }

        $orderItem = new OrderItem([
            'order_fk' => $orderId,
            'item_fk' => $mealId,
            'quantity' => $quantity,
            'price_at_sale' => $menuItem->price
        ]);

        return $orderItem->save();
    }
}

