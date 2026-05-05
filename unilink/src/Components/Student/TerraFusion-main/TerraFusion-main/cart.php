<?php
// Cart Page - TerraFusion Restaurant Ordering System
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'cart_functions.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$customer_id = $_SESSION['user_id'];
$order_id = getOrCreateCart($customer_id);

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    $meal_id = isset($_POST['meal_id']) ? (int)$_POST['meal_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    $response = ['success' => false];
    
    switch ($action) {
        case 'add':
            $response['success'] = addToCart($order_id, $meal_id, $quantity);
            break;
            
        case 'update':
            $response['success'] = updateCartItem($order_id, $meal_id, $quantity);
            break;
            
        case 'remove':
            $response['success'] = removeFromCart($order_id, $meal_id);
            break;
    }
    
    // Get updated cart count
    if ($response['success']) {
        $response['cart_count'] = getCartCount($order_id);
    }
    
    echo json_encode($response);
    exit;
}

// Get cart items for display
$cartItems = getCartItems($order_id);
$subtotal = getCartTotal($order_id);
$tax = $subtotal * 0.10;
$deliveryFee = ($subtotal > 0) ? 5.00 : 0.00; // No fee if empty
$total = $subtotal + $tax + $deliveryFee;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - TerraFusion</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .cart-item {
            display: flex;
            padding: 1.5rem;
            background: #1a1a1a;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.2);
            margin-bottom: 1.5rem;
            position: relative;
            transition: all 0.3s ease;
            border: 1px solid #333;
        }
        
        .cart-item-image {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            overflow: hidden;
            margin-right: 1.5rem;
            flex-shrink: 0;
            border: 1px solid #333;
        }
        
        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .cart-item-details {
            flex-grow: 1;
        }
        
        .cart-item-name {
            margin: 0 0 0.5rem 0;
            color: #cda45e;
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: capitalize;
        }
        
        .cart-item-description {
            margin: 0 0 1rem 0;
            color: #aaa;
            font-size: 0.9rem;
            line-height: 1.4;
        }
        
        .cart-item-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
        }
        
        .cart-item-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .item-quantity {
            background: #333;
            color: #fff;
            padding: 0.3rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
        }
        
        .remove-item-btn {
            background: #444;
            border: none;
            color: #999;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 1rem;
            padding: 0;
        }
        
        .remove-item-btn i {
            font-size: 16px;
            background-color: transparent;
        }
        
        .remove-item-btn:hover {
            color: #e74c3c;
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-right: 10px;
        }
        
        .quantity-btn {
            background: var(--accent-gold);
            color: var(--text-dark);
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.2s ease;
            padding: 0;
            line-height: 1;
        }
        
        .quantity-btn:hover {
            background: var(--accent-gold-dark);
            transform: scale(1.1);
        }
        
        .quantity-btn.minus {
            background: var(--card-bg);
            border: 1px solid var(--accent-gold);
            color: var(--accent-gold);
        }
        
        .quantity-btn.minus:hover {
            background: var(--accent-gold);
            color: var(--text-dark);
        }
        
        .quantity-display {
            min-width: 30px;
            text-align: center;
            font-weight: bold;
            color: var(--text-primary);
        }
        
        .cart-summary {
            background: #1a1a1a;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.2);
            margin-top: 2rem;
            border: 1px solid #333;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #333;
            color: #eee;
        }
        
        .summary-row:last-child {
            border-bottom: none;
            font-size: 1.2rem;
            font-weight: 600;
            color: #cda45e;
            padding-top: 0.5rem;
        }
        
        .empty-cart {
            text-align: center;
            padding: 4rem 2rem;
        }
        
        .empty-cart p {
            color: #666;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Terra Fusion</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/swiper@8.4.7/swiper-bundle.min.css" rel="stylesheet">
    
    <!-- Main CSS File -->
    <link href="main.css" rel="stylesheet">
    
    <!-- Vendor JS Files -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/isotope-layout@3.0.6/dist/isotope.pkgd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@8.4.7/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/imagesloaded@5.0.0/imagesloaded.pkgd.min.js"></script>
    
    <!-- Icon Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    </head>
    
    <main class="main-content" style="padding-top: 2rem;">
        <div class="container">
            <h1 class="page-title mb-4" style="margin-top: 0; padding-top: 0; color: #cda45e">Your Cart</h1>
            
            <?php if (empty($cartItems)): ?>
                <div class="empty-cart">
                    <h2>Your cart is empty</h2>
                    <p>Looks like you haven't added any items to your cart yet.</p>
                    <a href="menu.php" class="btn btn-primary">Browse Menu</a>
                </div>
            <?php else: ?>
                <div class="cart-container">
                    <div class="cart-items" id="cart-items">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="cart-item" data-item-id="<?php echo $item['meal_id']; ?>">
                                <div class="cart-item-image">
                                    <img src="<?php echo !empty($item['image']) ? 'images/meals-imgs/' . htmlspecialchars($item['image']) : 'images/meals-imgs/default.png'; ?>" 
                                         alt="<?php echo htmlspecialchars($item['meal_name']); ?>"
                                         onerror="this.onerror=null; this.src='images/meals-imgs/default.png';">
                                </div>
                                <div class="cart-item-details">
                                    <h3 class="cart-item-name"><?php echo htmlspecialchars($item['meal_name']); ?></h3>
                                    <p class="cart-item-description"><?php echo htmlspecialchars($item['description'] ?? ''); ?></p>
                                    <div class="cart-item-footer">
                                        <span class="cart-item-price"><?php echo number_format((float)$item['price'], 2); ?> EGP</span>
                                        <div class="cart-item-actions">
                                            <div class="quantity-controls">
                                                <button class="quantity-btn minus" data-meal-id="<?php echo $item['meal_id']; ?>">-</button>
                                                <span class="quantity-display"><?php echo $item['quantity']; ?></span>
                                                <button class="quantity-btn plus" data-meal-id="<?php echo $item['meal_id']; ?>">+</button>
                                            </div>
                                            <button class="remove-item-btn" data-meal-id="<?php echo $item['meal_id']; ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="cart-summary">
                        <h2 class="section-title">Order Summary</h2>
                        
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span id="subtotal"><?php echo number_format($subtotal, 2); ?> EGP</span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Tax (10%)</span>
                            <span id="tax"><?php echo number_format($tax, 2); ?> EGP</span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Delivery Fee</span>
                            <span id="delivery-fee"><?php echo number_format($deliveryFee, 2); ?> EGP</span>
                        </div>
                        
                        <div class="summary-row">
                            <strong>Total</strong>
                            <strong id="total"><?php echo number_format($total, 2); ?> EGP</strong>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="checkout.php" class="btn btn-primary btn-lg">Proceed to Checkout</a>
                            <a href="menu.php" class="btn btn-primary btn-lg">Continue Shopping</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
  <footer id="footer" class="footer" style="position: relative; overflow: visible;">
    <!-- Floating Shapes Background -->
    <div class="floating-shapes">
      <span class="shape" style="--i:11; --x: 05%; --d: 0s;">✦</span>
      <span class="shape" style="--i:12; --x: 15%; --d: 2s;">✧</span>
      <span class="shape" style="--i:15; --x: 25%; --d: 4s;">●</span>
      <span class="shape" style="--i:13; --x: 35%; --d: 1s;">◆</span>
      <span class="shape" style="--i:18; --x: 45%; --d: 5s;">✨</span>
      <span class="shape" style="--i:14; --x: 55%; --d: 3s;">✦</span>
      <span class="shape" style="--i:16; --x: 65%; --d: 6s;">✧</span>
      <span class="shape" style="--i:19; --x: 75%; --d: 2s;">●</span>
      <span class="shape" style="--i:20; --x: 85%; --d: 4s;">◆</span>
      
      <span class="shape" style="--i:21; --x: 10%; --d: 1.5s;">✨</span>
      <span class="shape" style="--i:22; --x: 20%; --d: 3.5s;">✦</span>
      <span class="shape" style="--i:23; --x: 30%; --d: 5.5s;">✧</span>
      <span class="shape" style="--i:24; --x: 40%; --d: 2.5s;">●</span>
      <span class="shape" style="--i:25; --x: 50%; --d: 4.5s;">◆</span>
      <span class="shape" style="--i:26; --x: 60%; --d: 0.5s;">✨</span>
      <span class="shape" style="--i:27; --x: 70%; --d: 6.5s;">✦</span>
      <span class="shape" style="--i:28; --x: 80%; --d: 1.2s;">✧</span>
      <span class="shape" style="--i:29; --x: 90%; --d: 3.2s;">●</span>
      <span class="shape" style="--i:30; --x: 02%; --d: 5.2s;">◆</span>
      <span class="shape" style="--i:31; --x: 12%; --d: 2.8s;">✨</span>
      <span class="shape" style="--i:32; --x: 22%; --d: 4.8s;">✦</span>
      <span class="shape" style="--i:33; --x: 32%; --d: 0.8s;">✧</span>
      <span class="shape" style="--i:34; --x: 42%; --d: 6.8s;">●</span>
      <span class="shape" style="--i:35; --x: 52%; --d: 1.8s;">◆</span>
      <span class="shape" style="--i:36; --x: 62%; --d: 3.8s;">✨</span>
      <span class="shape" style="--i:37; --x: 72%; --d: 5.8s;">✦</span>
    </div>

    <div class="container footer-top">
      <div class="row gy-4 justify-content-center text-center">
        <div class="col-lg-4 col-md-6 footer-about">
          <a href="index.php" class="logo d-flex align-items-center">
            <span class="sitename">Terra Fusion</span>
          </a>
          <div class="footer-contact pt-3">
            <p>Misr International University</p>
            <p>Egypt, Cairo, Obour City, MIU</p>
            <p class="mt-3"><strong>Phone:</strong> <span>+20 101 234 5678</span></p>
            <p><strong>Email:</strong> <span>contact@terrafusion.com</span></p>
          </div>
          <div class="social-links d-flex mt-4 justify-content-center">
            <a href=""><i class="bi bi-twitter-x"></i></a>
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href=""><i class="bi bi-instagram"></i></a>
            <a href=""><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Useful Links</h4>
          <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="index.php#about">About us</a></li>
            <li><a href="menu.php">Menu</a></li>
            <li><a href="index.php#contact">Contact</a></li>
            <li><a href="meet-us.php">Meet Us</a></li>
          </ul>
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">Terra Fusion</strong> <span>All Rights Reserved</span></p>
      <div class="credits">
      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>


  <!-- Main JS File -->
  <script src="main.js"></script>
    
    <script src="assets/js/cart.js"></script>
</body>
</html>