<?php
session_start();
require_once 'config.php'; // Using config.php as db.php was not found

// 1. Check Login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? ''; // used for linking orders if needed

// 2. Fetch Stats
try {
    // Current User Logic: We'll assume orders are linked by 'customer_name' matching full_name 
    // OR if there's a user_id column in orders. Based on previous context, likely customer_name or we check constraints.
    // The context mentioned: "Carts Table: User Link: customer_id (Use this instead of user_id)"
    // The context for Orders in previous turn: "Count Active Orders: Query orders table. ... if orders uses customer_name ... join via name"
    // I will use customer_name as the primary link since that's what OrderRepository used.
    
    // Active Orders (Not Completed, Not Cancelled)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE customer_name = ? AND status NOT IN ('Completed', 'Cancelled', 'Paid', 'Served')"); 
    // Adjusting 'Paid'/'Served' depending on what 'Active' means. User said "NOT 'completed' or 'cancelled'". 
    // I will stick strictly to: Status NOT IN ('completed', 'cancelled') logic if exact status names are known.
    // Let's assume 'completed' and 'cancelled' are the exact strings or case variants.
    $stmt->execute([$full_name]);
    $active_orders = $stmt->fetchColumn();

    // Total Orders
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE customer_name = ?");
    $stmt->execute([$full_name]);
    $total_orders = $stmt->fetchColumn();

    // Total Spent
    $stmt = $pdo->prepare("SELECT SUM(total_amount) FROM orders WHERE customer_name = ?");
    $stmt->execute([$full_name]);
    $total_spent = $stmt->fetchColumn() ?: 0;

    // 3. Chart Data (Last 6 Months)
    // We want [MonthName => Count] or just array of counts. User asked for JSON array like [2, 5, 0...]
    // We need to ensure we get 6 data points even if 0.
    
    $chart_data = [];
    for ($i = 5; $i >= 0; $i--) {
        $month_start = date('Y-m-01', strtotime("-$i months"));
        $month_end = date('Y-m-t', strtotime("-$i months"));
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE customer_name = ? AND order_date BETWEEN ? AND ?");
        $stmt->execute([$full_name, $month_start, $month_end]);
        $chart_data[] = (int)$stmt->fetchColumn();
    }
    $chart_counts_json = json_encode($chart_data);
    
    // Month labels for JS
    $chart_labels = [];
    for ($i = 5; $i >= 0; $i--) {
        $chart_labels[] = date('M', strtotime("-$i months"));
    }
    $chart_labels_json = json_encode($chart_labels);

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TerraFusion</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Custom Dashboard CSS override if needed, but we'll try to put most in style.css or inline for specific glass effects -->
    <style>
        /* Specific Dashboard Layout Overrides */
        body {
            background-color: var(--bg-primary, #121212);
        }
        
        .dashboard-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
            gap: 0;
        }

        /* Sidebar Styles */
        .sidebar-wrapper {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(212, 175, 55, 0.1);
            padding: 2rem;
            display: flex;
            flex-direction: column;
        }

        .user-info {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-gold), #8a7338);
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #000;
        }

        .nav-link.active {
            background: rgba(212, 175, 55, 0.15);
            color: var(--accent-gold);
            border-left: 3px solid var(--accent-gold);
        }

        /* Stats Cards with Glassmorphism */
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 16px;
            padding: 1.5rem;
            transition: transform 0.3s ease;
            height: 100%;
        }

        .glass-card h3 {
            color: var(--accent-gold);
            font-size: 2.5rem;
            margin-bottom: 0;
        }

        .glass-card p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .chart-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 16px;
            padding: 1.5rem;
            margin-top: 2rem;
            height: 400px;
            position: relative;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }
            .sidebar-wrapper {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                padding: 1rem;
                overflow-x: auto;
                border-bottom: 1px solid rgba(212, 175, 55, 0.1);
                border-right: none;
            }
            .user-info {
                display: none; 
            }
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar-wrapper">
        <div class="user-info">
            <div class="user-avatar">
                <i class="bi bi-person-fill"></i>
            </div>
            <h5 class="text-white mb-1"><?php echo htmlspecialchars($full_name); ?></h5>
            <small class="text-muted">Food Lover</small>
        </div>
        
        <nav class="nav flex-column gap-2">
            <a href="#" class="nav-link active text-white d-flex align-items-center rounded p-2">
                <i class="bi bi-grid-fill me-3"></i> Dashboard
            </a>
            <a href="menu.php" class="nav-link text-white d-flex align-items-center rounded p-2">
                <i class="bi bi-cup-hot me-3"></i> Order Food
            </a>
            <a href="userprofile.php" class="nav-link text-white d-flex align-items-center rounded p-2">
                <i class="bi bi-person-gear me-3"></i> Profile
            </a>
            <a href="logout.php" class="nav-link text-danger d-flex align-items-center rounded p-2 mt-auto">
                <i class="bi bi-box-arrow-right me-3"></i> Logout
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="p-4 overflow-auto">
        <header class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="h3 text-white mb-0">Overview</h1>
                <p class="text-muted">Welcome back, <?php echo htmlspecialchars($full_name); ?></p>
            </div>
            <a href="menu.php" class="btn btn-primary">New Order</a>
        </header>

        <!-- Stats Grid -->
        <div class="row g-4">
            <!-- Active Orders -->
            <div class="col-md-4">
                <div class="glass-card" data-tilt data-tilt-max="5" data-tilt-speed="400" data-tilt-glare data-tilt-max-glare="0.2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p>Active Orders</p>
                            <h3><?php echo number_format($active_orders); ?></h3>
                        </div>
                        <div class="icon-box text-warning bg-opacity-10 bg-warning rounded-circle p-3">
                            <i class="bi bi-clock-history fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="col-md-4">
                <div class="glass-card" data-tilt data-tilt-max="5" data-tilt-speed="400" data-tilt-glare data-tilt-max-glare="0.2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p>Total Orders</p>
                            <h3><?php echo number_format($total_orders); ?></h3>
                        </div>
                        <div class="icon-box text-primary bg-opacity-10 bg-primary rounded-circle p-3">
                            <i class="bi bi-bag-check fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Spent -->
            <div class="col-md-4">
                <div class="glass-card" data-tilt data-tilt-max="5" data-tilt-speed="400" data-tilt-glare data-tilt-max-glare="0.2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p>Total Spent</p>
                            <h3>$<?php echo number_format($total_spent, 2); ?></h3>
                        </div>
                        <div class="icon-box text-success bg-opacity-10 bg-success rounded-circle p-3">
                            <i class="bi bi-currency-dollar fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="chart-container">
            <canvas id="orderChart"></canvas>
        </div>
    </main>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Vanilla-Tilt -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.0/vanilla-tilt.min.js"></script>

