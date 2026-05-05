<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Users Management</h1>
    <!-- Add New Staff Button -->
    <button type="button" class="btn btn-gold" data-bs-toggle="modal" data-bs-target="#userModal" onclick="resetUserForm()">
        <i class="fas fa-user-plus me-2"></i> Add New User
    </button>
</div>

<div class="table-responsive">
    <table class="table table-custom table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data['users'])): ?>
                <?php foreach ($data['users'] as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['user_id']) ?></td>
                    <td><?= htmlspecialchars($user['full_name'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><span class="badge bg-secondary"><?= htmlspecialchars($user['role']) ?></span></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-sm btn-outline-gold me-2" 
                                    onclick='editUser(<?= json_encode($user) ?>)'
                                    data-bs-toggle="modal" data-bs-target="#userModal">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                    onclick="document.getElementById('deleteUserId').value = <?= htmlspecialchars($user['user_id']) ?>;"
                                    data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center">No users found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add/Edit User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Add New User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm" action="index.php?page=users&action=create" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="userId" name="userId">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current (Edit mode)">
                        <small class="text-muted d-block mt-1">Required for new users.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="Waiter">Waiter</option>
                            <option value="Table Manager">Table Manager</option>
                            <option value="Chef Boss">Chef Boss</option>
                            <option value="Manager">Manager</option>
                        </select>
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

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title" id="deleteUserModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Are you sure you want to delete this user? This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-top border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="index.php?page=users&action=delete" method="POST" class="d-inline mb-0">
                    <input type="hidden" name="userId" id="deleteUserId" value="">
                    <button type="submit" class="btn btn-danger">Yes, Delete User</button>
                </form>
            </div>
        </div>
    </div>
</div>
