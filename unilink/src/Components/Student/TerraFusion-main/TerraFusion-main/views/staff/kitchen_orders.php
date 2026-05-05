<?php
$pageTitle = 'Kitchen Orders - TerraFusion';
ob_start();
?>

<h1 class="playfair-font mb-4">Kitchen Orders</h1>

<?php if (empty($orders)): ?>
    <div class="alert alert-info">No pending orders at this time.</div>
<?php else: ?>
    <?php foreach ($orders as $order): ?>
        <div class="card bg-card border-gold mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Order #<?= $order->id ?></h5>
                    <small class="text-muted"><?= format_date($order->created_at) ?></small>
                </div>
                <div>
                    <span class="badge bg-<?= 
                        $order->status === 'preparing' ? 'warning' : 'info' 
                    ?>">
                        <?= ucfirst($order->status) ?>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Customer:</strong> <?= htmlspecialchars($order->customer_name ?? 'N/A') ?><br>
                    <?php if ($order->table_number): ?>
                        <strong>Table:</strong> <?= htmlspecialchars($order->table_number) ?><br>
                    <?php endif; ?>
                    <strong>Total:</strong> <span class="text-gold"><?= format_currency($order->total_amount) ?></span>
                </div>
                
                <h6>Items:</h6>
                <ul>
                    <?php foreach ($order->items as $item): ?>
                        <li>
                            <?= htmlspecialchars($item->item_name) ?> x<?= $item->quantity ?>
                            <?php if (!empty($item->special_instructions)): ?>
                                <br><small class="text-muted">Note: <?= htmlspecialchars($item->special_instructions) ?></small>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                
                <div class="mt-3">
                    <form method="POST" action="<?= url('staff/update-order-status') ?>" class="d-inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="order_id" value="<?= $order->id ?>">
                        
                        <?php if ($order->status === 'confirmed'): ?>
                            <button type="submit" name="status" value="preparing" class="btn btn-warning">
                                Start Preparing
                            </button>
                        <?php elseif ($order->status === 'preparing'): ?>
                            <button type="submit" name="status" value="ready" class="btn btn-success">
                                Mark as Ready
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>

