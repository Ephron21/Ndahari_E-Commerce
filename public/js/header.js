// header.js
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const header = document.querySelector('.site-header');
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mainNav = document.querySelector('.main-nav');
    const dropdowns = document.querySelectorAll('.dropdown');
    const logo = document.querySelector('.logo');
    
    // Add initial animation to logo
    logo.classList.add('fade-in');
    
    // Animate nav items with staggered delay
    const navItems = document.querySelectorAll('.nav-menu > li');
    navItems.forEach((item, index) => {
        setTimeout(() => {
            item.classList.add('fade-in');
        }, 100 * (index + 1));
    });
    
    // Scroll effect for header
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
    
    // Mobile menu toggle
    mobileMenuToggle.addEventListener('click', function() {
        this.classList.toggle('active');
        mainNav.classList.toggle('active');
        
        // Animate burger icon
        const spans = this.querySelectorAll('span');
        if (this.classList.contains('active')) {
            spans[0].style.transform = 'rotate(45deg) translate(5px, 6px)';
            spans[1].style.opacity = '0';
            spans[2].style.transform = 'rotate(-45deg) translate(5px, -6px)';
        } else {
            spans[0].style.transform = 'none';
            spans[1].style.opacity = '1';
            spans[2].style.transform = 'none';
        }
    });
    
    // Handle dropdowns on mobile
    if (window.innerWidth <= 768) {
        dropdowns.forEach(dropdown => {
            const dropdownToggle = dropdown.querySelector('.dropdown-toggle');
            dropdownToggle.addEventListener('click', function(e) {
                e.preventDefault();
                dropdown.classList.toggle('active');
            });
        });
    }
    
    // Notification dot for messages (if unread messages exist)
    const addNotificationBadge = () => {
        if (document.body.getAttribute('data-logged-in') === 'true') {
            const messageLink = document.querySelector('a[href="messages.php"]');
            if (messageLink) {
                // This would normally check with the server for unread messages
                // For demo purposes, we'll randomly add a notification badge
                if (Math.random() > 0.5) {
                    const badge = document.createElement('span');
                    badge.className = 'notification-badge pulse';
                    badge.style.position = 'absolute';
                    badge.style.top = '0';
                    badge.style.right = '0';
                    badge.style.width = '8px';
                    badge.style.height = '8px';
                    badge.style.borderRadius = '50%';
                    badge.style.backgroundColor = '#ff4757';
                    messageLink.style.position = 'relative';
                    messageLink.appendChild(badge);
                }
            }
        }
    };
    addNotificationBadge();
    
    // Interactive hover effects
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('mouseover', function() {
            this.style.transform = 'translateY(-3px)';
        });
        button.addEventListener('mouseout', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Change header background on hover (subtle effect)
    let originalHeaderColor = getComputedStyle(header).backgroundColor;
    header.addEventListener('mouseover', function() {
        this.style.transition = 'background-color 0.3s ease';
        this.style.backgroundColor = 'rgba(255,255,255,0.95)';
    });
    header.addEventListener('mouseout', function() {
        this.style.backgroundColor = originalHeaderColor;
    });
    
    // Easter egg - logo shake on multiple clicks
    let logoClicks = 0;
    logo.addEventListener('click', function(e) {
        logoClicks++;
        if (logoClicks >= 3) {
            e.preventDefault();
            logo.classList.add('shake');
            setTimeout(() => {
                logo.classList.remove('shake');
                logoClicks = 0;
            }, 1000);
        }
    });
    
    // Highlight active page in navigation
    const highlightActivePage = () => {
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.nav-menu > li > a');
        
        navLinks.forEach(link => {
            const linkPath = link.getAttribute('href');
            if (currentPath.includes(linkPath) && linkPath !== 'index.php') {
                link.style.color = '#0056b3';
                link.style.fontWeight = '600';
            }
            if (currentPath === '/' && linkPath === 'index.php') {
                link.style.color = '#0056b3';
                link.style.fontWeight = '600';
            }
        });
    };
    highlightActivePage();
    
    // Add subtle hover interaction to all nav items
    navItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});