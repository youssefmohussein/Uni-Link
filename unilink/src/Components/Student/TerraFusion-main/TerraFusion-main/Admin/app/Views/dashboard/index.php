<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<?php if (isset($_SESSION['error_message'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <?= htmlspecialchars($_SESSION['error_message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php 
    unset($_SESSION['error_message']); 
endif; 
?>

<div class="row">
    <div class="col-md-12 mb-4">
        <h4 class="text-muted">Welcome, <?= htmlspecialchars($_SESSION['username'] ?? '') ?>!</h4>
        <p class="text-muted">You are logged in as <span class="badge bg-secondary"><?= htmlspecialchars($_SESSION['role_name'] ?? '') ?></span></p>
    </div>
</div>

<div class="row">
    <!-- Total Sales Card -->
    <div class="col-md-3 mb-4">
        <div class="card card-custom h-100 p-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-coins me-2"></i> Total Sales</h5>
                <p class="card-value">EGP <?= number_format($data['totalSales'] ?? 0, 2) ?></p>
            </div>
        </div>
    </div>

    <!-- Pending Orders Card -->
    <div class="col-md-3 mb-4">
        <div class="card card-custom h-100 p-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-shopping-bag me-2"></i> Pending Orders</h5>
                <p class="card-value"><?= number_format($data['pendingOrders'] ?? 0) ?></p>
            </div>
        </div>
    </div>

    <!-- Today's Reservations Card -->
    <div class="col-md-3 mb-4">
        <div class="card card-custom h-100 p-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-calendar-check me-2"></i> Today's Reservations</h5>
                <p class="card-value"><?= number_format($data['todayReservations'] ?? 0) ?></p>
            </div>
        </div>
    </div>

    <!-- Total Menu Items Card -->
    <div class="col-md-3 mb-4">
        <div class="card card-custom h-100 p-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-utensils me-2"></i> Total Menu Items</h5>
                <p class="card-value"><?= number_format($data['totalMenuItems'] ?? 0) ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row mt-4">
    <!-- Weekly Sales Chart -->
    <div class="col-md-8 mb-4">
        <div class="card card-custom p-3 h-100">
            <div class="card-body">
                <h5 class="card-title mb-4">Weekly Sales Overview</h5>
                <canvas id="weeklySalesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Menu Type Chart -->
    <div class="col-md-4 mb-4">
        <div class="card card-custom p-3 h-100">
            <div class="card-body">
                <h5 class="card-title mb-4">Meal Types</h5>
                <canvas id="menuTypeChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Colors from theme
    const goldColor = '#c9b078';
    const darkBg = '#1e1e1e';
    const textColor = '#e0e0e0';

    // Prepare Sales Data
    const salesData = <?= json_encode($data['dailySales'] ?? []) ?>;
    const salesLabels = salesData.map(item => item.date);
    const salesValues = salesData.map(item => item.total);

    // Weekly Sales Chart
    const ctxSales = document.getElementById('weeklySalesChart').getContext('2d');
    new Chart(ctxSales, {
        type: 'line',
        data: {
            labels: salesLabels,
            datasets: [{
                label: 'Daily Sales (EGP)',
                data: salesValues,
                borderColor: goldColor,
                backgroundColor: 'rgba(201, 176, 120, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { labels: { color: textColor } }
            },
            scales: {
                x: { ticks: { color: textColor }, grid: { color: '#333' } },
                y: { ticks: { color: textColor }, grid: { color: '#333' } }
            }
        }
    });

    // Prepare Meal Type Data
    const mealTypeData = <?= json_encode($data['mealTypeCounts'] ?? []) ?>;
    const mealTypeLabels = mealTypeData.map(item => item.label);
    const mealTypeValues = mealTypeData.map(item => item.value);

    // Menu Type Chart
    const ctxType = document.getElementById('menuTypeChart').getContext('2d');
    new Chart(ctxType, {
        type: 'doughnut',
        data: {
            labels: mealTypeLabels,
            datasets: [{
                data: mealTypeValues,
                backgroundColor: [
                    goldColor,
                    '#bfa060',
                    '#8c734b', 
                    '#5e4d32',
                    '#403522',
                    '#2b2316'
                ],
                borderColor: darkBg,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { color: textColor } }
            }
        }
    });
});
</script>
