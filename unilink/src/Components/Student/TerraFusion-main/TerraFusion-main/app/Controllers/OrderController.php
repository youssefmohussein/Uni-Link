<?php

namespace App\Controllers;

use App\Repositories\OrderRepository;
use App\Repositories\MenuRepository;
use App\Repositories\UserRepository;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Promotion;
use App\Models\Table;
use App\Services\OrderTypeFactory;
use App\Services\PaymentStrategy\PaymentStrategyInterface;
use App\Services\PaymentStrategy\CashPayment;
use App\Services\PaymentStrategy\CardPayment;
use App\Services\PaymentStrategy\MobilePayment;
use App\Services\DeliveryServiceAdapter;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\CsrfMiddleware;
use App\Libs\ErrorHandler;

class OrderController extends BaseController
{
    private OrderRepository $orderRepository;
    private MenuRepository $menuRepository;
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
        $this->menuRepository = new MenuRepository();
        $this->userRepository = new UserRepository();
    }

    /**
     * Show cart
     */
    public function cart(): void
    {
        $middleware = new AuthMiddleware();
        $middleware->handle();
        
        $cart = $_SESSION['cart'] ?? [];
        $cartItems = [];
        $total = 0;
        
        foreach ($cart as $item) {
            $menuItem = $this->menuRepository->find($item['meal_id']);
            if ($menuItem) {
                $cartItems[] = [
                    'menu_item' => $menuItem,
                    'quantity' => $item['quantity'],
                    'special_instructions' => $item['special_instructions'] ?? '',
                    'subtotal' => $menuItem->price * $item['quantity']
                ];
                $total += $menuItem->price * $item['quantity'];
            }
        }
        
        $this->view('customer/cart', [
            'cartItems' => $cartItems,
            'total' => $total
        ]);
    }

    /**
     * Add item to cart
     */
    public function addToCart(): void
    {
        $middleware = new AuthMiddleware();
        $middleware->handle();
        
        $mealId = filter_input(INPUT_POST, 'meal_id', FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;
        $specialInstructions = filter_input(INPUT_POST, 'special_instructions', FILTER_SANITIZE_STRING) ?? '';
        
        if (!$mealId) {
            $this->json(['success' => false, 'message' => 'Invalid menu item'], 400);
            return;
        }
        
        $menuItem = $this->menuRepository->find($mealId);
        if (!$menuItem || !$menuItem->isAvailable()) {
            $this->json(['success' => false, 'message' => 'Menu item not available'], 400);
            return;
        }
        
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Check if item already in cart
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['meal_id'] == $mealId && 
                ($item['special_instructions'] ?? '') === $specialInstructions) {
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $_SESSION['cart'][] = [
                'meal_id' => $mealId,
                'quantity' => $quantity,
                'special_instructions' => $specialInstructions
            ];
        }
        
        $this->json(['success' => true, 'message' => 'Item added to cart']);
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart(): void
    {
        $index = filter_input(INPUT_POST, 'index', FILTER_VALIDATE_INT);
        
        if ($index !== false && isset($_SESSION['cart'][$index])) {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index
            flash('success', 'Item removed from cart');
        }
        
        $this->redirect(url('order/cart'));
    }

    /**
     * Show checkout
     */
    public function checkout(): void
    {
        $middleware = new AuthMiddleware();
        $middleware->handle();
        
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            flash('error', 'Your cart is empty');
            $this->redirect(url('order/cart'));
            return;
        }
        
        $cartItems = [];
        $subtotal = 0;
        
        foreach ($cart as $item) {
            $menuItem = $this->menuRepository->find($item['meal_id']);
            if ($menuItem) {
                $quantity = $item['quantity'];
                $itemSubtotal = $menuItem->price * $quantity;
                $cartItems[] = [
                    'menu_item' => $menuItem,
                    'quantity' => $quantity,
                    'special_instructions' => $item['special_instructions'] ?? '',
                    'subtotal' => $itemSubtotal
                ];
                $subtotal += $itemSubtotal;
            }
        }
        
        // Get available tables for dine-in
        $tables = Table::getAvailable();
        
        // Check promo code
        $promoCode = $_SESSION['applied_promo'] ?? null;
        $discount = 0;
        $promotion = null;
        
        if ($promoCode) {
            $promotion = Promotion::findByCode($promoCode);
            if ($promotion && $promotion->isValid($subtotal)) {
                $discount = $promotion->calculateDiscount($subtotal);
            }
        }
        
        $total = $subtotal - $discount;
        
        $this->view('customer/checkout', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'promotion' => $promotion,
            'tables' => $tables
        ]);
    }

    /**
     * Apply promo code
     */
    public function applyPromo(): void
    {
        $code = filter_input(INPUT_POST, 'promo_code', FILTER_SANITIZE_STRING);
        
        if (empty($code)) {
            flash('error', 'Promo code is required');
            $this->redirect(url('order/checkout'));
            return;
        }
        
        $promotion = Promotion::findByCode($code);
        
        if (!$promotion) {
            flash('error', 'Invalid promo code');
            $this->redirect(url('order/checkout'));
            return;
        }
        
        // Calculate current cart total
        $subtotal = 0;
        foreach ($_SESSION['cart'] ?? [] as $item) {
            $menuItem = $this->menuRepository->find($item['meal_id']);
            if ($menuItem) {
                $subtotal += $menuItem->price * $item['quantity'];
            }
        }
        
        if (!$promotion->isValid($subtotal)) {
            flash('error', 'Promo code is not valid or expired');
            $this->redirect(url('order/checkout'));
            return;
        }
        
        $_SESSION['applied_promo'] = $code;
        flash('success', 'Promo code applied successfully!');
        $this->redirect(url('order/checkout'));
    }

    /**
     * Place order
     */
    public function placeOrder(): void
    {
        $middleware = new AuthMiddleware();
        $middleware->handle();
        
        $csrfMiddleware = new CsrfMiddleware();
        $csrfMiddleware->handle();
        
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            flash('error', 'Your cart is empty');
            $this->redirect(url('order/cart'));
            return;
        }
        
        $orderType = filter_input(INPUT_POST, 'order_type', FILTER_SANITIZE_STRING);
        $paymentMethod = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_STRING);
        
        // Calculate totals
        $subtotal = 0;
        foreach ($cart as $item) {
            $menuItem = $this->menuRepository->find($item['meal_id']);
            if ($menuItem) {
                $subtotal += $menuItem->price * $item['quantity'];
            }
        }
        
        // Apply promo
        $discount = 0;
        $promoCode = $_SESSION['applied_promo'] ?? null;
        if ($promoCode) {
            $promotion = Promotion::findByCode($promoCode);
            if ($promotion && $promotion->isValid($subtotal)) {
                $discount = $promotion->calculateDiscount($subtotal);
                $promotion->incrementUsage();
            }
        }
        
        $total = $subtotal - $discount;
        
        try {
            $user = $_SESSION['user'] ?? null;
            $orderData = [
                'customer_name' => $user ? $user->full_name : 'Guest',
                'total_amount' => $total,
                'table_number' => filter_input(INPUT_POST, 'table_number', FILTER_VALIDATE_INT)
            ];
            
            if ($orderType === 'dine_in') {
                $orderData['table_id'] = filter_input(INPUT_POST, 'table_id', FILTER_VALIDATE_INT);
            } elseif ($orderType === 'delivery') {
                $orderData['delivery_address'] = filter_input(INPUT_POST, 'delivery_address', FILTER_SANITIZE_STRING);
            }
            
            $order = OrderTypeFactory::createOrder($orderType, $orderData);
            $order->save();
            
            // Add items to order
            foreach ($cart as $item) {
                $this->orderRepository->addItemToOrder(
                    $order->order_id,
                    $item['meal_id'],
                    $item['quantity']
                );
            }
            
            // Process payment using Strategy Pattern
            $paymentStrategy = $this->getPaymentStrategy($paymentMethod);
            $paymentData = $this->getPaymentData($paymentMethod, $_POST);
            $paymentResult = $paymentStrategy->processPayment($total, $paymentData);
            
            // Create payment record
            $payment = new Payment([
                'order_id' => $order->order_id,
                'method' => $paymentMethod,
                'amount' => $total,
                'transaction_id' => $paymentResult['transaction_id'],
                'status' => $paymentResult['success'] ? Payment::STATUS_COMPLETED : Payment::STATUS_FAILED
            ]);
            $payment->save();
            
            if ($paymentResult['success']) {
                // Update order status
                $order->updateStatus(Order::STATUS_CONFIRMED);
                
                // Update table status if dine-in
                if ($orderType === 'dine_in' && !empty($orderData['table_id'])) {
                    $table = Table::find($orderData['table_id']);
                    if ($table) {
                        $table->occupy();
                    }
                }
                
                // Handle delivery
                if ($orderType === 'delivery') {
                    $deliveryAdapter = new DeliveryServiceAdapter\InternalDeliveryAdapter();
                    $deliveryResult = $deliveryAdapter->createDelivery([
                        'order_id' => $order->order_id,
                        'address' => $orderData['delivery_address'] ?? 'Default Address'
                    ]);
                    // Store delivery_id in order or separate table if needed
                }
                
                // Clear cart and promo
                unset($_SESSION['cart']);
                unset($_SESSION['applied_promo']);
                
                flash('success', 'Order placed successfully! Order ID: #' . $order->order_id);
                $this->redirect(url('customer/orders'));
            } else {
                flash('error', 'Payment failed: ' . $paymentResult['message']);
                $this->redirect(url('order/checkout'));
            }
            
        } catch (\Exception $e) {
            ErrorHandler::logError('Order placement error: ' . $e->getMessage());
            flash('error', 'Failed to place order. Please try again.');
            $this->redirect(url('order/checkout'));
        }
    }

    /**
     * Get payment strategy based on method
     */
    private function getPaymentStrategy(string $method): PaymentStrategyInterface
    {
        switch ($method) {
            case 'cash':
                return new CashPayment();
            case 'card':
                return new CardPayment();
            case 'mobile':
                return new MobilePayment();
            default:
                throw new \InvalidArgumentException('Invalid payment method');
        }
    }

    /**
     * Get payment data based on method
     */
    private function getPaymentData(string $method, array $postData): array
    {
        switch ($method) {
            case 'card':
                return [
                    'card_number' => $postData['card_number'] ?? '',
                    'expiry' => $postData['expiry'] ?? '',
                    'cvv' => $postData['cvv'] ?? ''
                ];
            case 'mobile':
                return [
                    'provider' => $postData['mobile_provider'] ?? 'generic',
                    'payment_token' => $postData['payment_token'] ?? bin2hex(random_bytes(16))
                ];
            default:
                return [];
        }
    }
}

