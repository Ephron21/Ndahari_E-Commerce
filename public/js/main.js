

// Initialize AOS (Animate on Scroll) library
document.addEventListener('DOMContentLoaded', function() {
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true
    });

    // Initialize modals
    initModals();

    // Initialize smooth scrolling for all anchor links
    initSmoothScrolling();
});

/**
 * Initialize modal functionality
 */
function initModals() {
    // Get all modal triggers
    const modalTriggers = document.querySelectorAll('[data-modal]');
    
    // Add click event to each trigger
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.getAttribute('data-modal');
            const modal = document.getElementById(modalId);
            if (modal) {
                openModal(modal);
            }
        });
    });

    // Get all modal close buttons
    const closeButtons = document.querySelectorAll('.modal-close');
    
    // Add click event to each close button
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            closeModal(modal);
        });
    });

    // Close modal when clicking outside of modal content
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            closeModal(e.target);
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModals = document.querySelectorAll('.modal[style*="display: block"]');
            openModals.forEach(modal => {
                closeModal(modal);
            });
        }
    });
}

/**
 * Open a modal
 * @param {HTMLElement} modal - The modal element to open
 */
function openModal(modal) {
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden'; // Prevent scrolling behind modal
}

/**
 * Close a modal
 * @param {HTMLElement} modal - The modal element to close
 */
function closeModal(modal) {
    modal.style.display = 'none';
    document.body.style.overflow = ''; // Restore scrolling
}

/**
 * Initialize smooth scrolling for anchor links
 */
function initSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Skip if it's a modal trigger
            if (this.hasAttribute('data-modal')) {
                return;
            }
            
            // Skip if href is just "#"
            if (href === '#') {
                return;
            }

            e.preventDefault();
            
            const targetElement = document.querySelector(href);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
}

/**
 * Show a notification toast
 * @param {string} message - The message to display
 * @param {string} type - The type of notification (success, error, info, warning)
 * @param {number} duration - How long to show the notification in milliseconds
 */
function showNotification(message, type = 'info', duration = 5000) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span>${message}</span>
            <button class="notification-close">&times;</button>
        </div>
    `;
    
    // Add notification to the container (create if it doesn't exist)
    let container = document.querySelector('.notification-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'notification-container';
        document.body.appendChild(container);
    }
    
    container.appendChild(notification);
    
    // Add close button functionality
    const closeButton = notification.querySelector('.notification-close');
    closeButton.addEventListener('click', function() {
        container.removeChild(notification);
    });
    
    // Auto-remove after duration
    setTimeout(() => {
        if (notification.parentNode === container) {
            container.removeChild(notification);
        }
    }, duration);
}
