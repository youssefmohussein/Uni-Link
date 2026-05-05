<?php
http_response_code(403);
$title = 'Access Denied';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> | Restaurant Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            text-align: center;
            max-width: 600px;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .error-icon {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 1.5rem;
        }
        .btn-home {
            margin-top: 1.5rem;
            padding: 0.5rem 2rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-ban"></i>
        </div>
        <h1 class="display-4 text-danger">403 - Access Denied</h1>
        <p class="lead">You don't have permission to access this page.</p>
        <p class="text-muted">If you believe this is an error, please contact the administrator.</p>
        <div>
            <a href="/Admin/public/dashboard" class="btn btn-primary btn-home">
                <i class="fas fa-home me-2"></i> Back to Dashboard
            </a>
            <a href="/Admin/public/logout" class="btn btn-outline-secondary btn-home ms-2">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
