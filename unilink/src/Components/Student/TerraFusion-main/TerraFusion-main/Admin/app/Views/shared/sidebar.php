<?php
// Sidebar Navigation
$currentPage = $_GET['page'] ?? 'dashboard';
?>
<nav id="sidebarMenu" class="d-md-block sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="sidebar-header d-none d-md-block">
            <h3>Terra Fusion</h3>
            <small class="text-muted">Admin Panel</small>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="index.php?page=dashboard">
                    <i class="fas fa-home me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentPage === 'orders' ? 'active' : '' ?>" href="index.php?page=orders">
                    <i class="fas fa-shopping-cart me-2"></i> Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentPage === 'menu' ? 'active' : '' ?>" href="index.php?page=menu">
                    <i class="fas fa-utensils me-2"></i> Menu
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentPage === 'reservations' ? 'active' : '' ?>" href="index.php?page=reservations">
                    <i class="fas fa-calendar-alt me-2"></i> Reservations
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= $currentPage === 'users' ? 'active' : '' ?>" href="index.php?page=users">
                    <i class="fas fa-users-cog me-2"></i> Users Mgmt
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $currentPage === 'profile' ? 'active' : '' ?>" href="index.php?page=profile">
                    <i class="fas fa-user me-2"></i> My Profile
                </a>
            </li>

            <li class="nav-item mt-4">
                <a class="nav-link text-danger" href="index.php?page=logout">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </li>
        </ul>
        
        <div class="mt-5 px-3 text-muted">
            <small>Logged in as:</small><br>
            <strong><?= htmlspecialchars($currentUser['username'] ?? 'User') ?></strong><br>
            <span class="badge bg-secondary"><?= htmlspecialchars($currentUser['role'] ?? '') ?></span>
        </div>
    </div>
</nav>
