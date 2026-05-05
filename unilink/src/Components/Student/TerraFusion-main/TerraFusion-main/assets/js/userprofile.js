    document.addEventListener('DOMContentLoaded', function() {
      // Initialize tooltips
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });

      // Handle profile form submission (in Modal)
      document.getElementById('profileForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        // Here you would typically send data via fetch to a backend script
        alert('Profile details updated successfully! (Demo)');
        // Close modal
        var modalEl = document.getElementById('editProfileModal');
        var modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();
      });

      // Handle password form submission (in Modal)
      document.getElementById('passwordForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        
        if (newPassword !== confirmPassword) {
            alert('Passwords do not match!');
            return;
        }
        
        // Here you would typically send data via fetch
        alert('Password updated successfully! (Demo)');
        this.reset();
        
        var modalEl = document.getElementById('editProfileModal');
        var modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();
      });
    });
