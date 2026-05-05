<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\ReservationModel;
use App\Models\MenuItemModel; // CRITICAL FIX: Correct namespace
use App\Controllers\AuthController;

class DashboardController
{
    private $orderModel;
    private $reservationModel;
    private $menuItemModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->reservationModel = new ReservationModel();
        $this->menuItemModel = new MenuItemModel(); 
    }

    public function index()
    {
        // Fetch stats
        $totalSales = $this->orderModel->getTotalSales();
        $pendingOrders = $this->orderModel->getPendingOrdersCount();
        $todayReservations = $this->reservationModel->getTodayReservationsCount();
        $totalMenuItems = count($this->menuItemModel->getAll()); // Simple count for now
        
        // Fetch Chart Data
        $dailySales = $this->orderModel->getDailySales(7);
        $mealTypeCounts = $this->menuItemModel->getMealTypeCounts();

        // Pass data to view
        $data = [
            'totalSales' => $totalSales,
            'pendingOrders' => $pendingOrders,
            'todayReservations' => $todayReservations,
            'totalMenuItems' => $totalMenuItems,
            'dailySales' => $dailySales,
            'mealTypeCounts' => $mealTypeCounts
        ];

        // Load View
        $content = __DIR__ . '/../Views/dashboard/index.php';
        include __DIR__ . '/../Views/shared/layout.php';
    }
}
