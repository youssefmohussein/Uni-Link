// Checkout Page JavaScript - TerraFusion Restaurant Ordering System

let currentStep = 1;
const totalSteps = 3;

// Initialize checkout page
document.addEventListener('DOMContentLoaded', function() {
    updateStepDisplay();
    setupOrderTypeHandler();
    setupPaymentMethodHandler();
    setupFormValidation();
    setupCardInputFormatting();
    setupCreditCardFlip();
    setupCreditCardDisplay();
});

// Handle order type selection
function setupOrderTypeHandler() {
    const orderTypeSelect = document.getElementById('order_type');
    const tableSelection = document.getElementById('table-selection');
    const deliveryAddress = document.getElementById('delivery-address');
    const deliveryAddress2 = document.getElementById('delivery-address-2');
    
    if (orderTypeSelect) {
        orderTypeSelect.addEventListener('change', function() {
            const orderType = this.value;
            
            // Show/hide table selection for dine-in
            if (orderType === 'dine-in') {
                tableSelection.style.display = 'block';
                deliveryAddress.style.display = 'none';
                deliveryAddress2.style.display = 'none';
                document.getElementById('table_number').required = true;
                document.getElementById('address_line1').required = false;
            } else if (orderType === 'delivery') {
                tableSelection.style.display = 'none';
                deliveryAddress.style.display = 'block';
                deliveryAddress2.style.display = 'block';
                document.getElementById('table_number').required = false;
                document.getElementById('address_line1').required = true;
            } else {
                tableSelection.style.display = 'none';
                deliveryAddress.style.display = 'none';
                deliveryAddress2.style.display = 'none';
                document.getElementById('table_number').required = false;
                document.getElementById('address_line1').required = false;
            }
        });
    }
}

// Handle payment method selection
function setupPaymentMethodHandler() {
    const paymentMethods = document.querySelectorAll('.payment-method');
    paymentMethods.forEach(method => {
        method.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                selectPaymentMethod(radio.value);
            }
        });
    });
}

function selectPaymentMethod(method) {
    // Remove selected class from all payment methods
    document.querySelectorAll('.payment-method').forEach(m => {
        m.classList.remove('selected');
    });
    
    // Add selected class to clicked method
    const selectedMethod = document.querySelector(`.payment-method:has(input[value="${method}"])`);
    if (selectedMethod) {
        selectedMethod.classList.add('selected');
    }
    
    // Show/hide card details
    const cardDetails = document.getElementById('card-details');
    if (cardDetails) {
        if (method === 'card') {
            cardDetails.style.display = 'block';
        } else {
            cardDetails.style.display = 'none';
        }
    }
}

// Setup form validation
function setupFormValidation() {
    const form = document.getElementById('checkout-form');
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('error')) {
                validateField(this);
            }
        });
    });
    
    // Email validation
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailRegex.test(this.value)) {
                showFieldError(this, 'Please enter a valid email address');
            }
        });
    }
    
    // Phone validation
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^\d+\-() ]/g, '');
        });
    }
}

// Validate individual field
function validateField(field) {
    const value = field.value.trim();
    const isRequired = field.hasAttribute('required');
    
    if (isRequired && !value) {
        showFieldError(field, 'This field is required');
        return false;
    }
    
    // Special validations
    if (field.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            showFieldError(field, 'Please enter a valid email address');
            return false;
        }
    }
    
    if (field.id === 'card_number' && value) {
        const cardNumber = value.replace(/\s/g, '');
        if (cardNumber.length < 13 || cardNumber.length > 19) {
            showFieldError(field, 'Please enter a valid card number');
            return false;
        }
    }
    
    if (field.id === 'card_expiry' && value) {
        const expiryRegex = /^(0[1-9]|1[0-2])\/\d{2}$/;
        if (!expiryRegex.test(value)) {
            showFieldError(field, 'Please enter a valid expiry date (MM/YY)');
            return false;
        }
    }
    
    if (field.id === 'card_cvv' && value) {
        if (value.length < 3 || value.length > 4) {
            showFieldError(field, 'Please enter a valid CVV');
            return false;
        }
    }
    
    clearFieldError(field);
    return true;
}

