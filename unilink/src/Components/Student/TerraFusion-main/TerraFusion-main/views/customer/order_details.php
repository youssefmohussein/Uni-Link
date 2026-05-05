<?php
$pageTitle = 'Order Details - TerraFusion';
ob_start();
?>

<h1 class="playfair-font mb-4">Order #<?= $order->id ?></h1>

<div class="row">
    <div class="col-md-8">
        <div class="card bg-card border-gold mb-3">
            <div class="card-header bg-gold text-dark">
                <h5 class="mb-0">Order Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Order Date:</strong> <?= format_date($order->created_at) ?></p>
                <p><strong>Status:</strong> 
                    <span class="badge bg-<?= 
                        $order->status === 'delivered' ? 'success' : 
                        ($order->status === 'cancelled' ? 'danger' : 'warning') 
                    ?>">
                        <?= ucfirst($order->status) ?>
                    </span>
                </p>
                <p><strong>Order Type:</strong> <?= ucfirst(str_replace('_', ' ', $order->order_type)) ?></p>
                <?php if ($order->table_number): ?>
                    <p><strong>Table:</strong> <?= htmlspecialchars($order->table_number) ?></p>
                <?php endif; ?>
                <?php if ($order->delivery_address): ?>
                    <p><strong>Delivery Address:</strong> <?= htmlspecialchars($order->delivery_address) ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card bg-card border-gold">
            <div class="card-header">
                <h5 class="mb-0">Order Items</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order->items as $item): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($item->item_name) ?>
                                    <?php if (!empty($item->special_instructions)): ?>
                                        <br><small class="text-muted">Note: <?= htmlspecialchars($item->special_instructions) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= $item->quantity ?></td>
                                <td><?= format_currency($item->price_at_order) ?></td>
                                <td class="text-gold fw-bold"><?= format_currency($item->quantity * $item->price_at_order) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total:</td>
                            <td class="text-gold fw-bold fs-5"><?= format_currency($order->total_amount) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-card border-gold">
            <div class="card-header bg-gold text-dark">
                <h5 class="mb-0">Order Status</h5>
            </div>
            <div class="card-body">
                <div class="progress mb-3" style="height: 20px;">
                    <?php
                    $statuses = ['pending', 'confirmed', 'preparing', 'ready', 'delivered'];
                    $currentIndex = array_search($order->status, $statuses);
                    $progress = (($currentIndex + 1) / count($statuses)) * 100;
                    ?>
                    <div class="progress-bar bg-gold" role="progressbar" style="width: <?= $progress ?>%"></div>
                </div>
                <ul class="list-unstyled">
                    <li class="mb-2"><?= in_array('pending', array_slice($statuses, 0, $currentIndex + 1)) ? '✓' : '○' ?> Pending</li>
                    <li class="mb-2"><?= in_array('confirmed', array_slice($statuses, 0, $currentIndex + 1)) ? '✓' : '○' ?> Confirmed</li>
                    <li class="mb-2"><?= in_array('preparing', array_slice($statuses, 0, $currentIndex + 1)) ? '✓' : '○' ?> Preparing</li>
                    <li class="mb-2"><?= in_array('ready', array_slice($statuses, 0, $currentIndex + 1)) ? '✓' : '○' ?> Ready</li>
                    <li class="mb-2"><?= in_array('delivered', array_slice($statuses, 0, $currentIndex + 1)) ? '✓' : '○' ?> Delivered</li>
                </ul>
                
                <?php if ($order->status === 'delivered'): ?>
                    <a href="<?= url('customer/review/' . $order->id) ?>" class="btn btn-gold w-100 mt-3">
                        Leave Review
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>

