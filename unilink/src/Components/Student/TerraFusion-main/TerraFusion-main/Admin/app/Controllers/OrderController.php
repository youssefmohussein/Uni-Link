<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Controllers\AuthController;

class OrderController
{
    private $orderModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
    }

    public function index()
    {
        $orders = $this->orderModel->getAllOrdersWithDetails();
        
        $data = [
            'orders' => $orders
        ];

        $content = __DIR__ . '/../Views/orders/index.php';
        include __DIR__ . '/../Views/shared/layout.php';
    }

    public function updateStatus()
    {
        AuthController::requireLogin();
        
        $id = $_REQUEST['id'] ?? null;
        $status = $_REQUEST['status'] ?? null;

        if ($id && $status) {
            $this->orderModel->updateStatus($id, $status);
        }
        
        header("Location: /TerraFusion/Admin/public/index.php?page=orders");
        exit();
    }
}
