<?php
$pageTitle = 'Staff Dashboard - TerraFusion';
ob_start();
?>

<h1 class="playfair-font mb-4">Kitchen Dashboard</h1>

<div class="row">
    <div class="col-md-4">
        <div class="card bg-card border-gold">
            <div class="card-header bg-warning">
                <h5 class="mb-0">Pending Orders (<?= count($pendingOrders) ?>)</h5>
            </div>
            <div class="card-body">
                <?php foreach ($pendingOrders as $order): ?>
                    <div class="mb-3 p-2 border border-gold rounded">
                        <strong>Order #<?= $order->id ?></strong><br>
                        <small>Created: <?= format_date($order->created_at) ?></small><br>
                        <a href="<?= url('staff/kitchen-orders') ?>" class="btn btn-sm btn-gold mt-2">View Details</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-card border-gold">
            <div class="card-header bg-info">
                <h5 class="mb-0">Preparing (<?= count($preparingOrders) ?>)</h5>
            </div>
            <div class="card-body">
                <?php foreach ($preparingOrders as $order): ?>
                    <div class="mb-3 p-2 border border-gold rounded">
                        <strong>Order #<?= $order->id ?></strong><br>
                        <small>Started: <?= format_date($order->updated_at) ?></small><br>
                        <a href="<?= url('staff/kitchen-orders') ?>" class="btn btn-sm btn-gold mt-2">View Details</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-card border-gold">
            <div class="card-header bg-success">
                <h5 class="mb-0">Ready (<?= count($readyOrders) ?>)</h5>
            </div>
            <div class="card-body">
                <?php foreach ($readyOrders as $order): ?>
                    <div class="mb-3 p-2 border border-gold rounded">
                        <strong>Order #<?= $order->id ?></strong><br>
                        <small>Ready: <?= format_date($order->updated_at) ?></small><br>
                        <a href="<?= url('staff/kitchen-orders') ?>" class="btn btn-sm btn-gold mt-2">View Details</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="<?= url('staff/kitchen-orders') ?>" class="btn btn-gold btn-lg">View All Kitchen Orders</a>
    <a href="<?= url('staff/tables') ?>" class="btn btn-outline-gold btn-lg ms-2">Manage Tables</a>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>

