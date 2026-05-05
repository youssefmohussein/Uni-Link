<?php
// Order Tracking Page - TerraFusion Restaurant Ordering System
session_start();
require_once 'config.php';

$orderId = isset($_GET['order_id']) ? $_GET['order_id'] : '';

if (!$orderId) {
    header('Location: index.php'); // Redirect if no ID provided
    exit;
}

try {
    // 1. Fetch Order Details
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die("Order not found.");
    }

    // 2. Fetch Order Items
    $stmtItems = $pdo->prepare("
        SELECT 
            m.meal_name as name, 
            od.quantity, 
            od.price_at_sale as price 
        FROM order_details od
        JOIN meals m ON od.item_fk = m.meal_id
        WHERE od.order_fk = ?
    ");
    $stmtItems->execute([$orderId]);
    $orderItems = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

    // 3. Map Database Status to Timeline Status vars
    // DB Statuses: Pending, processing, ready, out_for_delivery, delivered (assumed common naming)
    // Timeline Keys: confirmed, in_preparation, ready, out_for_delivery, delivered
    
    $dbStatus = strtolower($order['status'] ?? 'pending');
    $mappedStatus = 'confirmed'; // Default

    switch ($dbStatus) {
        case 'pending':
        case 'new':
            $mappedStatus = 'confirmed';
            break;
        case 'processing':
        case 'in_preparation':
            $mappedStatus = 'in_preparation';
            break;
        case 'ready':
            $mappedStatus = 'ready';
            break;
        case 'out_for_delivery':
            $mappedStatus = 'out_for_delivery';
            break;
        case 'delivered':
        case 'completed':
            $mappedStatus = 'delivered';
            break;
        default:
            $mappedStatus = 'confirmed';
    }

    $deliveryAddress = $order['table_number'] 
        ? "Table " . $order['table_number'] 
        : ($_SESSION['address'] ?? 'Address on file'); // Fallback if address not in orders table yet

    // Construct Order Data Array
    $orderData = [
        'order_id' => $order['order_id'],
        'order_date' => date('F j, Y \a\t g:i A', strtotime($order['order_date'])),
        'status' => $mappedStatus, 
        'estimated_delivery' => date('F j, Y \a\t g:i A', strtotime($order['order_date'] . ' +45 minutes')),
        'delivery_address' => $deliveryAddress,
        'customer_name' => $order['customer_name'],
        'customer_phone' => $_SESSION['phone'] ?? 'N/A', // Fallback
        'order_items' => $orderItems,
        'total' => $order['total_amount']
    ];

} catch (PDOException $e) {
    die("Error fetching tracking details: " . $e->getMessage());
}

// Status timeline
$statusTimeline = [
    'confirmed' => [
        'title' => 'Order Confirmed',
        'description' => 'Your order has been received and confirmed',
        'icon' => '✓',
        'completed' => true,
        'time' => date('g:i A', strtotime($order['order_date']))
    ],
    'in_preparation' => [
        'title' => 'In Preparation',
        'description' => 'Our kitchen is preparing your order',
        'icon' => '👨‍🍳',
        'completed' => false,
        'active' => false,
        'time' => null
    ],
    'ready' => [
        'title' => 'Ready',
        'description' => 'Your order is ready for pickup/delivery',
        'icon' => '📦',
        'completed' => false,
        'time' => null
    ],
    'out_for_delivery' => [
        'title' => 'Out for Delivery',
        'description' => 'Your order is on the way',
        'icon' => '🚗',
        'completed' => false,
        'time' => null
    ],
    'delivered' => [
        'title' => 'Delivered',
        'description' => 'Your order has been delivered',
        'icon' => '🎉',
        'completed' => false,
        'time' => null
    ]
];

