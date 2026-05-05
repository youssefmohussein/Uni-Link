<?php
$pageTitle = 'Promotions - TerraFusion';
ob_start();
?>

<h1 class="playfair-font mb-4">Promotions Management</h1>

<div class="row mb-4">
    <div class="col-md-12">
        <button class="btn btn-gold" data-bs-toggle="modal" data-bs-target="#addPromoModal">
            Add New Promotion
        </button>
    </div>
</div>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Type</th>
                <th>Value</th>
                <th>Valid From</th>
                <th>Valid To</th>
                <th>Uses</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($promotions as $promo): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($promo->code) ?></strong></td>
                    <td><?= ucfirst($promo->discount_type) ?></td>
                    <td class="text-gold">
                        <?= $promo->discount_type === 'percent' ? $promo->discount_value . '%' : format_currency($promo->discount_value) ?>
                    </td>
                    <td><?= format_date($promo->start_date) ?></td>
                    <td><?= format_date($promo->end_date) ?></td>
                    <td><?= $promo->uses_count ?> / <?= $promo->max_uses ?? '∞' ?></td>
                    <td>
                        <span class="badge bg-<?= $promo->is_active ? 'success' : 'danger' ?>">
                            <?= $promo->is_active ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Promotion Modal -->
<div class="modal fade" id="addPromoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-card text-light">
            <div class="modal-header border-gold">
                <h5 class="modal-title">Add Promotion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= url('admin/promotions/create') ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Promo Code</label>
                        <input type="text" name="code" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Discount Type</label>
                        <select name="discount_type" class="form-select" required>
                            <option value="percent">Percentage</option>
                            <option value="fixed">Fixed Amount</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Discount Value</label>
                        <input type="number" name="discount_value" class="form-control" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Minimum Order Amount</label>
                        <input type="number" name="min_order_amount" class="form-control" step="0.01" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="datetime-local" name="start_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">End Date</label>
                        <input type="datetime-local" name="end_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Max Uses (leave empty for unlimited)</label>
                        <input type="number" name="max_uses" class="form-control">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-gold">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold">Add Promotion</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>

