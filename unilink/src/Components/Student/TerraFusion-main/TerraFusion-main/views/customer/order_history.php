<?php
$pageTitle = 'Order History - TerraFusion';
ob_start();
?>

<h1 class="playfair-font mb-4">My Orders</h1>

<?php if (empty($orders)): ?>
    <div class="alert alert-info">You haven't placed any orders yet.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?= $order->id ?></td>
                        <td><?= format_date($order->created_at) ?></td>
                        <td><?= $order->item_count ?? 0 ?> item(s)</td>
                        <td class="text-gold fw-bold"><?= format_currency($order->total_amount) ?></td>
                        <td>
                            <span class="badge bg-<?= 
                                $order->status === 'delivered' ? 'success' : 
                                ($order->status === 'cancelled' ? 'danger' : 'warning') 
                            ?>">
                                <?= ucfirst($order->status) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= url('customer/orders/' . $order->id) ?>" class="btn btn-sm btn-gold">View</a>
                            <?php if ($order->status === 'delivered'): ?>
                                <a href="<?= url('customer/review/' . $order->id) ?>" class="btn btn-sm btn-outline-gold">Review</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>

