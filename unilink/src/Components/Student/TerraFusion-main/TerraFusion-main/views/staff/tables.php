<?php
$pageTitle = 'Manage Tables - TerraFusion';
ob_start();
?>

<h1 class="playfair-font mb-4">Table Management</h1>

<div class="row">
    <?php foreach ($tables as $table): ?>
        <div class="col-md-4 mb-3">
            <div class="card bg-card border-gold">
                <div class="card-body text-center">
                    <h5 class="playfair-font">Table <?= htmlspecialchars($table->table_number) ?></h5>
                    <p class="text-muted">Capacity: <?= $table->capacity ?> guests</p>
                    
                    <span class="badge bg-<?= 
                        $table->status === 'available' ? 'success' : 
                        ($table->status === 'occupied' ? 'danger' : 'warning') 
                    ?> mb-3">
                        <?= ucfirst($table->status) ?>
                    </span>
                    
                    <form method="POST" action="<?= url('staff/update-table-status') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="table_id" value="<?= $table->id ?>">
                        <select name="status" class="form-select mb-2">
                            <option value="available" <?= $table->status === 'available' ? 'selected' : '' ?>>Available</option>
                            <option value="occupied" <?= $table->status === 'occupied' ? 'selected' : '' ?>>Occupied</option>
                            <option value="reserved" <?= $table->status === 'reserved' ? 'selected' : '' ?>>Reserved</option>
                        </select>
                        <button type="submit" class="btn btn-gold w-100">Update Status</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>

