document.addEventListener('DOMContentLoaded', function() {
    // Handle add to cart button click
    document.addEventListener('click', function(e) {
        const addToCartBtn = e.target.closest('.add-to-cart-btn');
        if (addToCartBtn) {
            e.preventDefault();
            const container = addToCartBtn.closest('.add-to-cart-btn-container');
            const mealId = addToCartBtn.dataset.mealId;
            
            // Hide add to cart button, show quantity controls
            addToCartBtn.style.display = 'none';
            const quantityContainer = container.querySelector('.quantity-container');
            quantityContainer.style.display = 'flex';
            
            // Add to cart
            if (typeof updateCart === 'function') {
                updateCart('add', mealId, 1);
            } else {
                console.error('updateCart function not found');
            }
        }
    });

    // Handle quantity changes
    document.addEventListener('click', function(e) {
        // Handle plus button
        if (e.target.classList.contains('quantity-btn') && !e.target.classList.contains('minus')) {
            const container = e.target.closest('.quantity-controls');
            const display = container.querySelector('.quantity-display');
            let quantity = parseInt(display.textContent) + 1;
            display.textContent = quantity;
            
            // Update cart
            const mealId = e.target.closest('.menu-item').dataset.itemId;
            if (typeof updateCart === 'function') {
                updateCart('update', mealId, quantity);
            }
        }
        
        // Handle minus button
        if (e.target.classList.contains('minus')) {
            const container = e.target.closest('.quantity-controls');
            const display = container.querySelector('.quantity-display');
            let quantity = parseInt(display.textContent) - 1;
            
            if (quantity <= 0) {
                // If quantity reaches 0, remove from cart and show add to cart button
                const addToCartContainer = e.target.closest('.add-to-cart-btn-container');
                const mealId = e.target.closest('.menu-item').dataset.itemId;
                
                if (typeof updateCart === 'function') {
                    updateCart('remove', mealId).then(() => {
                        addToCartContainer.querySelector('.add-to-cart-btn').style.display = 'block';
                        addToCartContainer.querySelector('.quantity-container').style.display = 'none';
                    });
                }
                return;
            }
            
            display.textContent = quantity;
            
            // Update cart
            const mealId = e.target.closest('.menu-item').dataset.itemId;
            if (typeof updateCart === 'function') {
                updateCart('update', mealId, quantity);
            }
        }
    });
});
