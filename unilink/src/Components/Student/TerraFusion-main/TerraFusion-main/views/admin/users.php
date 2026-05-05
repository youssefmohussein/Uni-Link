<?php
$pageTitle = 'User Management - TerraFusion';
ob_start();
?>

<h1 class="playfair-font mb-4">User Management</h1>

<div class="row mb-4">
    <div class="col-md-12">
        <button class="btn btn-gold" data-bs-toggle="modal" data-bs-target="#addUserModal">
            Add New User
        </button>
    </div>
</div>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user->id ?></td>
                    <td><?= htmlspecialchars($user->full_name) ?></td>
                    <td><?= htmlspecialchars($user->email) ?></td>
                    <td>
                        <?php
                        $role = \App\Models\Role::find($user->role_id);
                        echo htmlspecialchars($role->name ?? 'N/A');
                        ?>
                    </td>
                    <td><?= format_date($user->created_at) ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-gold" onclick="editUser(<?= $user->id ?>)">Edit</button>
                        <a href="<?= url('admin/users/delete/' . $user->id) ?>" 
                           class="btn btn-sm btn-danger" 
                           onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-card text-light">
            <div class="modal-header border-gold">
                <h5 class="modal-title">Add User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= url('admin/users/create') ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="tel" name="phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role_id" class="form-select" required>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role->id ?>"><?= htmlspecialchars($role->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-gold">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>

