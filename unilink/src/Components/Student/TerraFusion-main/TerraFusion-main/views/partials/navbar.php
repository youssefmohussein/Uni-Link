<?php
$user = user();
$currentUrl = $_SERVER['REQUEST_URI'] ?? '';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-gold mb-4">
    <div class="container">
        <a class="navbar-brand playfair-font" href="<?= url('customer/menu') ?>">
            <span class="text-gold">Terra</span><span class="text-light">Fusion</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if (auth()): ?>
                    <?php if (is_customer()): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUrl, 'menu') !== false ? 'active' : '' ?>" href="<?= url('customer/menu') ?>">Menu</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUrl, 'order') !== false ? 'active' : '' ?>" href="<?= url('order/cart') ?>">Cart</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUrl, 'orders') !== false ? 'active' : '' ?>" href="<?= url('customer/orders') ?>">My Orders</a>
                        </li>
                    <?php elseif (is_staff()): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUrl, 'staff/dashboard') !== false ? 'active' : '' ?>" href="<?= url('staff/dashboard') ?>">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUrl, 'kitchen') !== false ? 'active' : '' ?>" href="<?= url('staff/kitchen-orders') ?>">Kitchen Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUrl, 'tables') !== false ? 'active' : '' ?>" href="<?= url('staff/tables') ?>">Tables</a>
                        </li>
                    <?php elseif (is_admin()): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUrl, 'admin/dashboard') !== false ? 'active' : '' ?>" href="<?= url('admin/dashboard') ?>">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUrl, 'menu-management') !== false ? 'active' : '' ?>" href="<?= url('admin/menu-management') ?>">Menu</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUrl, 'users') !== false ? 'active' : '' ?>" href="<?= url('admin/users') ?>">Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUrl, 'promotions') !== false ? 'active' : '' ?>" href="<?= url('admin/promotions') ?>">Promotions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($currentUrl, 'reports') !== false ? 'active' : '' ?>" href="<?= url('admin/reports') ?>">Reports</a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
            
            <ul class="navbar-nav">
                <?php if (auth()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <?= htmlspecialchars($user->full_name ?? 'User') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= url('logout') ?>">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#authModal" data-form="login">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-gold ms-2" href="#" data-bs-toggle="modal" data-bs-target="#authModal" data-form="register">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<?php include __DIR__ . '/auth_modal.php'; ?>

