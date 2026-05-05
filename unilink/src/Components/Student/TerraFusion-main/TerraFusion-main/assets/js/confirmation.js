// Order Confirmation Page JavaScript - TerraFusion Restaurant Ordering System

document.addEventListener('DOMContentLoaded', function() {
    // Animate confirmation icon
    const confirmationIcon = document.querySelector('.confirmation-icon');
    if (confirmationIcon) {
        confirmationIcon.style.opacity = '0';
        confirmationIcon.style.transform = 'scale(0.5)';
        
        setTimeout(() => {
            confirmationIcon.style.transition = 'all 0.5s ease';
            confirmationIcon.style.opacity = '1';
            confirmationIcon.style.transform = 'scale(1)';
        }, 100);
    }
    
    // Animate cards
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.4s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 200 + (index * 100));
    });
    
    // Print order functionality
    setupPrintFunctionality();
});

// Setup print functionality
function setupPrintFunctionality() {
    // Add print button if needed
    const printBtn = document.createElement('button');
    printBtn.className = 'btn btn-secondary';
    printBtn.textContent = 'Print Order';
    printBtn.onclick = function() {
        window.print();
    };
    
    // You can add this button to the page if needed
    // document.querySelector('.confirmation-header').appendChild(printBtn);
}

// Print styles (would be in a separate print.css in production)
const printStyles = `
    @media print {
        body {
            background: white;
            color: black;
        }
        .btn {
            display: none;
        }
        .card {
            border: 1px solid #ccc;
            page-break-inside: avoid;
        }
    }
`;

// Add print styles to head
const styleSheet = document.createElement('style');
styleSheet.textContent = printStyles;
document.head.appendChild(styleSheet);

