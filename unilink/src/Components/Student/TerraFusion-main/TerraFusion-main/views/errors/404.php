<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #1A1A1A; color: #F0F0F0; font-family: 'Open Sans', sans-serif; }
        .text-gold { color: #C8A252; }
    </style>
</head>
<body>
    <div class="container text-center py-5">
        <h1 class="display-1 text-gold playfair-font">404</h1>
        <h2 class="mb-4">Page Not Found</h2>
        <p class="text-muted mb-4">The page you are looking for does not exist.</p>
        <a href="<?= url('customer/menu') ?>" class="btn btn-gold">Go Home</a>
    </div>
</body>
</html>

