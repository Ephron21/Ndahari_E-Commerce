document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu functionality (already defined in your code)
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    const mobileMenuClose = document.querySelector('.mobile-menu-close');
    
    if (mobileMenuToggle && mobileMenu && mobileMenuClose) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
        
        mobileMenuClose.addEventListener('click', function() {
            mobileMenu.classList.remove('active');
            document.body.style.overflow = '';
        });
    }
    
    // Mobile dropdown functionality (already defined in your code)
    const mobileDropdownItems = document.querySelectorAll('.mobile-nav .has-dropdown > a');
    
    mobileDropdownItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            this.parentNode.classList.toggle('active');
        });
    });
    
    // Back to top button (already defined in your code with enhancements)
    const backToTopButton = document.getElementById('back-to-top');
    
    if (backToTopButton) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });
        
        backToTopButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
    
    // Add typing text animation to the first paragraph in the footer
    const firstParagraph = document.querySelector('.footer-column:first-child p');
    if (firstParagraph) {
        const text = firstParagraph.textContent;
        firstParagraph.innerHTML = '';
        
        let typingElement = document.createElement('span');
        typingElement.classList.add('typing-text');
        firstParagraph.appendChild(typingElement);
        
        let i = 0;
        const typingSpeed = 50; // Milliseconds per character
        
        function typeWriter() {
            if (i < text.length) {
                typingElement.textContent += text.charAt(i);
                i++;
                setTimeout(typeWriter, typingSpeed);
            } else {
                // Remove the blinking cursor after typing is complete
                typingElement.classList.remove('typing-text');
            }
        }
        
        // Start typing animation after a short delay
        setTimeout(typeWriter, 1000);
    }
    
    // Add shine effect to headings
    const footerHeadings = document.querySelectorAll('.footer-column h3');
    footerHeadings.forEach(heading => {
        heading.classList.add('shine-effect');
    });
    
    // Create parallax background dots
    const footerElement = document.querySelector('.site-footer');
    if (footerElement) {
        const parallaxBg = document.createElement('div');
        parallaxBg.classList.add('parallax-bg');
        footerElement.appendChild(parallaxBg);
        
        // Create random dots
        for (let i = 0; i < 30; i++) {
            createParallaxDot(parallaxBg);
        }
        
        // Parallax effect on mouse move
        document.addEventListener('mousemove', function(e) {
            const dots = document.querySelectorAll('.parallax-dot');
            dots.forEach(dot => {
                const speed = parseFloat(dot.getAttribute('data-speed'));
                const x = (window.innerWidth - e.pageX * speed) / 100;
                const y = (window.innerHeight - e.pageY * speed) / 100;
                
                dot.style.transform = `translate(${x}px, ${y}px)`;
            });
        });
    }
    
    // Create testimonial slider
    createTestimonialSlider();
    
    // Show email subscription popup after delay
    setTimeout(showEmailSubscription, 5000);
    
    // Add notification system
    setupNotifications();
    
    // Add hover animations for social icons
    addSocialIconsAnimation();
});

// Function to create parallax dots
function createParallaxDot(container) {
    const dot = document.createElement('div');
    dot.classList.add('parallax-dot');
    
    // Random properties
    const size = Math.random() * 50 + 10;
    const posX = Math.random() * 100;
    const posY = Math.random() * 100;
    const speed = Math.random() * 0.5;
    
    dot.style.width = `${size}px`;
    dot.style.height = `${size}px`;
    dot.style.left = `${posX}%`;
    dot.style.top = `${posY}%`;
    dot.setAttribute('data-speed', speed);
    
    container.appendChild(dot);
}

