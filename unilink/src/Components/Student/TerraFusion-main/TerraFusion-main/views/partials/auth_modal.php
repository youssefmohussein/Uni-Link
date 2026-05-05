<!-- Authentication Modal -->
<div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-gold">
            <div class="modal-header bg-dark text-light border-bottom-gold">
                <h5 class="modal-title playfair-font" id="authModalLabel">Login</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-dark">
                <!-- Login Form -->
                <form id="loginForm" class="auth-form" action="<?= url('login') ?>" method="POST" style="display: block;">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    <div class="mb-3">
                        <label for="loginEmail" class="form-label text-light">Email</label>
                        <input type="email" class="form-control bg-dark text-light border-gold" id="loginEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="loginPassword" class="form-label text-light">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control bg-dark text-light border-gold" id="loginPassword" name="password" required>
                            <button class="btn btn-outline-gold" type="button" id="toggleLoginPassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-gold w-100 mb-2">Login</button>
                    <div class="text-center">
                        <a href="#" class="text-gold switch-form" data-form="register">Don't have an account? Register</a>
                    </div>
                </form>

                <!-- Register Form -->
                <form id="registerForm" class="auth-form" action="<?= url('register') ?>" method="POST" style="display: none;">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    <div class="mb-3">
                        <label for="registerName" class="form-label text-light">Full Name</label>
                        <input type="text" class="form-control bg-dark text-light border-gold" id="registerName" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="registerEmail" class="form-label text-light">Email</label>
                        <input type="email" class="form-control bg-dark text-light border-gold" id="registerEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="registerPhone" class="form-label text-light">Phone</label>
                        <input type="tel" class="form-control bg-dark text-light border-gold" id="registerPhone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="registerPassword" class="form-label text-light">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control bg-dark text-light border-gold" id="registerPassword" name="password" required>
                            <button class="btn btn-outline-gold" type="button" id="toggleRegisterPassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="registerPasswordConfirmation" class="form-label text-light">Confirm Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control bg-dark text-light border-gold" id="registerPasswordConfirmation" name="password_confirmation" required>
                            <button class="btn btn-outline-gold" type="button" id="toggleRegisterPasswordConfirmation">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-gold w-100 mb-2">Register</button>
                    <div class="text-center">
                        <a href="#" class="text-gold switch-form" data-form="login">Already have an account? Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.modal-content {
    border: 1px solid #D4AF37;
    border-radius: 0.5rem;
}
.modal-header {
    border-bottom: 1px solid #D4AF37;
}
.border-gold {
    border-color: #D4AF37 !important;
}
.border-bottom-gold {
    border-bottom: 1px solid #D4AF37 !important;
}
.btn-outline-gold {
    color: #D4AF37;
    border-color: #D4AF37;
}
.btn-outline-gold:hover {
    background-color: #D4AF37;
    color: #000;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize modal
    const authModal = new bootstrap.Modal(document.getElementById('authModal'));
    
    // Handle modal show event
    document.getElementById('authModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const formType = button.getAttribute('data-form') || 'login';
        showForm(formType);
    });
    
    // Toggle password visibility
    function setupPasswordToggle(inputId, buttonId) {
        const passwordInput = document.getElementById(inputId);
        const toggleButton = document.getElementById(buttonId);
        
        if (passwordInput && toggleButton) {
            toggleButton.addEventListener('click', function() {
                const type = passwordInput.type === 'password' ? 'text' : 'password';
                passwordInput.type = type;
                const icon = this.querySelector('i');
                if (icon) {
                    icon.classList.toggle('bi-eye');
                    icon.classList.toggle('bi-eye-slash');
                }
            });
        }
    }
    
    // Initialize password toggles
    setupPasswordToggle('loginPassword', 'toggleLoginPassword');
    setupPasswordToggle('registerPassword', 'toggleRegisterPassword');
    setupPasswordToggle('registerPasswordConfirmation', 'toggleRegisterPasswordConfirmation');
    
    // Show the appropriate form
    function showForm(formType) {
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const modalTitle = document.getElementById('authModalLabel');
        
        if (!loginForm || !registerForm || !modalTitle) return;
        
        if (formType === 'login') {
            loginForm.style.display = 'block';
            registerForm.style.display = 'none';
            modalTitle.textContent = 'Login';
        } else if (formType === 'register') {
            loginForm.style.display = 'none';
            registerForm.style.display = 'block';
            modalTitle.textContent = 'Register';
        }
    }
    
    // Handle form switching
    document.addEventListener('click', function(e) {
        const switchLink = e.target.closest('.switch-form');
        if (!switchLink) return;
        
        e.preventDefault();
        const formType = switchLink.getAttribute('data-form');
        if (formType) {
            showForm(formType);
        }
    });
    
    // Handle form submissions
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Logging in...';
            }
            return true;
        });
    }
    
    if (registerForm) {
        registerForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Registering...';
            }
            return true;
        });
    }
});
</script>
