<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'cart_functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_id = getOrCreateCart($user_id);
$cartItems = getCartItems($cart_id);
$total_amount = getCartTotal($cart_id) * 1.1 + 5.0; // Subtotal + 10% tax + 5 EGP fee

if (empty($cartItems)) {
    header('Location: menu.php');
    exit;
}

try {
    $pdo->beginTransaction();

    // 0. served_by_fk logic
    $served_by_fk = $_SESSION['user_id']; // Since it's self-ordering or logged-in user

    // 1. Validate Stock & Calculate Total (Live Price)
    $stmtStock = $pdo->prepare("SELECT quantity, availability, price, meal_name FROM meals WHERE meal_id = ? FOR UPDATE");
    $stmtUpdateStock = $pdo->prepare("UPDATE meals SET quantity = quantity - ? WHERE meal_id = ?");
    
    $calculated_total = 0;
    $finalItems = [];

    foreach ($cartItems as $item) {
        $stmtStock->execute([$item['meal_id']]);
        $meal = $stmtStock->fetch(PDO::FETCH_ASSOC);

        if (!$meal) {
            throw new Exception("Item '{$item['meal_name']}' not found.");
        }
        
        // Availability Check
        if ($meal['availability'] === 'Out of Stock') {
            throw new Exception("Item '{$meal['meal_name']}' is Out of Stock.");
        }

        // Quantity Check
        if ($meal['quantity'] < $item['quantity']) {
            throw new Exception("Not enough stock for '{$meal['meal_name']}'. Only {$meal['quantity']} left.");
        }

        $price_at_sale = $meal['price'];
        $calculated_total += $price_at_sale * $item['quantity'];
        
        $finalItems[] = [
            'id' => $item['meal_id'],
            'qty' => $item['quantity'],
            'price' => $price_at_sale
        ];
    }
    
    // Add Tax/Fee logic matching existing logic
    $total_amount_final = $calculated_total * 1.1 + 5.0;

    // 2. Insert into orders table
    $stmt = $pdo->prepare("
        INSERT INTO orders (customer_name, table_number, total_amount, status, order_date, served_by_fk) 
        VALUES (?, ?, ?, 'Pending', NOW(), ?)
    ");
    
    $firstName = $_POST['first_name'] ?? '';
    $lastName = $_POST['last_name'] ?? '';
    $customerName = trim($firstName . ' ' . $lastName) ?: ($_SESSION['full_name'] ?? $_SESSION['user_name'] ?? 'Guest');
    $tableNumber = isset($_POST['table_number']) ? (int)$_POST['table_number'] : NULL;

    $stmt->execute([$customerName, $tableNumber, $total_amount_final, $served_by_fk]);
    $order_id = $pdo->lastInsertId();

    // 3. Insert items & Update Stock
    $stmtItem = $pdo->prepare("
        INSERT INTO order_details (order_fk, item_fk, quantity, price_at_sale) 
        VALUES (?, ?, ?, ?)
    ");

    foreach ($finalItems as $fItem) {
        // Insert Detail
        $stmtItem->execute([
            $order_id,
            $fItem['id'],
            $fItem['qty'],
            $fItem['price']
        ]);
        
        // Reduce Stock
        $stmtUpdateStock->execute([$fItem['qty'], $fItem['id']]);
        
        // Auto-update status if 0 (Optional, but good practice)
        // $pdo->exec("UPDATE meals SET availability='Out of Stock' WHERE quantity=0 AND meal_id=" . $fItem['id']);
    }

    // 4. Clear the cart
    clearCart($cart_id);

    $pdo->commit();

    // Store for confirmation
    $_SESSION['last_order'] = [
        'order_id' => $order_id,
        'customer_name' => $customerName,
        'total' => $total_amount_final,
        'timestamp' => time()
    ];

    header('Location: order-confirmation.php?order_id=' . $order_id);
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Order processing failed: " . $e->getMessage());
    $errorMessage = htmlspecialchars($e->getMessage());
    echo <<<HTML
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Order Error - TerraFusion</title>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="assets/css/style.css">
        <style>
            body {
                background: var(--bg-primary, #000);
                color: var(--text-color, #fff);
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
                margin: 0;
            }
            .error-container {
                background: #111;
                border: 1px solid rgba(212, 175, 55, 0.2);
                border-radius: 12px;
                padding: 3rem;
                text-align: center;
                box-shadow: 0 10px 30px rgba(0,0,0,0.5);
                max-width: 500px;
                width: 90%;
            }
            .error-icon {
                font-size: 4rem;
                color: #e74c3c;
                margin-bottom: 1rem;
            }
            .error-title {
                font-family: 'Playfair Display', serif;
                color: var(--accent-gold, #cda45e);
                margin-bottom: 1.5rem;
            }
            .btn-gold {
                background: var(--accent-gold, #cda45e);
                color: #000;
                border: 2px solid var(--accent-gold, #cda45e);
                padding: 10px 25px;
                border-radius: 5px;
                transition: 0.3s;
                text-decoration: none;
                font-family: 'Poppins', sans-serif;
                font-weight: 500;
                display: inline-block;
                margin-top: 1.5rem;
            }
            .btn-gold:hover {
                background: transparent;
                color: var(--accent-gold, #cda45e);
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="error-icon">⚠</div>
            <h2 class="error-title">Order Processing Failed</h2>
            <p>{$errorMessage}</p>
            <a href="cart.php" class="btn-gold">Return to Cart</a>
        </div>
    </body>
    </html>
HTML;
    exit;
}