// Show field error
function showFieldError(field, message) {
    field.classList.add('error');
    const errorElement = field.parentElement.querySelector('.form-error');
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }
}

// Clear field error
function clearFieldError(field) {
    field.classList.remove('error');
    const errorElement = field.parentElement.querySelector('.form-error');
    if (errorElement) {
        errorElement.style.display = 'none';
    }
}

// Setup card input formatting
function setupCardInputFormatting() {
    const cardNumberInput = document.getElementById('card_number');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function() {
            let value = this.value.replace(/\s/g, '');
            value = value.replace(/\D/g, '');
            value = value.match(/.{1,4}/g)?.join(' ') || value;
            this.value = value;
            updateCardDisplay('number', value || '1234 5678 9012 3456');
        });
    }
    
    const cardExpiryInput = document.getElementById('card_expiry');
    if (cardExpiryInput) {
        cardExpiryInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            this.value = value;
            updateCardDisplay('expiry', value || 'MM/YY');
        });
    }
    
    const cardCvvInput = document.getElementById('card_cvv');
    if (cardCvvInput) {
        cardCvvInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
            updateCardDisplay('cvv', this.value || '123');
        });
    }
    
    const cardholderNameInput = document.getElementById('cardholder_name');
    if (cardholderNameInput) {
        cardholderNameInput.addEventListener('input', function() {
            updateCardDisplay('name', this.value.toUpperCase() || 'JOHN DOE');
        });
    }
}

// Setup credit card flip animation
function setupCreditCardFlip() {
    const cardCvvInput = document.getElementById('card_cvv');
    const creditCard = document.getElementById('credit-card');
    
    if (cardCvvInput && creditCard) {
        cardCvvInput.addEventListener('focus', function() {
            creditCard.classList.add('flipped');
        });
        
        cardCvvInput.addEventListener('blur', function() {
            if (!this.value) {
                creditCard.classList.remove('flipped');
            }
        });
        
        // Also flip when focusing on other card fields (flip back)
        const cardFields = ['card_number', 'card_expiry', 'cardholder_name'];
        cardFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.addEventListener('focus', function() {
                    creditCard.classList.remove('flipped');
                });
            }
        });
    }
}

// Update credit card display
function updateCardDisplay(type, value) {
    const card = document.getElementById('credit-card');
    if (!card) return;
    
    switch(type) {
        case 'number':
            const numberDisplay = document.getElementById('card-display-number');
            if (numberDisplay) {
                numberDisplay.textContent = value || '1234 5678 9012 3456';
            }
            break;
        case 'expiry':
            const expiryDisplay = document.getElementById('card-display-expiry');
            if (expiryDisplay) {
                expiryDisplay.textContent = value || 'MM/YY';
            }
            break;
        case 'cvv':
            const cvvDisplay = document.getElementById('card-display-cvv');
            if (cvvDisplay) {
                cvvDisplay.textContent = value || '123';
            }
            break;
        case 'name':
            const nameDisplay = document.getElementById('card-display-name');
            if (nameDisplay) {
                nameDisplay.textContent = value || 'JOHN DOE';
            }
            break;
    }
}

// Setup credit card display updates
function setupCreditCardDisplay() {
    // Initialize with default values
    updateCardDisplay('number', '1234 5678 9012 3456');
    updateCardDisplay('expiry', 'MM/YY');
    updateCardDisplay('cvv', '123');
    updateCardDisplay('name', 'JOHN DOE');
}

// Navigate to next step
function nextStep() {
    if (validateCurrentStep()) {
        if (currentStep < totalSteps) {
            currentStep++;
            updateStepDisplay();
            
            // Populate review section on step 3
            if (currentStep === 3) {
                populateReviewSection();
            }
        }
    }
}

// Navigate to previous step
function previousStep() {
    if (currentStep > 1) {
        currentStep--;
        updateStepDisplay();
    }
}

