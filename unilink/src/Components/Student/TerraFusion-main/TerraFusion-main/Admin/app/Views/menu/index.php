<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Menu Management</h1>
    <?php if(isset($_SESSION['role_id']) && $_SESSION['role_id'] >= 3): // Chef Boss or Manager ?>
    <button type="button" class="btn btn-gold" data-bs-toggle="modal" data-bs-target="#menuModal" onclick="resetForm()">
        <i class="fas fa-plus me-2"></i> Add Meal
    </button>
    <?php endif; ?>
</div>

<div class="table-responsive">
    <table class="table table-custom table-hover">
        <thead>
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Price</th>
                <th>Description</th>
                <th>Availability</th>
                <?php if(isset($_SESSION['role_id']) && $_SESSION['role_id'] >= 3): ?>
                <th>Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data['menuItems'])): ?>
                <?php foreach ($data['menuItems'] as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['meal_name']) ?></td>
                    <td><span class="badge bg-secondary"><?= htmlspecialchars($item['meal_type']) ?></span></td>
                    <td>EGP <?= number_format($item['price'], 2) ?></td>
                    <td><?= htmlspecialchars(substr($item['description'], 0, 50)) ?>...</td>
                    <td>
                        <span class="badge <?= $item['availability'] === 'Available' ? 'bg-success' : 'bg-danger' ?>">
                            <?= htmlspecialchars($item['availability']) ?>
                        </span>
                    </td>
                    <?php if(isset($_SESSION['role_id']) && $_SESSION['role_id'] >= 3): ?>
                    <td>
                        <button class="btn btn-sm btn-outline-gold me-2" 
                                onclick='editItem(<?= json_encode($item) ?>)'
                                data-bs-toggle="modal" data-bs-target="#menuModal">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <a href="index.php?page=menu&action=delete&id=<?= $item['meal_id'] ?>" 
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Are you sure you want to delete this item?');">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">No menu items found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="menuModalLabel">Add Menu Item</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?page=menu&action=save" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="itemId" name="id">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Meal Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="meal_type" class="form-label">Meal Type</label>
                            <select class="form-select" id="meal_type" name="meal_type" required>
                                <?php if (!empty($data['uniqueMealTypes'])): ?>
                                    <?php foreach ($data['uniqueMealTypes'] as $type): ?>
                                        <option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="Breakfast">Breakfast</option>
                                    <option value="Lunch">Lunch</option>
                                    <option value="Dinner">Dinner</option>
                                    <option value="Drinks">Drinks</option>
                                    <option value="Appetizers">Appetizers</option>
                                    <option value="Desserts">Desserts</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Price (EGP)</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="meal_image" class="form-label">Meal Image</label>
                        <input type="file" class="form-control" id="meal_image" name="meal_image" accept="image/*">
                        <input type="hidden" id="current_image" name="current_image">
                        <div class="form-text text-muted">Click to browse and select an image from your computer</div>
                        <div id="current_image_preview" class="mt-2" style="display: none;">
                            <small class="text-muted">Current: <span id="current_image_name"></span></small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_available" name="is_available" checked>
                        <label class="form-check-label" for="is_available">Available</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-gold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
