<?php
$pageTitle = 'Register - TerraFusion';
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card bg-card border-gold">
            <div class="card-body p-4">
                <h2 class="card-title text-center playfair-font mb-4">Create Account</h2>
                
                <form method="POST" action="<?= url('register') ?>">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               value="<?= old('full_name') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= old('email') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               value="<?= old('phone') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required minlength="6">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                    </div>
                    
                    <button type="submit" class="btn btn-gold w-100 mb-3">Register</button>
                </form>
                
                <div class="text-center">
                    <p class="text-muted">Already have an account? <a href="<?= url('login') ?>" class="text-gold">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>

