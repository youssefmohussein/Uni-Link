<?php
$currentUser = [
    'username' => \App\Helpers\Session::get('full_name'),
    'role' => \App\Helpers\Session::get('role')
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terra Fusion Admin</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

    <!-- Mobile Header / Toggle -->
    <nav class="navbar navbar-dark bg-black d-md-none p-3 border-bottom border-secondary">
        <a class="navbar-brand brand-title" href="#">Terra Fusion</a>
        <button class="navbar-toggler" type="button" id="sidebarToggle" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include __DIR__ . '/sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <?php 
                $success = \App\Helpers\Session::getFlash('success');
                $error = \App\Helpers\Session::getFlash('error');
                $sessionError = \App\Helpers\Session::getFlash('session_error'); // From index.php
                ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= htmlspecialchars($success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($sessionError): ?>
                    <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                        <i class="fas fa-user-shield me-2"></i>
                        <?= htmlspecialchars($sessionError) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($content) && file_exists($content)) {
                    include $content;
                } else {
                    echo "<h2>Content not found</h2>";
                } ?>
            </main>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
</body>
</html>
