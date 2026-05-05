<?php
$pageTitle = 'Admin Dashboard - TerraFusion';
ob_start();
?>

<style>
    body {
        background-color: #121212 !important;
        color: #FFD700 !important;
    }
    .card {
        background: rgba(255, 255, 255, 0.05) !important;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 215, 0, 0.2) !important;
        color: #FFD700 !important;
    }
    .text-gold {
        color: #FFD700 !important;
    }
    .text-muted {
        color: rgba(255, 255, 255, 0.6) !important;
    }
    .table {
        color: #FFD700 !important;
    }
    .table thead th {
        border-bottom: 1px solid rgba(255, 215, 0, 0.2) !important;
        color: #FFD700 !important;
    }
    .table td {
        border-bottom: 1px solid rgba(255, 215, 0, 0.1) !important;
    }
    .bg-card {
        background-color: rgba(30, 30, 30, 0.6) !important;
    }
    .border-gold {
        border-color: #FFD700 !important;
    }
</style>

<h1 class="playfair-font mb-4 text-gold">Admin Dashboard</h1>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-card border-gold">
            <div class="card-body text-center">
                <h3 class="text-gold"><?= $salesSummary->total_orders ?? 0 ?></h3>
                <p class="text-muted mb-0">Today's Orders</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-card border-gold">
            <div class="card-body text-center">
                <h3 class="text-gold"><?= format_currency($salesSummary->total_revenue ?? 0) ?></h3>
                <p class="text-muted mb-0">Today's Revenue</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-card border-gold">
            <div class="card-body text-center">
                <h3 class="text-gold"><?= $activeOrdersCount ?? 0 ?></h3>
                <p class="text-muted mb-0">Active Orders</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-card border-gold">
            <div class="card-body text-center">
                <h3 class="text-danger"><?= count($lowStockItems ?? []) ?></h3>
                <p class="text-muted mb-0">Low Stock Items</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card bg-card border-gold">
            <div class="card-header bg-gold text-dark">
                <h5 class="mb-0">Recent Orders</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td>#<?= $order->order_id ?></td>
                                    <td><?= htmlspecialchars($order->customer_name ?? 'N/A') ?></td>
                                    <td><?= format_currency($order->total_amount) ?></td>
                                    <td><span class="badge bg-warning"><?= ucfirst($order->status) ?></span></td>
                                    <td><?= format_date($order->order_date) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <?php if (!empty($lowStockItems)): ?>
            <div class="card bg-card border-gold">
                <div class="card-header bg-danger text-light">
                    <h5 class="mb-0">Low Stock Alert</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($lowStockItems as $item): ?>
                        <div class="mb-2">
                            <strong><?= htmlspecialchars($item->item_name) ?></strong><br>
                            <small class="text-muted">Stock: <?= $item->current_stock ?> / Threshold: <?= $item->low_stock_threshold ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>