// Validate current step
function validateCurrentStep() {
    const currentSection = document.getElementById(`step-${currentStep}`);
    const requiredFields = currentSection.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!validateField(field)) {
            isValid = false;
        }
    });
    
    // Special validation for step 2 (payment)
    if (currentStep === 2) {
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
        if (!paymentMethod) {
            alert('Please select a payment method');
            isValid = false;
        } else if (paymentMethod.value === 'card') {
            const cardFields = ['card_number', 'card_expiry', 'card_cvv', 'cardholder_name'];
            cardFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field && !validateField(field)) {
                    isValid = false;
                }
            });
        }
    }
    
    // Special validation for step 1 (order type)
    if (currentStep === 1) {
        const orderType = document.getElementById('order_type').value;
        if (orderType === 'dine-in') {
            const tableNumber = document.getElementById('table_number');
            if (!tableNumber.value) {
                showFieldError(tableNumber, 'Please select a table number');
                isValid = false;
            }
        } else if (orderType === 'delivery') {
            const address = document.getElementById('address_line1');
            if (!address.value) {
                showFieldError(address, 'Please enter your delivery address');
                isValid = false;
            }
        }
    }
    
    return isValid;
}

// Update step display
function updateStepDisplay() {
    // Update step indicators
    document.querySelectorAll('.checkout-step').forEach((step, index) => {
        const stepNum = index + 1;
        step.classList.remove('active', 'completed');
        
        if (stepNum < currentStep) {
            step.classList.add('completed');
        } else if (stepNum === currentStep) {
            step.classList.add('active');
        }
    });
    
    // Update form sections
    document.querySelectorAll('.checkout-form-section').forEach((section, index) => {
        const sectionNum = index + 1;
        section.classList.remove('active');
        
        if (sectionNum === currentStep) {
            section.classList.add('active');
        }
    });
    
    // Update navigation buttons
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const submitBtn = document.getElementById('submit-btn');
    
    if (prevBtn) {
        prevBtn.style.display = currentStep > 1 ? 'inline-block' : 'none';
    }
    
    if (nextBtn) {
        nextBtn.style.display = currentStep < totalSteps ? 'inline-block' : 'none';
    }
    
    if (submitBtn) {
        submitBtn.style.display = currentStep === totalSteps ? 'inline-block' : 'none';
    }
}

// Populate review section
function populateReviewSection() {
    const reviewContent = document.getElementById('review-info-content');
    if (!reviewContent) return;
    
    const orderType = document.getElementById('order_type').value;
    const firstName = document.getElementById('first_name').value;
    const lastName = document.getElementById('last_name').value;
    const email = document.getElementById('email').value;
    const phone = document.getElementById('phone').value;
    
    let html = `
        <div style="margin-bottom: 1rem;">
            <strong style="color: var(--text-primary);">Name:</strong> ${firstName} ${lastName}
        </div>
        <div style="margin-bottom: 1rem;">
            <strong style="color: var(--text-primary);">Email:</strong> ${email}
        </div>
        <div style="margin-bottom: 1rem;">
            <strong style="color: var(--text-primary);">Phone:</strong> ${phone}
        </div>
        <div style="margin-bottom: 1rem;">
            <strong style="color: var(--text-primary);">Order Type:</strong> ${orderType.charAt(0).toUpperCase() + orderType.slice(1).replace('-', ' ')}
        </div>
    `;
    
    if (orderType === 'dine-in') {
        const tableNumber = document.getElementById('table_number').value;
        html += `<div><strong style="color: var(--text-primary);">Table:</strong> Table ${tableNumber}</div>`;
    } else if (orderType === 'delivery') {
        const address = document.getElementById('address_line1').value;
        const city = document.getElementById('city').value;
        const postalCode = document.getElementById('postal_code').value;
        html += `<div style="margin-top: 1rem;"><strong style="color: var(--text-primary);">Address:</strong> ${address}`;
        if (city) html += `, ${city}`;
        if (postalCode) html += ` ${postalCode}`;
        html += `</div>`;
    }
    
    const specialInstructions = document.getElementById('special_instructions').value;
    if (specialInstructions) {
        html += `<div style="margin-top: 1rem;"><strong style="color: var(--text-primary);">Special Instructions:</strong> ${specialInstructions}</div>`;
    }
    
    reviewContent.innerHTML = html;
}

// Handle form submission
// document.getElementById('checkout-form').addEventListener('submit', function(e) {
//     // Let the form submit normally to process-order.php
// });

