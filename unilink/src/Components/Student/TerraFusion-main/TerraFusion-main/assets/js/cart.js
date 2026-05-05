// Cart Page JavaScript - TerraFusion Restaurant Ordering System

// Make AJAX request to update cart
async function updateCart(action, mealId, quantity = 1) {
    try {
        const formData = new FormData();
        formData.append('action', action);
        formData.append('meal_id', mealId);
        formData.append('quantity', quantity);

        const response = await fetch('cart.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (data.success) {
            // Update cart badge
            const cartBadge = document.getElementById('cart-badge');
            if (cartBadge) {
                cartBadge.textContent = data.cart_count || '0';
                cartBadge.style.display = data.cart_count > 0 ? 'flex' : 'none';
            }

            return true;
        }
        return false;
    } catch (error) {
        console.error('Error updating cart:', error);
        return false;
    }
}

// Update quantity in cart
async function updateQuantity(mealId, newQuantity) {
    return await updateCart('update', mealId, newQuantity);
}

// Update quantity from input field
function updateQuantityFromInput(input) {
    const mealId = input.closest('.cart-item').dataset.itemId;
    const newQuantity = parseInt(input.value) || 1;
    updateQuantity(mealId, newQuantity).then(() => {
        updateCartTotals();
    });
}

// Remove item from cart
async function removeItem(mealId) {
    const cartItem = document.querySelector(`.cart-item[data-item-id="${mealId}"]`);
    if (!cartItem) return;

    const success = await updateCart('remove', mealId);

    if (success) {
        cartItem.style.animation = 'fadeOut 0.3s ease';

        // Remove from DOM after animation
        setTimeout(() => {
            cartItem.remove();
            updateCartTotals();
            checkEmptyCart();
        }, 300);
    }
}

// Update cart totals
function updateCartTotals() {
    let subtotal = 0;
    const cartItems = document.querySelectorAll('.cart-item');

    cartItems.forEach(item => {
        const priceText = item.querySelector('.cart-item-price').textContent;
        const quantityElement = item.querySelector('.quantity-display');
        const quantity = quantityElement ? parseInt(quantityElement.textContent) || 1 : 1;

        // Extract price (handles both "X.XX EGP each" and "X.XX EGP" formats)
        const priceMatch = priceText.match(/([\d.]+)/);
        if (priceMatch) {
            const unitPrice = parseFloat(priceMatch[1]);
            subtotal += unitPrice * quantity;
        }
    });

    const tax = subtotal * 0.10; // 10% tax
    const deliveryFee = 5.00;
    const total = subtotal + tax + deliveryFee;

    // Update the UI
    const subtotalElement = document.getElementById('subtotal');
    const taxElement = document.getElementById('tax');
    const deliveryElement = document.getElementById('delivery-fee');
    const totalElement = document.getElementById('total');

    if (subtotalElement) subtotalElement.textContent = subtotal.toFixed(2) + ' EGP';
    if (taxElement) taxElement.textContent = tax.toFixed(2) + ' EGP';
    if (deliveryElement) deliveryElement.textContent = deliveryFee.toFixed(2) + ' EGP';
    if (totalElement) totalElement.textContent = total.toFixed(2) + ' EGP';
}

// Check if cart is empty and show appropriate message
function checkEmptyCart() {
    const cartItems = document.querySelectorAll('.cart-item');
    const emptyCartMessage = document.getElementById('empty-cart-message');
    const cartItemsContainer = document.getElementById('cart-items');
    const orderSummary = document.querySelector('.order-summary');

    if (cartItems.length === 0) {
        if (emptyCartMessage) emptyCartMessage.style.display = 'block';
        if (cartItemsContainer) cartItemsContainer.style.display = 'none';
        if (orderSummary) orderSummary.style.display = 'none';
    } else {
        if (emptyCartMessage) emptyCartMessage.style.display = 'none';
        if (cartItemsContainer) cartItemsContainer.style.display = 'block';
        if (orderSummary) orderSummary.style.display = 'block';
    }
}

// Initialize cart functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    // Update cart totals on page load
    updateCartTotals();

    // Check if cart is empty on page load
    checkEmptyCart();

    // Handle quantity changes
    document.addEventListener('click', function (e) {
        // Handle plus button
        if (e.target.classList.contains('quantity-btn') && !e.target.classList.contains('minus')) {
            // Ensure we are acting on a cart item, prevents interference with menu page
            if (!e.target.closest('.cart-item')) return;

            const container = e.target.closest('.quantity-controls');
            const display = container.querySelector('.quantity-display');
            let quantity = parseInt(display.textContent) + 1;
            display.textContent = quantity;

            // Update in database
            const mealId = e.target.closest('.cart-item').dataset.itemId;
            updateQuantity(mealId, quantity).then(() => {
                updateCartTotals();
            });
        }

        // Handle minus button
        if (e.target.classList.contains('minus')) {
            // Ensure we are acting on a cart item
            if (!e.target.closest('.cart-item')) return;

            const container = e.target.closest('.quantity-controls');
            const display = container.querySelector('.quantity-display');
            let quantity = parseInt(display.textContent) - 1;

            if (quantity < 1) quantity = 1;

            display.textContent = quantity;

            // Update in database
            const mealId = e.target.closest('.cart-item').dataset.itemId;
            updateQuantity(mealId, quantity).then(() => {
                updateCartTotals();
            });
        }
    });

    // Handle remove item button
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-item-btn')) {
            const mealId = e.target.closest('.cart-item').dataset.itemId;
            removeItem(mealId);
        }
    });
});