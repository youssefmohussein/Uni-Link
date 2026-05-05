<?php
$pageTitle = 'Leave Review - TerraFusion';
ob_start();
?>

<h1 class="playfair-font mb-4">Leave Review for Order #<?= $order->id ?></h1>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card bg-card border-gold">
            <div class="card-body">
                <form method="POST" action="<?= url('customer/review') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="order_id" value="<?= $order->id ?>">
                    
                    <div class="mb-4 text-center">
                        <label class="form-label d-block mb-3">Rating</label>
                        <div class="rating">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" name="rating" id="rating<?= $i ?>" value="<?= $i ?>" required>
                                <label for="rating<?= $i ?>" class="text-gold fs-3">★</label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment</label>
                        <textarea name="comment" id="comment" class="form-control" rows="5" 
                                  placeholder="Share your experience..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-gold w-100">Submit Review</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: center;
    gap: 10px;
}

.rating input {
    display: none;
}

.rating label {
    cursor: pointer;
    color: #999999;
    transition: color 0.2s;
}

.rating input:checked ~ label,
.rating label:hover,
.rating label:hover ~ label {
    color: #C8A252;
}
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>

