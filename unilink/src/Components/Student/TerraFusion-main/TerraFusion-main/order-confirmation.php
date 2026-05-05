<?php
// Order Confirmation Page - TerraFusion Restaurant Ordering System
session_start();
require_once 'config.php';

// Redirect if no order ID
if (!isset($_GET['order_id'])) {
    header('Location: index.php');
    exit;
}

$order_id = $_GET['order_id'];
$customer_id = $_SESSION['user_id'] ?? 0;

try {
    // 1. Fetch Order Info (Verify it belongs to user if logged in)
    $stmt = $pdo->prepare("
        SELECT * FROM orders 
        WHERE order_id = ? AND (served_by_fk = ? OR served_by_fk IS NULL)
    ");
    $stmt->execute([$order_id, $customer_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die("Order not found or access denied.");
    }

    $orderNumber = $order['order_id'];
    $orderDate = date('F j, Y \a\t g:i A', strtotime($order['order_date']));
    $total = $order['total_amount'];
    
    // 2. Fetch Order Items
    $stmt = $pdo->prepare("
        SELECT 
            m.meal_name as name, 
            od.quantity, 
            od.price_at_sale as price 
        FROM order_details od
        JOIN meals m ON od.item_fk = m.meal_id
        WHERE od.order_fk = ?
    ");
    $stmt->execute([$order_id]);
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate subtotal for display logic (though total is stored)
    $subtotal = 0;
    foreach ($orderItems as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    $tax = $subtotal * 0.10;
    $deliveryFee = 5.00; // Fixed fee for now

    // 3. User & Delivery Info (Fallback to session/defaults if not in DB order record)
    // Note: The 'orders' table has customer_name, table_number. 
    // Address/Phone/Email might not be in 'orders' table based on previous schema checks.
    // We'll use what we have and fallback to session/static for missing fields.
    
    $customerName = $order['customer_name'];
    $customerEmail = $_SESSION['user_email'] ?? 'N/A';
    $customerPhone = $_SESSION['user_phone'] ?? 'N/A';
    
    $tableNumber = $order['table_number'];
    
    // Determine Order Type based on table_number
    if ($tableNumber) {
        $orderType = 'dine-in';
        $paymentMethod = 'Pay at Counter / Table';
        $deliveryAddress = "Table $tableNumber";
    } else {
        $orderType = 'delivery'; // Defaulting to delivery if no table
        $paymentMethod = 'Cash / Card';
        $deliveryAddress =  $_SESSION['address'] ?? 'Address on file'; 
    }
    
    $estimatedDelivery = date('F j, Y \a\t g:i A', strtotime('+45 minutes'));
    $orderTypeDisplay = ucfirst($orderType);

} catch (PDOException $e) {
    die("Error fetching order: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - TerraFusion</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="confirmation-container">
            <!-- Confirmation Header -->
            <div class="confirmation-header">
                <div class="confirmation-icon">✓</div>
                <h1 class="confirmation-message">Order Confirmed!</h1>
                <p class="order-number">Order #<?php echo htmlspecialchars($orderNumber); ?></p>
                <p style="color: var(--text-secondary); margin-top: 0.5rem;">
                    Thank you for your order. We've received it and will start preparing it right away.
                </p>
            </div>

            <!-- Order Status -->
            <div class="card" style="text-align: center;">
                <span class="status-badge status-confirmed">Confirmed</span>
                <p style="margin-top: 1rem; color: var(--text-secondary);">
                    Your order was placed on <?php echo $orderDate; ?>
                </p>
            </div>

            <!-- Order Details Grid -->
            <div class="order-details-grid">
                <!-- Order Items -->
                <div class="card">
                    <h2 class="section-title">Order Items</h2>
                    <ul class="order-items-list">
                        <?php foreach ($orderItems as $item): ?>
                            <li class="order-item">
                                <span>
                                    <span class="order-item-name"><?php echo htmlspecialchars($item['name']); ?></span>
                                    <span class="order-item-quantity">(x<?php echo $item['quantity']; ?>)</span>
                                </span>
                                <span class="order-item-price">
                                    <?php echo number_format($item['price'] * $item['quantity'], 2); ?> EGP
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="order-summary-box" style="margin-top: 2rem;">
                        <h3 class="summary-box-title">Order Summary</h3>
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span><?php echo number_format($subtotal, 2); ?> EGP</span>
                        </div>
                        <div class="summary-row">
                            <span>Tax (10%)</span>
                            <span><?php echo number_format($tax, 2); ?> EGP</span>
                        </div>
                        <div class="summary-row">
                            <span>Delivery Fee</span>
                            <span><?php echo number_format($deliveryFee, 2); ?> EGP</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span><?php echo number_format($total, 2); ?> EGP</span>
                        </div>
                    </div>
                </div>

                <!-- Delivery & Payment Info -->
                <div class="card">
                    <h2 class="section-title">Delivery Information</h2>
                    
                    <div class="delivery-info">
                        <div class="delivery-info-item">
                            <span class="delivery-info-label">Order Type:</span>
                            <span class="delivery-info-value"><?php echo htmlspecialchars($orderTypeDisplay); ?></span>
                        </div>
                        
                        <?php if ($orderType === 'delivery'): ?>
                            <div class="delivery-info-item">
                                <span class="delivery-info-label">Delivery Address:</span>
                                <span class="delivery-info-value" style="text-align: right;">
                                    <?php echo htmlspecialchars($deliveryAddress); ?>
                                </span>
                            </div>
                            <div class="delivery-info-item">
                                <span class="delivery-info-label">Estimated Delivery:</span>
                                <span class="delivery-info-value"><?php echo $estimatedDelivery; ?></span>
                            </div>
                        <?php elseif ($orderType === 'takeaway'): ?>
                            <div class="delivery-info-item">
                                <span class="delivery-info-label">Pickup Time:</span>
                                <span class="delivery-info-value"><?php echo $estimatedDelivery; ?></span>
                            </div>
                        <?php else: ?>
                            <div class="delivery-info-item">
                                <span class="delivery-info-label">Table Number:</span>
                                <span class="delivery-info-value">Table 5</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="delivery-info">
                        <h3 class="summary-box-title" style="margin-bottom: 1rem;">Customer Details</h3>
                        <div class="delivery-info-item">
                            <span class="delivery-info-label">Name:</span>
                            <span class="delivery-info-value"><?php echo htmlspecialchars($customerName); ?></span>
                        </div>
                        <div class="delivery-info-item">
                            <span class="delivery-info-label">Email:</span>
                            <span class="delivery-info-value"><?php echo htmlspecialchars($customerEmail); ?></span>
                        </div>
                        <div class="delivery-info-item">
                            <span class="delivery-info-label">Phone:</span>
                            <span class="delivery-info-value"><?php echo htmlspecialchars($customerPhone); ?></span>
                        </div>
                    </div>

                    <div class="delivery-info">
                        <h3 class="summary-box-title" style="margin-bottom: 1rem;">Payment Information</h3>
                        <div class="delivery-info-item">
                            <span class="delivery-info-label">Payment Method:</span>
                            <span class="delivery-info-value"><?php echo htmlspecialchars($paymentMethod); ?></span>
                        </div>
                        <div class="delivery-info-item">
                            <span class="delivery-info-label">Amount Paid:</span>
                            <span class="delivery-info-value"><?php echo number_format($total, 2); ?> EGP</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Tracking Info - Only show if delivery option was selected -->
            <?php if ($orderType === 'delivery'): ?>
            <div class="card" style="margin-top: 2rem;" id="tracking-section">
                <h2 class="section-title">Track Your Order</h2>
                <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
                    You can track your order status in real-time. We'll send you updates via email and SMS.
                </p>
                
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="order-tracking.php?order_id=<?php echo urlencode($orderNumber); ?>" 
                       class="btn btn-primary">
                        Track Order Status
                    </a>
                    <a href="userprofile.php#orders" class="btn btn-secondary">
                        View Order History
                    </a>
                    <a href="menu.php" class="btn btn-secondary">
                        Back to Menu
                    </a>
                </div>
            </div>
            <?php else: ?>
            <div class="card" style="margin-top: 2rem;">
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="userprofile.php#orders" class="btn btn-secondary">
                        View Order History
                    </a>
                    <a href="menu.php" class="btn btn-secondary">
                        Back to Menu
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Additional Information -->
            <div class="card" style="margin-top: 2rem; background-color: rgba(200, 162, 82, 0.1); border-color: var(--accent-gold);">
                <h3 style="color: var(--accent-gold); margin-bottom: 1rem;">What's Next?</h3>
                <ul style="list-style: none; padding: 0; color: var(--text-primary);">
                    <li style="margin-bottom: 0.75rem; padding-left: 1.5rem; position: relative;">
                        <span style="position: absolute; left: 0; color: var(--accent-gold);">✓</span>
                        We've received your order and payment
                    </li>
                    <li style="margin-bottom: 0.75rem; padding-left: 1.5rem; position: relative;">
                        <span style="position: absolute; left: 0; color: var(--accent-gold);">⏱</span>
                        Our kitchen is preparing your order
                    </li>
                    <li style="margin-bottom: 0.75rem; padding-left: 1.5rem; position: relative;">
                        <span style="position: absolute; left: 0; color: var(--accent-gold);">📦</span>
                        You'll receive a notification when your order is ready
                    </li>
                    <li style="padding-left: 1.5rem; position: relative;">
                        <span style="position: absolute; left: 0; color: var(--accent-gold);">⭐</span>
                        Don't forget to rate your experience after delivery!
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <script src="assets/js/confirmation.js"></script>
</body>
</html>