// Function to create testimonial slider
function createTestimonialSlider() {
    // Check if footer exists and doesn't already have a testimonial slider
    const footer = document.querySelector('.site-footer');
    if (!footer || document.querySelector('.testimonial-slider')) return;
    
    // Create testimonial slider section
    const sliderSection = document.createElement('div');
    sliderSection.classList.add('testimonial-slider');
    
    // Add container
    const container = document.createElement('div');
    container.classList.add('container');
    sliderSection.appendChild(container);
    
    // Add testimonials wrapper
    const wrapper = document.createElement('div');
    wrapper.classList.add('testimonials-wrapper');
    container.appendChild(wrapper);
    
    // Sample testimonials
    const testimonials = [
        {
            content: "Ndahari helped me find the perfect part-time job that fits my schedule. The platform is intuitive and connects you with quality employers!",
            author: "Jean Pierre, Web Developer"
        },
        {
            content: "As a business owner, Ndahari has been instrumental in helping us find reliable part-time staff quickly. Highly recommended!",
            author: "Marie Claire, Restaurant Owner"
        },
        {
            content: "The opportunities I've found through Ndahari have allowed me to support my family while continuing my education. Thank you!",
            author: "Emmanuel, Student & IT Support"
        }
    ];
    
    // Add testimonials to wrapper
    testimonials.forEach(testimonial => {
        const testimonialDiv = document.createElement('div');
        testimonialDiv.classList.add('testimonial');
        
        const contentP = document.createElement('p');
        contentP.classList.add('testimonial-content');
        contentP.textContent = testimonial.content;
        
        const authorP = document.createElement('p');
        authorP.classList.add('testimonial-author');
        authorP.textContent = testimonial.author;
        
        testimonialDiv.appendChild(contentP);
        testimonialDiv.appendChild(authorP);
        wrapper.appendChild(testimonialDiv);
    });
    
    // Add slider controls
    const controls = document.createElement('div');
    controls.classList.add('slider-controls');
    
    testimonials.forEach((_, index) => {
        const dot = document.createElement('div');
        dot.classList.add('slider-dot');
        if (index === 0) dot.classList.add('active');
        dot.setAttribute('data-index', index);
        dot.addEventListener('click', function() {
            changeSlide(index);
        });
        controls.appendChild(dot);
    });
    
    container.appendChild(controls);
    
    // Insert before the footer
    footer.parentNode.insertBefore(sliderSection, footer);
    
    // Auto-slide functionality
    let currentSlide = 0;
    const totalSlides = testimonials.length;
    
    function changeSlide(index) {
        currentSlide = index;
        wrapper.style.transform = `translateX(-${currentSlide * 100}%)`;
        
        // Update active dot
        document.querySelectorAll('.slider-dot').forEach((dot, i) => {
            if (i === currentSlide) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }
    
    // Auto-rotate slides
    setInterval(() => {
        currentSlide = (currentSlide + 1) % totalSlides;
        changeSlide(currentSlide);
    }, 5000);
}

// Function to show email subscription popup
function showEmailSubscription() {
    // Check if subscription element already exists
    if (document.querySelector('.email-subscribe')) return;
    
    // Create subscription element
    const subscribeDiv = document.createElement('div');
    subscribeDiv.classList.add('email-subscribe');
    
    subscribeDiv.innerHTML = `
        <span class="close-subscription">&times;</span>
        <h4>Stay Updated!</h4>
        <p>Subscribe to receive job alerts and career tips.</p>
        <form class="email-form">
            <input type="email" placeholder="Your email address" required>
            <button type="submit">Subscribe</button>
        </form>
    `;
    
    document.body.appendChild(subscribeDiv);
    
    // Show with animation after a small delay
    setTimeout(() => {
        subscribeDiv.classList.add('show');
    }, 200);
    
    // Close button functionality
    const closeButton = subscribeDiv.querySelector('.close-subscription');
    closeButton.addEventListener('click', () => {
        subscribeDiv.classList.remove('show');
        setTimeout(() => {
            subscribeDiv.remove();
        }, 400);
    });
    
    // Form submission
    const form = subscribeDiv.querySelector('form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get email value
        const email = this.querySelector('input[type="email"]').value;
        
        // Here you would normally send this to your server
        // For now, just show a success notification
        showNotification('Successfully subscribed! Thank you.', 'success');
        
        // Close subscription form
        subscribeDiv.classList.remove('show');
        setTimeout(() => {
            subscribeDiv.remove();
        }, 400);
    });
}

// Function to setup notification system
function setupNotifications() {
    // Check if notification container exists
    const container = document.querySelector('.notification-container');
    if (!container) return;
    
    // Global function to show notifications
    window.showNotification = function(message, type = 'info') {
        const notification = document.createElement('div');
        notification.classList.add('notification', type);
        notification.textContent = message;
        
        container.appendChild(notification);
        
        // Remove notification after delay
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.4s forwards';
            setTimeout(() => {
                notification.remove();
            }, 400);
        }, 4000);
    };
    
    // Demo notification
    setTimeout(() => {
        showNotification('Welcome to Ndahari! Explore our available jobs.', 'info');
    }, 2000);
}

// Function to add social icons animation
function addSocialIconsAnimation() {
    const socialLinks = document.querySelectorAll('.social-links a');
    
    socialLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            const icon = this.querySelector('i');
            icon.style.animation = 'iconRotate 0.6s ease';
        });
        
        link.addEventListener('mouseleave', function() {
            const icon = this.querySelector('i');
            icon.style.animation = '';
        });
    });
}

// Function to add animated counter for statistics
function addAnimatedCounters() {
    // Create statistics section
    const footer = document.querySelector('.site-footer');
    if (!footer) return;
    
    const statsSection = document.createElement('div');
    statsSection.classList.add('stats-section');
    
    const container = document.createElement('div');
    container.classList.add('container');
    statsSection.appendChild(container);
    
    const statsWrapper = document.createElement('div');
    statsWrapper.classList.add('stats-wrapper');
    container.appendChild(statsWrapper);
    
    // Sample stats
    const stats = [
        { label: 'Jobs Posted', value: 1500 },
        { label: 'Happy Clients', value: 870 },
        { label: 'Job Seekers', value: 3200 },
        { label: 'Success Stories', value: 420 }
    ];
    
    stats.forEach(stat => {
        const statDiv = document.createElement('div');
        statDiv.classList.add('stat-item');
        
        const counterDiv = document.createElement('div');
        counterDiv.classList.add('counter');
        counterDiv.setAttribute('data-target', stat.value);
        counterDiv.textContent = '0';
        
        const labelDiv = document.createElement('div');
        labelDiv.classList.add('stat-label');
        labelDiv.textContent = stat.label;
        
        statDiv.appendChild(counterDiv);
        statDiv.appendChild(labelDiv);
        statsWrapper.appendChild(statDiv);
    });
    
    // Insert before footer
    footer.parentNode.insertBefore(statsSection, footer);
    
    // Animation for counters
    const counters = document.querySelectorAll('.counter');
    
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = parseInt(counter.getAttribute('data-target'));
                const duration = 2000; // 2 seconds
                const step = Math.ceil(target / (duration / 20)); // Update every 20ms
                
                let current = 0;
                const timer = setInterval(() => {
                    current += step;
                    if (current >= target) {
                        counter.textContent = target;
                        clearInterval(timer);
                    } else {
                        counter.textContent = current;
                    }
                }, 20);
                
                counterObserver.unobserve(counter);
            }
        });
    }, { threshold: 0.5 });
    
    counters.forEach(counter => {
        counterObserver.observe(counter);
    });
}

// Call the animated counters function
addAnimatedCounters();