<?php
$pageTitle = 'Menu - TerraFusion';
ob_start();
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="playfair-font">Our Menu</h1>
    </div>
    <div class="col-md-4">
        <form method="GET" action="<?= url('customer/menu') ?>">
            <div class="input-group">
                <input type="text" class="form-control" name="search" 
                       placeholder="Search menu..." value="<?= htmlspecialchars($searchQuery ?? '') ?>">
                <button class="btn btn-gold" type="submit">Search</button>
            </div>
        </form>
    </div>
</div>

<!-- Categories Filter -->
<?php if (!empty($categories)): ?>
<div class="mb-4">
    <a href="<?= url('customer/menu') ?>" 
       class="btn <?= empty($selectedCategory) ? 'btn-gold' : 'btn-outline-gold' ?> me-2 mb-2">
        All
    </a>
    <?php foreach ($categories as $category): ?>
        <a href="<?= url('customer/menu?category=' . $category->id) ?>" 
           class="btn <?= $selectedCategory == $category->id ? 'btn-gold' : 'btn-outline-gold' ?> me-2 mb-2">
            <?= htmlspecialchars($category->name) ?>
        </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Menu Items Grid -->
<div class="menu-grid">
    <?php if (empty($menuItems)): ?>
        <div class="col-12">
            <div class="alert alert-info">No menu items found.</div>
        </div>
    <?php else: ?>
        <?php foreach ($menuItems as $item): ?>
            <div class="card menu-card">
                <?php if (!empty($item->image_path)): ?>
                    <img src="<?= asset('images/' . $item->image_path) ?>" 
                         class="card-img-top" 
                         alt="<?= htmlspecialchars($item->name) ?>"
                         style="height: 200px; object-fit: cover;">
                <?php else: ?>
                    <div class="card-img-top bg-dark d-flex align-items-center justify-content-center" 
                         style="height: 200px;">
                        <span class="text-muted">No Image</span>
                    </div>
                <?php endif; ?>
                
                <div class="card-body">
                    <h5 class="card-title playfair-font"><?= htmlspecialchars($item->name) ?></h5>
                    <p class="card-text text-muted"><?= htmlspecialchars($item->description ?? '') ?></p>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="price text-gold fw-bold"><?= format_currency($item->price) ?></span>
                        <button class="btn btn-gold btn-sm add-to-cart" 
                                data-item-id="<?= $item->id ?>">
                            Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.add-to-cart').forEach(btn => {
    btn.addEventListener('click', function() {
        const itemId = this.getAttribute('data-item-id');
        const form = new FormData();
        form.append('menu_item_id', itemId);
        form.append('quantity', 1);
        form.append('csrf_token', '<?= csrf_token() ?>');
        
        fetch('<?= url("order/add-to-cart") ?>', {
            method: 'POST',
            body: form
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Item added to cart!');
            }
        });
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>

