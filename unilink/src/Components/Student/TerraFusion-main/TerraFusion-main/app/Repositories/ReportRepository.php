<?php

namespace App\Repositories;

use App\Libs\Database;
use PDO;

class ReportRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get daily sales report
     */
    public function getDailySales(string $date = null): array
    {
        $date = $date ?: date('Y-m-d');
        
        $sql = "SELECT 
                    DATE(p.created_at) as sale_date,
                    COUNT(DISTINCT p.order_id) as total_orders,
                    SUM(p.amount) as total_revenue,
                    AVG(p.amount) as average_order_value
                FROM payments p
                WHERE p.status = 'completed'
                AND DATE(p.created_at) = :date
                GROUP BY DATE(p.created_at)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':date' => $date]);
        
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $results[] = $row;
        }
        
        return $results;
    }

    /**
     * Get popular items report
     */
    public function getPopularItems(int $limit = 10, string $startDate = null, string $endDate = null): array
    {
        $dateFilter = '';
        $params = [];
        
        if ($startDate && $endDate) {
            $dateFilter = "AND DATE(o.order_date) BETWEEN :start_date AND :end_date";
            $params[':start_date'] = $startDate;
            $params[':end_date'] = $endDate;
        }
        
        $sql = "SELECT 
                    m.meal_id as id,
                    m.meal_name as name,
                    m.price,
                    SUM(od.quantity) as total_quantity,
                    SUM(od.quantity * od.price_at_sale) as total_revenue
                FROM order_details od
                INNER JOIN meals m ON od.item_fk = m.meal_id
                INNER JOIN orders o ON od.order_fk = o.order_id
                WHERE o.status != 'cancelled'
                {$dateFilter}
                GROUP BY m.meal_id, m.meal_name, m.price
                ORDER BY total_quantity DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $results[] = $row;
        }
        
        return $results;
    }

    /**
     * Get peak ordering times
     */
    public function getPeakOrderingTimes(string $startDate = null, string $endDate = null): array
    {
        $dateFilter = '';
        $params = [];
        
        if ($startDate && $endDate) {
            $dateFilter = "WHERE DATE(o.order_date) BETWEEN :start_date AND :end_date";
            $params[':start_date'] = $startDate;
            $params[':end_date'] = $endDate;
        }
        
        $sql = "SELECT 
                    HOUR(o.order_date) as order_hour,
                    COUNT(*) as order_count,
                    SUM(o.total_amount) as total_revenue
                FROM orders o
                {$dateFilter}
                GROUP BY HOUR(o.order_date)
                ORDER BY order_count DESC";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $results[] = $row;
        }
        
        return $results;
    }

    /**
     * Get staff performance report
     */
    public function getStaffPerformance(string $startDate = null, string $endDate = null): array
    {
        $dateFilter = '';
        $params = [];
        
        if ($startDate && $endDate) {
            $dateFilter = "AND DATE(o.order_date) BETWEEN :start_date AND :end_date";
            $params[':start_date'] = $startDate;
            $params[':end_date'] = $endDate;
        }
        
        $sql = "SELECT 
                    u.user_id as id,
                    u.full_name,
                    COUNT(DISTINCT o.order_id) as total_orders_processed,
                    SUM(o.total_amount) as total_revenue,
                    AVG(o.total_amount) as average_order_value
                FROM orders o
                INNER JOIN users u ON o.served_by_fk = u.user_id
                WHERE u.role IN ('Waiter', 'Chef Boss', 'Manager')
                {$dateFilter}
                GROUP BY u.user_id, u.full_name
                ORDER BY total_revenue DESC";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $results[] = $row;
        }
        
        return $results;
    }

    /**
     * Get sales summary for date range
     */
    public function getSalesSummary(string $startDate, string $endDate): object
    {
        $sql = "SELECT 
                    COUNT(DISTINCT o.order_id) as total_orders,
                    SUM(p.amount) as total_revenue,
                    AVG(p.amount) as average_order_value,
                    COUNT(DISTINCT o.customer_name) as unique_customers
                FROM orders o
                INNER JOIN payments p ON o.order_id = p.order_id
                WHERE p.status = 'completed'
                AND DATE(o.order_date) BETWEEN :start_date AND :end_date";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':start_date' => $startDate,
            ':end_date' => $endDate
        ]);
        
        return $stmt->fetch(PDO::FETCH_OBJ) ?: (object)[];
    }
}

