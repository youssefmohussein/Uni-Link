<?php

namespace App\Controllers;

use App\Repositories\OrderRepository;
use App\Models\Order;
use App\Models\Review;
use App\Middlewares\AuthMiddleware;

class CustomerController extends BaseController
{
    private OrderRepository $orderRepository;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
    }

    /**
     * Show menu (redirect to MenuController)
     */
    public function menu(): void
    {
        $menuController = new MenuController();
        $menuController->index();
    }

    /**
     * Show order history
     */
    public function orders(): void
    {
        $middleware = new AuthMiddleware();
        $middleware->handle();
        
        $orders = $this->orderRepository->getByUser($_SESSION['user_id']);
        
        $this->view('customer/order_history', [
            'orders' => $orders
        ]);
    }

    /**
     * Show order details
     */
    public function orderDetails(int $id): void
    {
        $middleware = new AuthMiddleware();
        $middleware->handle();
        
        $order = $this->orderRepository->getOrderWithDetails($id);
        
        if (!$order || $order->user_id != $_SESSION['user_id']) {
            flash('error', 'Order not found');
            $this->redirect(url('customer/orders'));
            return;
        }
        
        $this->view('customer/order_details', [
            'order' => $order
        ]);
    }

    /**
     * Track order status
     */
    public function trackOrder(int $id): void
    {
        $middleware = new AuthMiddleware();
        $middleware->handle();
        
        $order = $this->orderRepository->getOrderWithDetails($id);
        
        if (!$order || $order->user_id != $_SESSION['user_id']) {
            $this->json(['success' => false, 'message' => 'Order not found'], 404);
            return;
        }
        
        $this->json([
            'success' => true,
            'order' => $order
        ]);
    }

    /**
     * Show review form
     */
    public function showReview(int $orderId): void
    {
        $middleware = new AuthMiddleware();
        $middleware->handle();
        
        $order = $this->orderRepository->getOrderWithDetails($orderId);
        
        if (!$order || $order->user_id != $_SESSION['user_id']) {
            flash('error', 'Order not found');
            $this->redirect(url('customer/orders'));
            return;
        }
        
        // Check if order is delivered
        if ($order->status !== Order::STATUS_DELIVERED) {
            flash('error', 'You can only review delivered orders');
            $this->redirect(url('customer/orders'));
            return;
        }
        
        $this->view('customer/review', [
            'order' => $order
        ]);
    }

    /**
     * Submit review
     */
    public function submitReview(): void
    {
        $middleware = new AuthMiddleware();
        $middleware->handle();
        
        $orderId = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
        $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 5]]);
        $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING) ?? '';
        
        if (!$orderId || !$rating) {
            flash('error', 'Rating is required');
            $this->redirect(url('customer/orders'));
            return;
        }
        
        $order = $this->orderRepository->find($orderId);
        if (!$order || $order->user_id != $_SESSION['user_id']) {
            flash('error', 'Invalid order');
            $this->redirect(url('customer/orders'));
            return;
        }
        
        $review = new Review([
            'user_id' => $_SESSION['user_id'],
            'order_id' => $orderId,
            'rating' => $rating,
            'comment' => $comment,
            'is_approved' => false // Admin needs to approve
        ]);
        
        if ($review->save()) {
            flash('success', 'Thank you for your review!');
        } else {
            flash('error', 'Failed to submit review');
        }
        
        $this->redirect(url('customer/orders'));
    }
}

