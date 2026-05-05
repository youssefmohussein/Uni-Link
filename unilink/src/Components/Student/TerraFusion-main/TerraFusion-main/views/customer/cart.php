<?php
$pageTitle = 'Shopping Cart - TerraFusion';
ob_start();
?>

<h1 class="playfair-font mb-4">Shopping Cart</h1>

<?php if (empty($cartItems)): ?>
    <div class="alert alert-info">
        Your cart is empty. <a href="<?= url('customer/menu') ?>" class="text-gold">Browse Menu</a>
    </div>
<?php else: ?>
    <div class="row">
        <div class="col-md-8">
            <div class="card bg-card border-gold">
                <div class="card-body">
                    <?php foreach ($cartItems as $index => $cartItem): ?>
                        <div class="row align-items-center mb-3 pb-3 border-bottom border-gold">
                            <div class="col-md-8">
                                <h5 class="playfair-font"><?= htmlspecialchars($cartItem['menu_item']->name) ?></h5>
                                <p class="text-muted mb-0"><?= format_currency($cartItem['menu_item']->price) ?> each</p>
                                <?php if (!empty($cartItem['special_instructions'])): ?>
                                    <small class="text-muted">Note: <?= htmlspecialchars($cartItem['special_instructions']) ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-2 text-center">
                                <span class="fw-bold">Qty: <?= $cartItem['quantity'] ?></span>
                            </div>
                            <div class="col-md-2 text-end">
                                <span class="text-gold fw-bold"><?= format_currency($cartItem['subtotal']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-card border-gold">
                <div class="card-header bg-gold text-dark">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Subtotal:</span>
                        <span class="fw-bold"><?= format_currency($total) ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold">Total:</span>
                        <span class="text-gold fw-bold fs-5"><?= format_currency($total) ?></span>
                    </div>
                    <a href="<?= url('order/checkout') ?>" class="btn btn-gold w-100">Proceed to Checkout</a>
                    <a href="<?= url('customer/menu') ?>" class="btn btn-outline-gold w-100 mt-2">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>

