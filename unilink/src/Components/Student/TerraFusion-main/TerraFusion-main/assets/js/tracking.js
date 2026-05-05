// Order Tracking Page JavaScript - TerraFusion Restaurant Ordering System

document.addEventListener('DOMContentLoaded', function() {
    // Animate timeline items on load
    const timelineItems = document.querySelectorAll('.timeline-item');
    timelineItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateX(-20px)';
        setTimeout(() => {
            item.style.transition = 'all 0.4s ease';
            item.style.opacity = '1';
            item.style.transform = 'translateX(0)';
        }, index * 150);
    });
    
    // Animate progress line
    const progressLine = document.getElementById('timeline-progress');
    if (progressLine) {
        setTimeout(() => {
            progressLine.style.transition = 'height 1s ease';
        }, 500);
    }
    
    // Auto-refresh every 30 seconds (in production, use WebSocket or polling)
    setInterval(refreshOrderStatus, 30000);
});

function refreshOrderStatus() {
    const refreshBtn = document.querySelector('.refresh-btn');
    if (refreshBtn) {
        refreshBtn.style.transform = 'rotate(360deg)';
        setTimeout(() => {
            refreshBtn.style.transform = 'rotate(0deg)';
        }, 500);
    }
    
    // In production, this would fetch updated status from server
    // For now, just show a notification
    showNotification('Order status refreshed', 'success');
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 2rem;
        right: 2rem;
        background: linear-gradient(145deg, var(--card-bg) 0%, #1f1f1f 100%);
        border: 2px solid ${type === 'success' ? 'var(--success-green)' : 'var(--accent-gold)'};
        border-radius: 12px;
        padding: 1rem 1.5rem;
        color: var(--text-primary);
        z-index: 10000;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        animation: slideInRight 0.3s ease;
        max-width: 300px;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

