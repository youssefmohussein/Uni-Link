<?php
$pageTitle = 'Checkout - TerraFusion';
ob_start();
?>

<h1 class="playfair-font mb-4">Checkout</h1>

<!-- Progress Bar -->
<div class="progress mb-4" style="height: 6px;">
    <div class="progress-bar bg-gold" role="progressbar" style="width: 100%;"></div>
</div>

<div class="row">
    <div class="col-lg-8">
        <form method="POST" action="<?= url('order/place') ?>">
            <?= csrf_field() ?>
            
            <!-- Order Type -->
            <div class="card bg-card border-gold mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Order Type</h5>
                </div>
                <div class="card-body">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="order_type" id="dine_in" value="dine_in" checked>
                        <label class="form-check-label" for="dine_in">Dine In</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="order_type" id="takeaway" value="takeaway">
                        <label class="form-check-label" for="takeaway">Takeaway</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="order_type" id="delivery" value="delivery">
                        <label class="form-check-label" for="delivery">Delivery</label>
                    </div>
                </div>
            </div>
            
            <!-- Dine In Table Selection -->
            <div class="card bg-card border-gold mb-3" id="table-selection" style="display: block;">
                <div class="card-header">
                    <h5 class="mb-0">Select Table</h5>
                </div>
                <div class="card-body">
                    <select name="table_id" class="form-select" required>
                        <option value="">Select a table</option>
                        <?php foreach ($tables as $table): ?>
                            <option value="<?= $table->id ?>">Table <?= htmlspecialchars($table->table_number) ?> (Capacity: <?= $table->capacity ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <!-- Delivery Address -->
            <div class="card bg-card border-gold mb-3" id="delivery-address" style="display: none;">
                <div class="card-header">
                    <h5 class="mb-0">Delivery Address</h5>
                </div>
                <div class="card-body">
                    <textarea name="delivery_address" class="form-control" rows="3" placeholder="Enter your delivery address"></textarea>
                </div>
            </div>
            
            <!-- Special Instructions -->
            <div class="card bg-card border-gold mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Special Instructions</h5>
                </div>
                <div class="card-body">
                    <textarea name="special_instructions" class="form-control" rows="3" placeholder="Any special requests?"></textarea>
                </div>
            </div>
            
            <!-- Payment Method -->
            <div class="card bg-card border-gold mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Payment Method</h5>
                </div>
                <div class="card-body">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="payment_method" id="cash" value="cash" checked>
                        <label class="form-check-label" for="cash">Cash</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="payment_method" id="card" value="card">
                        <label class="form-check-label" for="card">Card</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="mobile" value="mobile">
                        <label class="form-check-label" for="mobile">Mobile Payment</label>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-gold btn-lg w-100">Place Order</button>
        </form>
    </div>
    
    <!-- Order Summary Sidebar -->
    <div class="col-lg-4">
        <div class="card bg-card border-gold sticky-top" style="top: 20px;">
            <div class="card-header bg-gold text-dark">
                <h5 class="mb-0">Order Summary</h5>
            </div>
            <div class="card-body">
                <?php foreach ($cartItems as $cartItem): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span><?= htmlspecialchars($cartItem['menu_item']->name) ?> x<?= $cartItem['quantity'] ?></span>
                        <span><?= format_currency($cartItem['subtotal']) ?></span>
                    </div>
                <?php endforeach; ?>
                
                <hr>
                
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span><?= format_currency($subtotal) ?></span>
                </div>
                
                <?php if ($discount > 0): ?>
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Discount:</span>
                        <span>-<?= format_currency($discount) ?></span>
                    </div>
                <?php endif; ?>
                
                <hr>
                
                <div class="d-flex justify-content-between">
                    <span class="fw-bold fs-5">Total:</span>
                    <span class="text-gold fw-bold fs-5"><?= format_currency($total) ?></span>
                </div>
            </div>
        </div>
        
        <!-- Promo Code -->
        <div class="card bg-card border-gold mt-3">
            <div class="card-body">
                <form method="POST" action="<?= url('order/apply-promo') ?>">
                    <?= csrf_field() ?>
                    <div class="input-group">
                        <input type="text" name="promo_code" class="form-control" placeholder="Promo Code">
                        <button type="submit" class="btn btn-gold">Apply</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Show/hide fields based on order type
document.querySelectorAll('input[name="order_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const tableSelection = document.getElementById('table-selection');
        const deliveryAddress = document.getElementById('delivery-address');
        const tableSelect = document.querySelector('select[name="table_id"]');
        const deliveryTextarea = document.querySelector('textarea[name="delivery_address"]');
        
        if (this.value === 'dine_in') {
            tableSelection.style.display = 'block';
            deliveryAddress.style.display = 'none';
            tableSelect.required = true;
            deliveryTextarea.required = false;
        } else if (this.value === 'delivery') {
            tableSelection.style.display = 'none';
            deliveryAddress.style.display = 'block';
            tableSelect.required = false;
            deliveryTextarea.required = true;
        } else {
            tableSelection.style.display = 'none';
            deliveryAddress.style.display = 'none';
            tableSelect.required = false;
            deliveryTextarea.required = false;
        }
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>

