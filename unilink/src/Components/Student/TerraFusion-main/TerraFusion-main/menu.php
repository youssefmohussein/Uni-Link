<?php
// Landing/Menu Page - TerraFusion Restaurant Ordering System
session_start();

// Include database configuration
require_once 'config.php';

// Initialize menu categories array
$menuCategories = [];

try {
    // Get distinct meal types from the database
    $stmt = $pdo->query("SELECT DISTINCT meal_type FROM meals WHERE availability = 'Available' AND quantity > 0 ORDER BY meal_type");
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Fetch meals for each meal_type
    foreach ($categories as $category) {
        $stmt = $pdo->prepare("
            SELECT 
                meal_id,
                meal_name,
                description,
                price,
                image,
                meal_type,
                quantity
            FROM meals 
            WHERE meal_type = :meal_type AND availability = 'Available' AND quantity > 0
            ORDER BY created_at DESC
        ");
        
        $stmt->execute([':meal_type' => $category]);
        $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Add to menu categories if there are meals
        if (!empty($meals)) {
            $menuCategories[$category] = $meals;
            // Debug output
            echo "<!-- Debug: Found " . count($meals) . " items in meal type: " . htmlspecialchars($category) . " -->";
        } else {
            echo "<!-- Debug: No items found for meal type: " . htmlspecialchars($category) . " -->";
        }
    }
} catch(Exception $e) {
    // Log error and show user-friendly message
    error_log("Error in menu.php: " . $e->getMessage());
    $error = "We're experiencing technical difficulties. Please try again later.";
    // For debugging, show the actual error to the user
    if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        $error .= "<br><small>Debug: " . htmlspecialchars($e->getMessage()) . "</small>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Terra Fusion - Menu</title>
  
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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

  <!-- Main CSS File -->
  <link href="main.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 5px;
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

        .add-to-cart-btn {
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .quantity-container {
            display: none;
        }
        
        .add-to-cart-btn-container {
            display: flex;
            align-items: center;
            margin-bottom: -1rem;
        }

        /* Additional styles for menu page */
        .hero-section {
            text-align: center;
            padding: 4rem 2rem;
            background: linear-gradient(135deg, rgba(200, 162, 82, 0.1) 0%, rgba(26, 26, 26, 1) 100%);
            border-bottom: 2px solid var(--accent-gold);
            margin-bottom: 3rem;
        }
        
        .hero-title {
            font-size: 3.5rem;
            color: var(--accent-gold);
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .hero-subtitle {
            font-size: 1.25rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
        }
        
        .menu-category {
            margin-bottom: 4rem;
        }
        
        .category-title {
            font-size: 2rem;
            color: var(--accent-gold);
            margin-bottom: 2rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--accent-gold);
        }
        
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
            gap: 2rem;
            margin-bottom: 0px;
            align-items: flex-start;
        }
        
        .menu-item {
            background: linear-gradient(145deg, var(--card-bg) 0%, #1f1f1f 100%);
            border: 1px solid rgba(200, 162, 82, 0.2);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            display: flex;
            flex-direction: column;
            inset 0 1px 0 rgba(255, 255, 255, 0.05);
            position: relative;
        }
        
        .menu-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(200, 162, 82, 0.1), transparent);
            transition: left 0.6s;
            z-index: 1;
        }
        
        .menu-item:hover {
            border-color: var(--accent-gold);
            transform: translateY(-8px) scale(1.02) rotateX(2deg);
            box-shadow: 0 12px 40px rgba(200, 162, 82, 0.3),
            0 6px 20px rgba(0, 0, 0, 0.4),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }
        
        .menu-item:hover::before {
            left: 100%;
        }
        
        .menu-item-image {
            width: 100%;
            height: 700px;
            object-fit: cover;
            border-bottom: 1px solid rgba(200, 162, 82, 0.2);
            display: block;
            margin: 0 auto;
        }
        
        .menu-item-content {
            padding: 2rem;
            padding-bottom: 2rem;
            display: flex;
            flex-direction: column;
        }
        
        .menu-item-name {
            font-size: 1.25rem;
            color: var(--accent-gold);
            margin-bottom: 0.5rem;
        }
        
        .menu-item-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 1rem;
            line-height: 1.5;
        }
        
        .menu-item-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .menu-item-price {
            font-size: 1.5rem;
            color: var(--accent-gold);
            font-weight: 600;
        }
        
        .add-to-cart-btn {
            padding: 0.5rem 1.5rem;
            font-size: 0.9rem;
            position: relative;
            z-index: 2;
        }
        
        .cart-icon {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--accent-gold) 0%, var(--hover-gold) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(200, 162, 82, 0.4),
                        0 2px 8px rgba(200, 162, 82, 0.3),
                        inset 0 1px 0 rgba(255, 255, 255, 0.2);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 1000;
            text-decoration: none;
            color: var(--bg-primary);
            position: relative;
            overflow: hidden;
        }
        
        .cart-icon::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .cart-icon:hover {
            transform: scale(1.15) rotate(5deg);
            box-shadow: 0 8px 25px rgba(200, 162, 82, 0.6),
                        0 4px 12px rgba(200, 162, 82, 0.4),
                        inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }
        
        .cart-icon:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--error-red);
            color: var(--text-primary);
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .menu-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Fix header alignment issues caused by style.css conflict */
        .header .container {
            padding: 0 15px !important; /* Reset to Bootstrap-like defaults, overriding style.css 2rem */
        }
        .header .branding {
            min-height: 60px !important; /* Ensure min-height matches index.php */
        }
        
        /* Ensure header spans full Bootstrap width on large screens (1400px+) */
        @media (min-width: 1400px) {
            .header .container {
                max-width: 1320px !important; /* Override style.css 1200px limit */
            }
        }
        /* Category Navigation Bar */
        .category-nav-container {
            position: sticky;
            top: 80px; /* Adjust based on navbar height */
            z-index: 900;
            background-color: transparent; /* No section background */
            padding: 10px 0;
            margin-bottom: 2rem;
            /* Removed border and box-shadow to blend in */
        }

        .category-nav-wrapper {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .category-nav-btn {
            background-color: transparent;
            color: var(--accent-gold);
            border: 1px solid var(--accent-gold); /* Thinner border */
            padding: 8px 25px;
            border-radius: 50px; /* Pill shape */
            text-decoration: none;
            font-family: 'Playfair Display', serif; /* Changed font */
            font-weight: 500;
            transition: all 0.3s ease;
            text-transform: capitalize; /* Changed from uppercase to capitalize */
            font-size: 1.1rem; /* Slightly larger for the font */
            letter-spacing: 0.5px;
        }

        .category-nav-btn:hover, .category-nav-btn.active {
            background-color: var(--accent-gold);
            color: #000; /* Black text on hover */
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(200, 162, 82, 0.4);
        }

        /* Adjust scroll offset for anchor links so headers aren't hidden behind sticky elements */
        html {
            scroll-behavior: smooth;
        }
        
        :target {
            scroll-margin-top: 160px; /* Navbar (approx 80px) + Category Bar (approx 70px) + gap */
        }
    </style>
</head>
<body class="starter-page-services <?php echo (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) ? 'logged-in' : ''; ?>">
  <?php 
  require_once 'cart_functions.php';

  $cart_count = 0;
  if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['user_id'])) {
      $cart_id = getOrCreateCart($_SESSION['user_id']);
      $cart_count = getCartCount($cart_id);
  }
  ?>

  <header id="header" class="header fixed-top">

    <div class="topbar d-flex align-items-center">
      <div class="container d-flex justify-content-center justify-content-md-between">

      </div>
    </div><!-- End Top Bar -->

    <div class="branding d-flex align-items-cente">

      <div class="container position-relative d-flex align-items-center justify-content-between">
        <a href="index.php" class="logo d-flex align-items-center me-auto me-xl-0">
          <h1 class="sitename">Terra Fusion</h1>
        </a>

        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="index.php">Home<br></a></li>
            <li><a href="menu.php" class="active">Menu</a></li>
            <li><a href="index.php#about">About</a></li>
            <li><a href="index.php#specials">Specials</a></li>
            <li><a href="index.php#events">Events</a></li>
            <li><a href="index.php#contact">Contact</a></li>
          </ul>
          <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
          <div class="d-flex">
            <a class="btn-book-a-table d-none d-xl-block" href="userprofile.php" title="My Profile"><i class="bi bi-person-circle"></i></a>
            <a class="btn-book-a-table d-none d-xl-block position-relative" href="cart.php" title="Cart">
              <i class="bi bi-cart3"></i>
              <span id="cart-badge" class="position-absolute translate-middle badge d-flex align-items-center justify-content-center" style="<?php echo $cart_count > 0 ? '' : 'display: none;'; ?>"><?php echo $cart_count; ?></span>
            </a>
            <a class="btn-book-a-table d-none d-xl-block" href="logout.php" title="Logout"><i class="bi bi-box-arrow-right"></i></a>

          </div>
        <?php else: ?>
          <a class="btn-book-a-table d-none d-xl-block" href="userprofile.php" title="Profile"><i class="bi bi-person-circle"></i></a>
        <?php endif; ?>

      </div>

    </div>

  </header>

    <main class="main">
      
    <div class="container" style="margin-top: 100px;">

        <!-- Category Navigation Bar -->
        <?php if (!empty($menuCategories)): ?>
        <div class="category-nav-container">
            <div class="category-nav-wrapper">
                <?php foreach ($menuCategories as $categoryName => $items): 
                    $catId = strtolower(str_replace(' ', '_', $categoryName));
                ?>
                <a href="#<?php echo $catId; ?>" class="category-nav-btn"><?php echo htmlspecialchars($categoryName); ?></a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Menu Categories -->
        <?php if (!empty($menuCategories)): ?>
            <?php foreach ($menuCategories as $categoryName => $items): 
                $catId = strtolower(str_replace(' ', '_', $categoryName));
            ?>
                <div class="menu-category">
                    <h2 id="<?php echo $catId; ?>" class="category-title"><?php echo htmlspecialchars($categoryName); ?></h2>
                    <div class="menu-grid">
                        <?php foreach ($items as $item): 
                            // Prepare item data for JavaScript
                            $itemData = [
                                'id' => $item['meal_id'],  // Changed from 'id' to 'meal_id'
                                'name' => $item['meal_name'],
                                'price' => (float)$item['price'],
                                'image' => $item['image'],
                                'description' => $item['description']
                            ];
                            ?>
                            <div class="menu-item" data-item-id="<?php echo (int)$item['meal_id']; ?>">
                                <div style="width: 100%; overflow: hidden; height: 350px; display: flex; align-items: center; justify-content: center; background: #1a1a1a;">
                                 <img src="<?php echo !empty($item['image']) ? htmlspecialchars($item['image']) : 'images/meals-imgs/default.png'; ?>" 
     alt="<?php echo htmlspecialchars($item['meal_name']); ?>"
                                         class="menu-item-image"
                                         style="width: 100%; height: 100%; object-fit: cover;"
                                         onerror="this.onerror=null; this.src='images/meals-imgs/default.png';">
                                </div>
                                <div class="menu-item-content">
                                    <h3 class="menu-item-name"><?php echo htmlspecialchars($item['meal_name']); ?></h3>
                                    <p class="menu-item-description"><?php echo htmlspecialchars($item['description'] ?? 'No description available.'); ?></p>
                                    <div class="menu-item-footer">
                                        <span class="menu-item-price"><?php echo number_format((float)$item['price'], 2); ?> EGP</span>
                                        <div class="add-to-cart-btn-container">
                                            <button class="btn btn-primary add-to-cart-btn" 
                                                    data-meal-id="<?php echo $item['meal_id']; ?>">
                                                Add to Cart
                                            </button>
                                            <div class="quantity-container">
                                                <div class="quantity-controls">
                                                    <button class="quantity-btn minus">-</button>
                                                    <span class="quantity-display">1</span>
                                                    <button class="quantity-btn">+</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-items">
                <p>No menu items found. Please check back later.</p>
            </div>
        <?php endif; ?>
    </div>
    </main>

    <footer id="footer" class="footer">
    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">Terra Fusion</strong> <span>All Rights Reserved</span></p>
    </div>
  </footer>

  <!-- Signup/Login Modal -->
  <div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="signupModalLabel">Welcome to Terra Fusion</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <ul class="nav nav-tabs mb-3" id="authTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login-tab-pane" type="button" role="tab" aria-controls="login-tab-pane" aria-selected="true">Login</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register-tab-pane" type="button" role="tab" aria-controls="register-tab-pane" aria-selected="false">Register</button>
            </li>
          </ul>
          <div class="tab-content" id="authTabContent">
            <!-- Login Tab -->
            <div class="tab-pane fade show active" id="login-tab-pane" role="tabpanel" aria-labelledby="login-tab" tabindex="0">
              <form id="loginForm" method="POST" action="auth.php">
                <input type="hidden" name="action" value="login">
                <div class="mb-3">
                  <label for="loginEmail" class="form-label">Email address</label>
                  <input type="email" class="form-control" id="loginEmail" name="email" required>
                </div>
                <div class="mb-3">
                  <label for="loginPassword" class="form-label">Password</label>
                  <input type="password" class="form-control" id="loginPassword" name="password" required>
                </div>
                <div class="d-grid gap-2">
                  <button type="submit" class="btn btn-primary">Login</button>
                </div>
              </form>
            </div>
            <!-- Register Tab -->
            <div class="tab-pane fade" id="register-tab-pane" role="tabpanel" aria-labelledby="register-tab" tabindex="0">
              <form id="registerForm" method="POST" action="auth.php">
                <input type="hidden" name="action" value="register">
                <div class="mb-3">
                  <label for="regUsername" class="form-label">Username</label>
                  <input type="text" class="form-control" id="regUsername" name="username" required>
                </div>
                <div class="mb-3">
                  <label for="regEmail" class="form-label">Email address</label>
                  <input type="email" class="form-control" id="regEmail" name="email" required>
                </div>
                <div class="mb-3">
                  <label for="regPhone" class="form-label">Phone Number</label>
                  <input type="tel" class="form-control" id="regPhone" name="phone" required>
                </div>
                <div class="mb-3">
                  <label for="regPassword" class="form-label">Password</label>
                  <input type="password" class="form-control" id="regPassword" name="password" required minlength="6">
                </div>
                <div class="mb-3">
                  <label for="regConfirmPassword" class="form-label">Confirm Password</label>
                  <input type="password" class="form-control" id="regConfirmPassword" name="confirm_password" required>
                </div>
                <div class="d-grid gap-2">
                  <button type="submit" class="btn btn-primary">Create Account</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

    <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/isotope-layout@3.0.6/dist/isotope.pkgd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@8.4.7/swiper-bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/imagesloaded@5.0.0/imagesloaded.pkgd.min.js"></script>
  
  <!-- Main JS File -->
  <script src="main.js"></script>


    <!-- Load cart.js first, then our menu quantity controls, then Mahmoud -->
    <script src="assets/js/cart.js"></script>
    <script src="assets/js/menu-quantity.js"></script>
    
    <!-- Mahmoud AI Chatbot Context -->
    <script>
        window.terraMenu = <?php echo json_encode($menuCategories); ?>;
    </script>
    <script src="assets/js/chef-mahmoud.js"></script>
    
    <style>
        /* Toast notification styles */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #4CAF50;
            color: white;
            padding: 12px 24px;
            border-radius: 4px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }
        
        .toast.error {
            background: #f44336;
        }
    </style>
    
</body>
</html>
