<?php

namespace App\Models;

use PDO;

class OrderModel extends BaseModel
{
    protected $table = 'orders';
    protected $primaryKey = 'order_id';

    public function getFullOrderDetails($id)
    {
        $sql = "SELECT o.*, 
                       u.full_name as waiter_name,
                       GROUP_CONCAT(CONCAT(m.meal_name, ' (', od.quantity, ')') SEPARATOR ', ') as items_summary
                FROM orders o
                LEFT JOIN users u ON o.served_by_fk = u.user_id
                LEFT JOIN order_details od ON o.order_id = od.order_fk
                LEFT JOIN meals m ON od.item_fk = m.meal_id
                WHERE o.order_id = :id
                GROUP BY o.order_id";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    public function getAllOrdersWithDetails()
    {
         $sql = "SELECT o.*, u.full_name as waiter_name 
                 FROM orders o 
                 LEFT JOIN users u ON o.served_by_fk = u.user_id 
                 ORDER BY o.order_date DESC";
         $stmt = $this->db->prepare($sql);
         $stmt->execute();
         return $stmt->fetchAll();
    }

    public function updateStatus($id, $status)
    {
        $sql = "UPDATE {$this->table} SET status = :status WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id, 'status' => $status]);
    }

    public function getTotalSales()
    {
        // Include 'Paid', 'Served' (if considered revenue), and legacy 'completed' status
        $stmt = $this->db->query("SELECT SUM(total_amount) as total FROM orders WHERE status IN ('Paid', 'Served', 'completed')");
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getTotalOrdersCount()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM {$this->table}");
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    public function getPendingOrdersCount()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM {$this->table} WHERE status = 'New'");
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    public function getDailySales($days = 7)
    {
        $sql = "SELECT DATE(order_date) as date, SUM(total_amount) as total 
                FROM orders 
                WHERE status IN ('Paid', 'Served', 'completed') AND order_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                GROUP BY DATE(order_date)
                ORDER BY DATE(order_date) ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
