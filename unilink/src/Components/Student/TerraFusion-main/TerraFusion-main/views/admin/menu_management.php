<?php
$pageTitle = 'Menu Management - TerraFusion';
ob_start();
?>

<h1 class="playfair-font mb-4">Menu Management</h1>

<div class="row mb-4">
    <div class="col-md-12">
        <button class="btn btn-gold" data-bs-toggle="modal" data-bs-target="#addMenuItemModal">
            Add New Menu Item
        </button>
    </div>
</div>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Status</th>
                <th>Meal Type</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($menuItems as $item): ?>
                <tr>
                    <td><?= $item->id ?></td>
                    <td><?= htmlspecialchars($item->name) ?></td>
                    <td><?= htmlspecialchars($item->meal_type ?? $item->category_name ?? 'N/A') ?></td>
                    <td class="text-gold"><?= format_currency($item->price) ?></td>
                    <td>
                        <span class="badge bg-<?= $item->is_available ? 'success' : 'danger' ?>">
                            <?= $item->is_available ? 'Available' : 'Unavailable' ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-gold" onclick="editItem(<?= $item->id ?>)">Edit</button>
                        <a href="<?= url('admin/menu/delete/' . $item->id) ?>" 
                           class="btn btn-sm btn-danger" 
                           onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Menu Item Modal -->
<div class="modal fade" id="addMenuItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-card text-light">
            <div class="modal-header border-gold">
                <h5 class="modal-title">Add Menu Item</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= url('admin/menu/create') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Meal Type</label>
                        <select name="category_id" class="form-select" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat->id ?>"><?= htmlspecialchars($cat->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price</label>
                        <input type="number" name="price" class="form-control" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_available" id="is_available" checked>
                            <label class="form-check-label" for="is_available">Available</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-gold">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold">Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>