// Get current status index
$statusOrder = ['confirmed', 'in_preparation', 'ready', 'out_for_delivery', 'delivered'];
$currentStatusIndex = array_search($orderData['status'], $statusOrder);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Order - TerraFusion</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/chef-mahmoud.css">
    <style>
        .tracking-header {
            text-align: center;
            padding: 2rem 0;
            margin-bottom: 3rem;
        }
        
        .order-id-display {
            font-size: 1.5rem;
            color: var(--accent-gold);
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .status-timeline {
            position: relative;
            padding: 2rem 0;
            margin: 2rem 0;
        }
        
        .timeline-line {
            position: absolute;
            left: 30px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--border-color);
            z-index: 0;
        }
        
        .timeline-line-progress {
            position: absolute;
            left: 30px;
            top: 0;
            width: 3px;
            background: linear-gradient(180deg, var(--accent-gold) 0%, var(--success-green) 100%);
            z-index: 1;
            transition: height 0.6s ease;
        }
        
        .timeline-item {
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: 2rem;
            margin-bottom: 3rem;
            padding-left: 1rem;
            z-index: 2;
        }
        
        .timeline-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
            background: var(--card-bg);
            border: 3px solid var(--border-color);
            color: var(--text-secondary);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .timeline-item.completed .timeline-icon {
            background: linear-gradient(135deg, var(--success-green) 0%, #66bb6a 100%);
            border-color: var(--success-green);
            color: var(--text-primary);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
            transform: scale(1.1);
        }
        
        .timeline-item.active .timeline-icon {
            background: linear-gradient(135deg, var(--accent-gold) 0%, var(--hover-gold) 100%);
            border-color: var(--accent-gold);
            color: var(--bg-primary);
            box-shadow: 0 8px 25px rgba(200, 162, 82, 0.5),
                        0 0 20px rgba(200, 162, 82, 0.3);
            animation: pulse 2s infinite;
            transform: scale(1.15);
        }
        
        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 8px 25px rgba(200, 162, 82, 0.5),
                            0 0 20px rgba(200, 162, 82, 0.3);
            }
            50% {
                box-shadow: 0 8px 25px rgba(200, 162, 82, 0.7),
                            0 0 30px rgba(200, 162, 82, 0.5);
            }
        }
        
        .timeline-content {
            flex: 1;
            padding-top: 0.5rem;
        }
        
        .timeline-title {
            font-size: 1.25rem;
            color: var(--accent-gold);
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .timeline-description {
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }
        
        .timeline-time {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }
        
        .tracking-map {
            width: 100%;
            height: 300px;
            background: var(--card-bg);
            border-radius: 16px;
            margin: 2rem 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
        }
        
        .tracking-map::before {
            content: '📍';
            font-size: 3rem;
            opacity: 0.3;
        }
        
        .order-summary-card {
            background: linear-gradient(145deg, var(--card-bg) 0%, #1f1f1f 100%);
            border-radius: 16px;
            padding: 2rem;
            margin-top: 2rem;
            border: 1px solid rgba(200, 162, 82, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        }
        
        .refresh-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-gold) 0%, var(--hover-gold) 100%);
            border: none;
            color: var(--bg-primary);
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(200, 162, 82, 0.4);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .refresh-btn:hover {
            transform: rotate(180deg) scale(1.1);
            box-shadow: 0 8px 25px rgba(200, 162, 82, 0.6);
        }
        
        @media (max-width: 768px) {
            .timeline-item {
                gap: 1rem;
            }
            
            .timeline-icon {
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="tracking-header">
            <h1>Track Your Order</h1>
            <div class="order-id-display">Order #<?php echo htmlspecialchars($orderData['order_id']); ?></div>
            <p style="color: var(--text-secondary);">Placed on <?php echo $orderData['order_date']; ?></p>
        </div>

        <!-- Status Timeline -->
        <div class="card">
            <h2 class="section-title">Order Status</h2>
            
            <div class="status-timeline">
                <div class="timeline-line"></div>
                <div class="timeline-line-progress" id="timeline-progress" style="height: <?php echo ($currentStatusIndex / (count($statusOrder) - 1)) * 100; ?>%;"></div>
                
                <?php foreach ($statusOrder as $index => $statusKey): 
                    $status = $statusTimeline[$statusKey];
                    $isCompleted = $index <= $currentStatusIndex;
                    $isActive = $index === $currentStatusIndex;
                ?>
                    <div class="timeline-item <?php echo $isCompleted ? 'completed' : ''; ?> <?php echo $isActive ? 'active' : ''; ?>">
                        <div class="timeline-icon">
                            <?php echo $status['icon']; ?>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title"><?php echo htmlspecialchars($status['title']); ?></div>
                            <div class="timeline-description"><?php echo htmlspecialchars($status['description']); ?></div>
                            <?php if ($status['time']): ?>
                                <div class="timeline-time"><?php echo $status['time']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Delivery Information -->
        <div class="card">
            <h2 class="section-title">Delivery Information</h2>
            
            <div class="delivery-info">
                <div class="delivery-info-item">
                    <span class="delivery-info-label">Estimated Delivery:</span>
                    <span class="delivery-info-value"><?php echo $orderData['estimated_delivery']; ?></span>
                </div>
                <div class="delivery-info-item">
                    <span class="delivery-info-label">Delivery Address:</span>
                    <span class="delivery-info-value" style="text-align: right;">
                        <?php echo htmlspecialchars($orderData['delivery_address']); ?>
                    </span>
                </div>
                <div class="delivery-info-item">
                    <span class="delivery-info-label">Contact:</span>
                    <span class="delivery-info-value"><?php echo htmlspecialchars($orderData['customer_phone']); ?></span>
                </div>
            </div>
            
            <div class="tracking-map">
                <div style="text-align: center; color: var(--text-secondary);">
                    <p>Live tracking map will appear here</p>
                    <p style="font-size: 0.875rem; margin-top: 0.5rem;">Real-time location updates</p>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="order-summary-card">
            <h3 class="summary-box-title">Order Summary</h3>
            <ul class="order-items-list">
                <?php foreach ($orderData['order_items'] as $item): ?>
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
            
            <div class="summary-row total" style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px solid var(--accent-gold);">
                <span>Total</span>
                <span><?php echo number_format($orderData['total'], 2); ?> EGP</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 2rem; justify-content: center;">
            <a href="order-confirmation.php?order_id=<?php echo urlencode($orderData['order_id']); ?>" 
               class="btn btn-secondary">
                View Order Details
            </a>
            <a href="userprofile.php#orders" class="btn btn-secondary">
                Order History
            </a>
            <a href="menu.php" class="btn btn-primary">
                Back to Menu
            </a>
        </div>
    </div>

    <!-- Refresh Button -->
    <button class="refresh-btn" onclick="refreshOrderStatus()" aria-label="Refresh order status" title="Refresh">
        🔄
    </button>

    <script src="assets/js/tracking.js"></script>
    
    <!-- Mahmoud AI Chatbot -->
    <script>
        window.terraMenu = []; 
    </script>
    <script src="assets/js/chef-mahmoud.js"></script>
</body>
</html>

