document.addEventListener('DOMContentLoaded', function() {
    
    // Select the reservation form
    const bookingForm = document.querySelector('form[action*="book-a-table.php"]');
    console.log('Booking form found:', bookingForm);
    
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            console.log('Form submit intercepted');
            e.preventDefault(); // Prevent default PHP submission

            const submitBtn = bookingForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            const loadingDiv = bookingForm.querySelector('.loading');
            const sentMessageDiv = bookingForm.querySelector('.sent-message');
            const errorMessageDiv = bookingForm.querySelector('.error-message');

            // Reset messages
            if(sentMessageDiv) sentMessageDiv.style.display = 'none';
            if(errorMessageDiv) errorMessageDiv.style.display = 'none';
            if(loadingDiv) loadingDiv.style.display = 'block';

            // Disable button
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Booking...';

            const formData = new FormData(bookingForm);

            fetch(bookingForm.action, {
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
                console.log('Response data:', data);
                if (data.status === 'success') {
                    // Success! Show SweetAlert with QR Code
                    Swal.fire({
                        title: 'Reservation Confirmed!',
                        html: `Table for ${data.guests || 'your party'} on ${data.date || 'the selected date'}. <br> <strong>Order #${data.reservation_id}</strong><br><img src="${data.qr_code_url}" alt="QR Code" style="width: 150px; height: 150px; border: 2px solid gold; margin-top: 10px;">`,
                        footer: 'Check your Profile to view this later.',
                        icon: 'success',
                        background: '#121212',
                        color: '#D4AF37',
                        confirmButtonColor: '#D4AF37'
                    });

                    // Reset form
                    bookingForm.reset();
                    if(loadingDiv) loadingDiv.style.display = 'none';
                    if(sentMessageDiv) {
                        sentMessageDiv.textContent = data.message;
                        sentMessageDiv.style.display = 'block';
                    }
                } else {
                    // Backend reported error
                    throw new Error(data.message || 'Form submission failed.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Show Error Alert
                Swal.fire({
                    title: 'Booking Failed',
                    text: error.message || 'Something went wrong. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#d33'
                });

                if(loadingDiv) loadingDiv.style.display = 'none';
                if(errorMessageDiv) {
                    errorMessageDiv.textContent = error.message;
                    errorMessageDiv.style.display = 'block';
                }
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });
    }
});
