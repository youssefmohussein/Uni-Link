// Menu Page JavaScript - TerraFusion Restaurant Ordering System

// Add item to cart
function addToCart(item) {
    // Ensure item is an object
    if (typeof item === 'string') {
        try {
            item = JSON.parse(item);
        } catch (e) {
            console.error('Invalid item data:', e);
            return;
        }
    }
    
    if (!item || !item.id) {
        console.error('Invalid item data');
        return;
    }
    
    // Get existing cart from localStorage
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Check if item already exists in cart
    const existingItemIndex = cart.findIndex(cartItem => cartItem.id === item.id);
    
    if (existingItemIndex > -1) {
        // Increment quantity
        cart[existingItemIndex].quantity += 1;
    } else {
        // Add new item with quantity 1
        cart.push({
            ...item,
            quantity: 1
        });
    }
    
    // Save to localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Update cart badge
    updateCartBadge();
    
    // Show success message
    showAddToCartMessage(item.name);
    
    // Animate cart icon
    animateCartIcon();
}

// Update cart badge
function updateCartBadge() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const totalItems = cart.reduce((sum, item) => sum + (item.quantity || 0), 0);
    const cartBadge = document.getElementById('cart-badge');
    
    if (cartBadge) {
        if (totalItems > 0) {
            cartBadge.textContent = totalItems;
            cartBadge.style.display = 'flex';
        } else {
            cartBadge.style.display = 'none';
        }
    }
}

// Show add to cart success message
function showAddToCartMessage(itemName) {
    // Create toast notification
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed;
        top: 2rem;
        right: 2rem;
        background-color: var(--card-bg);
        border: 2px solid var(--accent-gold);
        border-radius: 8px;
        padding: 1rem 1.5rem;
        color: var(--text-primary);
        z-index: 10000;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
        animation: slideIn 0.3s ease;
    `;
    
    toast.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="color: var(--success-green); font-size: 1.5rem;">✓</span>
            <div>
                <div style="font-weight: 600; color: var(--accent-gold);">Added to Cart!</div>
                <div style="font-size: 0.875rem; color: var(--text-secondary);">${itemName}</div>
            </div>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (document.body.contains(toast)) {
                document.body.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// Animate cart icon
function animateCartIcon() {
    const cartIcon = document.querySelector('.cart-icon');
    if (cartIcon) {
        cartIcon.style.transform = 'scale(1.2)';
        setTimeout(() => {
            cartIcon.style.transform = 'scale(1)';
        }, 200);
    }
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);

// Initialize page when DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Update cart badge on page load
    updateCartBadge();
    
    // Add click event listeners to all add to cart buttons
    document.addEventListener('click', function(event) {
        if (event.target.matches('.add-to-cart-btn') || event.target.closest('.add-to-cart-btn')) {
            event.preventDefault();
            const button = event.target.matches('.add-to-cart-btn') ? 
                event.target : 
                event.target.closest('.add-to-cart-btn');
            const itemData = JSON.parse(button.getAttribute('data-item'));
            addToCart(itemData);
        }
    });

    // Animate menu items on load
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        setTimeout(() => {
            item.style.transition = 'all 0.4s ease';
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, index * 100);
    });
});