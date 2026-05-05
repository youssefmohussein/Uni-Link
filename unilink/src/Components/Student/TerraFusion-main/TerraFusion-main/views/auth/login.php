<?php
$pageTitle = 'Login - TerraFusion';
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card bg-card border-gold">
            <div class="card-body p-4">
                <h2 class="card-title text-center playfair-font mb-4">Login</h2>
                
                <?php if (hasFlash('error')): ?>
                    <div class="alert alert-danger"><?= getFlash('error') ?></div>
                <?php endif; ?>
                
                <form method="POST" action="<?= url('login') ?>" id="loginForm">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= old('email') ?>" required autofocus>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-gold">Login</button>
                    </div>
                </form>
                
                <div class="text-center mt-3">
                    <p class="mb-0">Don't have an account? <a href="<?= url('register') ?>" class="text-gold">Register here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
ob_start();
?>
<script src="/TerraFusion/public/js/auth.js"></script>
<?php
$scripts = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>

