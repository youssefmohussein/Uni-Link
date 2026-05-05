(function() {
  "use strict";

  // Auth functionality
  document.addEventListener('DOMContentLoaded', function() {
    // Handle login form submission
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Logging in...';
            
            fetch('auth.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Redirect on success
                    window.location.href = data.redirect || 'userprofile.php';
                } else {
                    showAuthError(loginForm, data.message || 'Login failed. Please check your credentials.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAuthError(loginForm, 'An error occurred. Please try again.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });
    }

    // Handle registration form submission
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            // Client-side validation
            const password = formData.get('password');
            const confirmPassword = formData.get('confirm_password');
            
            if (password !== confirmPassword) {
                showAuthError(registerForm, 'Passwords do not match');
                return;
            }
            
            if (password.length < 6) {
                showAuthError(registerForm, 'Password must be at least 6 characters long');
                return;
            }

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Creating account...';

            fetch('auth.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Redirect on success
                    window.location.href = data.redirect || 'userprofile.php';
                } else {
                    showAuthError(registerForm, data.message || 'Registration failed. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAuthError(registerForm, 'An error occurred. Please try again.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });
    }

    // Helper function to show error messages
    function showAuthError(form, message) {
        // Remove any existing error messages
        const existingError = form.querySelector('.auth-error-message');
        if (existingError) {
            existingError.remove();
        }
        
        // Create and show new error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'auth-error-message alert alert-danger mt-3';
        errorDiv.textContent = message;
        form.appendChild(errorDiv);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            errorDiv.style.opacity = '0';
            setTimeout(() => errorDiv.remove(), 300);
        }, 5000);
    }

    // Update navigation based on login status
    function updateNavigation() {
        const signupBtn = document.querySelector('.btn-book-a-table');
        if (signupBtn) {
            // Check if user is logged in (you'll need to add this class to body in PHP)
            const isLoggedIn = document.body.classList.contains('logged-in');
            
            if (isLoggedIn) {
                signupBtn.innerHTML = '<i class="bi bi-person-circle"></i>';
                signupBtn.title = 'My Profile';
                signupBtn.href = 'userprofile.php';
                signupBtn.removeAttribute('data-bs-toggle');
                signupBtn.removeAttribute('data-bs-target');
            } else {
                signupBtn.innerHTML = '<i class="bi bi-person"></i>';
                signupBtn.title = 'Profile';
                signupBtn.href = '#';
                signupBtn.setAttribute('data-bs-toggle', 'modal');
                signupBtn.setAttribute('data-bs-target', '#signupModal');
            }
        }
    }

    // Call the function on page load
    updateNavigation();

    // Add animation to form inputs on focus
    const formInputs = document.querySelectorAll('.form-control');
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });
        
        // Check if input has value on page load
        if (input.value) {
            input.parentElement.classList.add('focused');
        }
    });
  });

  // Signup Modal Functionality
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize modal if it exists
    const signupModal = document.getElementById('signupModal');
    if (signupModal) {
      const modal = new bootstrap.Modal(signupModal);
      
      // Handle form submission
      const signupForm = document.getElementById('signupForm');
      if (signupForm) {
        signupForm.addEventListener('submit', function(e) {
          const email = document.getElementById('email').value;
          const password = document.getElementById('password').value;
          
          // Log the signup attempt
          console.log('Signup attempt with:', { email, password });
          
          // The form will now submit to the server
          // The server will handle the redirection for admin users
        });
      }
      
      // Handle forgot password
      const forgotPassword = document.getElementById('forgotPassword');
      if (forgotPassword) {
        forgotPassword.addEventListener('click', function(e) {
          e.preventDefault();
          const email = prompt('Please enter your email to reset your password:');
          if (email) {
            alert('Password reset link has been sent to ' + email);
          }
        });
      }
      
      // Handle Google signup
      const googleSignup = document.getElementById('googleSignup');
      if (googleSignup) {
        googleSignup.addEventListener('click', function() {
          // This would typically redirect to your OAuth endpoint
          alert('Redirecting to Google sign up...');
          // window.location.href = 'your-google-oauth-endpoint';
        });
      }
    }
  });

  /**
   * Apply .scrolled class to the body as the page is scrolled down
   */
  function toggleScrolled() {
    const selectBody = document.querySelector('body');
    const selectHeader = document.querySelector('#header');
    if (!selectHeader.classList.contains('scroll-up-sticky') && !selectHeader.classList.contains('sticky-top') && !selectHeader.classList.contains('fixed-top')) return;
    window.scrollY > 100 ? selectBody.classList.add('scrolled') : selectBody.classList.remove('scrolled');
  }

  document.addEventListener('scroll', toggleScrolled);
  window.addEventListener('load', toggleScrolled);

  /**
   * Mobile nav toggle
   */
  const mobileNavToggleBtn = document.querySelector('.mobile-nav-toggle');

  function mobileNavToogle() {
    document.querySelector('body').classList.toggle('mobile-nav-active');
    mobileNavToggleBtn.classList.toggle('bi-list');
    mobileNavToggleBtn.classList.toggle('bi-x');
  }
  mobileNavToggleBtn.addEventListener('click', mobileNavToogle);

  /**
   * Hide mobile nav on same-page/hash links
   */
  document.querySelectorAll('#navmenu a').forEach(navmenu => {
    navmenu.addEventListener('click', () => {
      if (document.querySelector('.mobile-nav-active')) {
        mobileNavToogle();
      }
    });

  });

  /**
   * Toggle mobile nav dropdowns
   */
  document.querySelectorAll('.navmenu .toggle-dropdown').forEach(navmenu => {
    navmenu.addEventListener('click', function(e) {
      e.preventDefault();
      this.parentNode.classList.toggle('active');
      this.parentNode.nextElementSibling.classList.toggle('dropdown-active');
      e.stopImmediatePropagation();
    });
  });

  /**
   * Preloader
   */
  const preloader = document.querySelector('#preloader');
  if (preloader) {
    window.addEventListener('load', () => {
      preloader.remove();
    });
  }

  /**
   * Scroll top button
   */
  let scrollTop = document.querySelector('.scroll-top');

  function toggleScrollTop() {
    if (scrollTop) {
      window.scrollY > 100 ? scrollTop.classList.add('active') : scrollTop.classList.remove('active');
    }
  }
  scrollTop.addEventListener('click', (e) => {
    e.preventDefault();
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });

  window.addEventListener('load', toggleScrollTop);
  document.addEventListener('scroll', toggleScrollTop);

  /**
   * Animation on scroll function and init
   */
  function aosInit() {
    AOS.init({
      duration: 600,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    });
  }
  window.addEventListener('load', aosInit);

  /**
   * Initiate glightbox
   */
  const glightbox = GLightbox({
    selector: '.glightbox'
  });

  /**
   * Init isotope layout and filters
   */
  document.querySelectorAll('.isotope-layout').forEach(function(isotopeItem) {
    let layout = isotopeItem.getAttribute('data-layout') ?? 'masonry';
    let filter = isotopeItem.getAttribute('data-default-filter') ?? '*';
    let sort = isotopeItem.getAttribute('data-sort') ?? 'original-order';

    let initIsotope;
    imagesLoaded(isotopeItem.querySelector('.isotope-container'), function() {
      initIsotope = new Isotope(isotopeItem.querySelector('.isotope-container'), {
        itemSelector: '.isotope-item',
        layoutMode: layout,
        filter: filter,
        sortBy: sort
      });
    });

    isotopeItem.querySelectorAll('.isotope-filters li').forEach(function(filters) {
      filters.addEventListener('click', function() {
        isotopeItem.querySelector('.isotope-filters .filter-active').classList.remove('filter-active');
        this.classList.add('filter-active');
        initIsotope.arrange({
          filter: this.getAttribute('data-filter')
        });
        if (typeof aosInit === 'function') {
          aosInit();
        }
      }, false);
    });

  });

  /**
   * Init swiper sliders
   */
  function initSwiper() {
    document.querySelectorAll(".init-swiper").forEach(function(swiperElement) {
      let config = JSON.parse(
        swiperElement.querySelector(".swiper-config").innerHTML.trim()
      );

      if (swiperElement.classList.contains("swiper-tab")) {
        initSwiperWithCustomPagination(swiperElement, config);
      } else {
        new Swiper(swiperElement, config);
      }
    });
  }

  window.addEventListener("load", initSwiper);

  /**
   * Correct scrolling position upon page load for URLs containing hash links.
   */
  window.addEventListener('load', function(e) {
    if (window.location.hash) {
      if (document.querySelector(window.location.hash)) {
        setTimeout(() => {
          let section = document.querySelector(window.location.hash);
          let scrollMarginTop = getComputedStyle(section).scrollMarginTop;
          window.scrollTo({
            top: section.offsetTop - parseInt(scrollMarginTop),
            behavior: 'smooth'
          });
        }, 100);
      }
    }
  });

  /**
   * Navmenu Scrollspy
   */
  let navmenulinks = document.querySelectorAll('.navmenu a');

  function navmenuScrollspy() {
    navmenulinks.forEach(navmenulink => {
      if (!navmenulink.hash) return;
      let section = document.querySelector(navmenulink.hash);
      if (!section) return;
      let position = window.scrollY + 200;
      if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
        document.querySelectorAll('.navmenu a.active').forEach(link => link.classList.remove('active'));
        navmenulink.classList.add('active');
      } else {
        navmenulink.classList.remove('active');
      }
    })
  }
  window.addEventListener('load', navmenuScrollspy);
  document.addEventListener('scroll', navmenuScrollspy);

})();