<script>
    // Initialize Vanilla Tilt
    VanillaTilt.init(document.querySelectorAll("[data-tilt]"), {
        max: 5,
        speed: 400,
        glare: true,
        "max-glare": 0.2,
    });

    // Initialize Chart
    const ctx = document.getElementById('orderChart').getContext('2d');
    
    // Gradient Fill
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(212, 175, 55, 0.5)'); // Gold with opacity
    gradient.addColorStop(1, 'rgba(212, 175, 55, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo $chart_labels_json; ?>,
            datasets: [{
                label: 'Orders',
                data: <?php echo $chart_counts_json; ?>,
                borderColor: '#D4AF37', // Gold
                backgroundColor: gradient,
                borderWidth: 3,
                tension: 0.4, // Smooth Key
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#D4AF37',
                pointHoverBackgroundColor: '#D4AF37',
                pointHoverBorderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#D4AF37',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)'
                    },
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.6)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.6)'
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'nearest',
            },
        }
    });

    // Animate Chart on Load (Simple Fade In)
    document.querySelector('.chart-container').style.opacity = 0;
    setTimeout(() => {
        document.querySelector('.chart-container').style.transition = 'opacity 1s ease';
        document.querySelector('.chart-container').style.opacity = 1;
    }, 100);
</script>

</body>
</html>
