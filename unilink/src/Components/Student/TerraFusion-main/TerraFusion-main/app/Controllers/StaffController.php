<?php

namespace App\Controllers;

use App\Repositories\OrderRepository;
use App\Models\Order;
use App\Models\Table;
use App\Middlewares\StaffMiddleware;

class StaffController extends BaseController
{
    private OrderRepository $orderRepository;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
    }

    /**
     * Show staff dashboard
     */
    public function dashboard(): void
    {
        $middleware = new StaffMiddleware();
        $middleware->handle();
        
        $pendingOrders = $this->orderRepository->getByStatus(Order::STATUS_CONFIRMED);
        $preparingOrders = $this->orderRepository->getByStatus(Order::STATUS_PREPARING);
        $readyOrders = $this->orderRepository->getByStatus(Order::STATUS_READY);
        
        $this->view('staff/dashboard', [
            'pendingOrders' => $pendingOrders,
            'preparingOrders' => $preparingOrders,
            'readyOrders' => $readyOrders
        ]);
    }

    /**
     * Show kitchen orders (for chef)
     */
    public function kitchenOrders(): void
    {
        $middleware = new StaffMiddleware();
        $middleware->handle();
        
        $orders = $this->orderRepository->getPendingOrders();
        
        $this->view('staff/kitchen_orders', [
            'orders' => $orders
        ]);
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(): void
    {
        $middleware = new StaffMiddleware();
        $middleware->handle();
        
        $orderId = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
        
        if (!$orderId || !$status) {
            flash('error', 'Invalid request');
            $this->redirect(url('staff/dashboard'));
            return;
        }
        
        $validStatuses = [
            Order::STATUS_CONFIRMED,
            Order::STATUS_PREPARING,
            Order::STATUS_READY,
            Order::STATUS_DELIVERED
        ];
        
        if (!in_array($status, $validStatuses)) {
            flash('error', 'Invalid status');
            $this->redirect(url('staff/dashboard'));
            return;
        }
        
        if ($this->orderRepository->updateStatus($orderId, $status)) {
            flash('success', 'Order status updated successfully');
        } else {
            flash('error', 'Failed to update order status');
        }
        
        $this->redirect(url('staff/kitchen-orders'));
    }

    /**
     * Show tables management
     */
    public function tables(): void
    {
        $middleware = new StaffMiddleware();
        $middleware->handle();
        
        $tables = Table::all();
        
        $this->view('staff/tables', [
            'tables' => $tables
        ]);
    }

    /**
     * Update table status
     */
    public function updateTableStatus(): void
    {
        $middleware = new StaffMiddleware();
        $middleware->handle();
        
        $tableId = filter_input(INPUT_POST, 'table_id', FILTER_VALIDATE_INT);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
        
        if (!$tableId || !$status) {
            flash('error', 'Invalid request');
            $this->redirect(url('staff/tables'));
            return;
        }
        
        $table = Table::find($tableId);
        if (!$table) {
            flash('error', 'Table not found');
            $this->redirect(url('staff/tables'));
            return;
        }
        
        $validStatuses = [Table::STATUS_AVAILABLE, Table::STATUS_OCCUPIED, Table::STATUS_RESERVED];
        if (!in_array($status, $validStatuses)) {
            flash('error', 'Invalid status');
            $this->redirect(url('staff/tables'));
            return;
        }
        
        $table->status = $status;
        if ($table->save()) {
            flash('success', 'Table status updated');
        } else {
            flash('error', 'Failed to update table status');
        }
        
        $this->redirect(url('staff/tables'));
    }

    /**
     * Create in-person order (for walk-in customers)
     */
    public function createInPersonOrder(): void
    {
        $middleware = new StaffMiddleware();
        $middleware->handle();
        
        $this->view('staff/create_order');
    }

    /**
     * Process in-person order
     */
    public function processInPersonOrder(): void
    {
        $middleware = new StaffMiddleware();
        $middleware->handle();
        
        // Similar to OrderController::placeOrder but for staff creating orders
        // Implementation similar to placeOrder but with staff user context
        flash('info', 'In-person order creation feature - to be implemented');
        $this->redirect(url('staff/dashboard'));
    }
}

