// About Page JavaScript - Ndahari E-commerce

document.addEventListener('DOMContentLoaded', function() {
    // Initialize animations
    initAnimations();
    
    // Initialize counters
    initCounters();
    
    // Initialize sliders
    initValuesSlider();
    initTestimonialSlider();
    
    // Initialize FAQ accordions
    initFaqAccordions();
    
    // Initialize parallax effect
    initParallax();
    
    // Initialize form handling
    initNewsletterForm();
});

// ===== Animation Functions =====
function initAnimations() {
    // Function to check if element is in viewport
    function isInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top <= (window.innerHeight || document.documentElement.clientHeight) * 0.85 &&
            rect.bottom >= 0
        );
    }
    
    // Elements to animate
    const animatedElements = document.querySelectorAll('.animate-fade-in, .animate-from-left, .animate-from-right');
    
    // Initial check for elements in viewport
    animatedElements.forEach(element => {
        if (isInViewport(element)) {
            element.classList.add('visible');
        }
    });
    
    // Listen for scroll to animate elements
    window.addEventListener('scroll', function() {
        animatedElements.forEach(element => {
            if (isInViewport(element) && !element.classList.contains('visible')) {
                element.classList.add('visible');
            }
        });
    });
}

// ===== Counter Animation =====
function initCounters() {
    const counters = document.querySelectorAll('.stat-number');
    
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-count'));
        let count = 0;
        const duration = 2000; // 2 seconds
        const frameDuration = 1000 / 60; // 60fps
        const totalFrames = Math.round(duration / frameDuration);
        const increment = target / totalFrames;
        
        function updateCount() {
            count += increment;
            if (count < target) {
                counter.textContent = Math.ceil(count).toLocaleString();
                requestAnimationFrame(updateCount);
            } else {
                counter.textContent = target.toLocaleString();
            }
        }
        
        // Start counter when element is in viewport
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    updateCount();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        observer.observe(counter);
    });
}

// ===== Values Slider =====
function initValuesSlider() {
    const track = document.querySelector('.values-track');
    const cards = document.querySelectorAll('.value-card');
    const prevBtn = document.querySelector('.values-slider .prev');
    const nextBtn = document.querySelector('.values-slider .next');
    const dotsContainer = document.querySelector('.values-slider .slider-dots');
    
    if (!track || !cards.length) return;
    
    let cardWidth = cards[0].offsetWidth + 30; // card width + margin
    let currentIndex = 0;
    const visibleCards = window.innerWidth < 768 ? 1 : window.innerWidth < 992 ? 2 : 3;
    const maxIndex = Math.max(0, cards.length - visibleCards);
    
    // Create dots
    for (let i = 0; i <= maxIndex; i++) {
        const dot = document.createElement('div');
        dot.classList.add('dot');
        if (i === 0) dot.classList.add('active');
        dot.addEventListener('click', () => goToSlide(i));
        dotsContainer.appendChild(dot);
    }
    
    // Update slide position
    function updateSlidePosition() {
        track.style.transform = `translateX(-${currentIndex * cardWidth}px)`;
        
        // Update dots
        const dots = document.querySelectorAll('.values-slider .dot');
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentIndex);
        });
    }
    
    // Go to specific slide
    function goToSlide(index) {
        currentIndex = Math.max(0, Math.min(index, maxIndex));
        updateSlidePosition();
    }
    
    // Previous slide
    function prevSlide() {
        goToSlide(currentIndex - 1);
    }
    
    // Next slide
    function nextSlide() {
        goToSlide(currentIndex + 1);
    }
    
    // Event listeners
    if (prevBtn) prevBtn.addEventListener('click', prevSlide);
    if (nextBtn) nextBtn.addEventListener('click', nextSlide);
    
    // Handle resize
    window.addEventListener('resize', () => {
        cardWidth = cards[0].offsetWidth + 30;
        updateSlidePosition();
    });
    
    // Auto slide every 5 seconds
    setInterval(nextSlide, 5000);
}

// ===== Testimonial Slider =====
function initTestimonialSlider() {
    const track = document.querySelector('.testimonial-track');
    const items = document.querySelectorAll('.testimonial-item');
    const prevBtn = document.querySelector('.testimonial-controls .prev');
    const nextBtn = document.querySelector('.testimonial-controls .next');
    const dotsContainer = document.querySelector('.testimonial-controls .slider-dots');
    
    if (!track || !items.length) return;
    
    let currentIndex = 0;
    const maxIndex = items.length - 1;
    
    // Create dots
    for (let i = 0; i <= maxIndex; i++) {
        const dot = document.createElement('div');
        dot.classList.add('dot');
        if (i === 0) dot.classList.add('active');
        dot.addEventListener('click', () => goToSlide(i));
        dotsContainer.appendChild(dot);
    }
    
    // Update slide position
    function updateSlidePosition() {
        track.style.transform = `translateX(-${currentIndex * 100}%)`;
        
        // Update dots
        const dots = document.querySelectorAll('.testimonial-controls .dot');
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentIndex);
        });
    }
    
    // Go to specific slide
    function goToSlide(index) {
        currentIndex = Math.max(0, Math.min(index, maxIndex));
        updateSlidePosition();
    }
    
    // Previous slide
    function prevSlide() {
        goToSlide(currentIndex - 1);
    }
    
    // Next slide
    function nextSlide() {
        goToSlide(currentIndex + 1);
    }
    
    // Event listeners
    if (prevBtn) prevBtn.addEventListener('click', prevSlide);
    if (nextBtn) nextBtn.addEventListener('click', nextSlide);
    
    // Auto slide every 7 seconds
    setInterval(nextSlide, 7000);
}

// ===== FAQ Accordions =====
function initFaqAccordions() {
    const accordionItems = document.querySelectorAll('.faq-item');
    
    accordionItems.forEach(item => {
        const header = item.querySelector('.faq-question');
        const content = item.querySelector('.faq-answer');
        
        if (!header || !content) return;
        
        header.addEventListener('click', () => {
            // Close all other accordions
            accordionItems.forEach(otherItem => {
                if (otherItem !== item && otherItem.classList.contains('active')) {
                    otherItem.classList.remove('active');
                    otherItem.querySelector('.faq-answer').style.maxHeight = '0';
                }
            });
            
            // Toggle current accordion
            const isActive = item.classList.contains('active');
            item.classList.toggle('active');
            
            if (isActive) {
                content.style.maxHeight = '0';
            } else {
                content.style.maxHeight = content.scrollHeight + 'px';
            }
        });
    });
}

// ===== Parallax Effect =====
function initParallax() {
    const parallaxElements = document.querySelectorAll('.parallax-bg');
    
    function updateParallax() {
        parallaxElements.forEach(element => {
            const speed = element.getAttribute('data-speed') || 0.5;
            const yPos = -(window.scrollY * speed);
            element.style.transform = `translateY(${yPos}px)`;
        });
    }
    
    window.addEventListener('scroll', updateParallax);
    updateParallax(); // Initialize positions
}

// ===== Newsletter Form =====
function initNewsletterForm() {
    const form = document.querySelector('.newsletter-form');
    
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const emailInput = form.querySelector('input[type="email"]');
        const submitBtn = form.querySelector('button[type="submit"]');
        const statusMessage = form.querySelector('.form-status') || document.createElement('div');
        
        if (!statusMessage.classList.contains('form-status')) {
            statusMessage.classList.add('form-status');
            form.appendChild(statusMessage);
        }
        
        if (!emailInput || !emailInput.value.trim()) {
            statusMessage.textContent = 'Please enter a valid email address.';
            statusMessage.classList.add('error');
            return;
        }
        
        // Disable form elements during submission
        emailInput.disabled = true;
        submitBtn.disabled = true;
        statusMessage.textContent = 'Submitting...';
        statusMessage.classList.remove('error');
        statusMessage.classList.add('pending');
        
        // Simulate API call
        setTimeout(() => {
            // Success
            statusMessage.textContent = 'Thank you! You are now subscribed to our newsletter.';
            statusMessage.classList.remove('pending', 'error');
            statusMessage.classList.add('success');
            form.reset();
            
            // Re-enable form after 3 seconds
            setTimeout(() => {
                emailInput.disabled = false;
                submitBtn.disabled = false;
            }, 3000);
        }, 1500);
        
        // For real implementation, replace the above with actual API call:
        /*
        fetch('/api/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email: emailInput.value.trim() })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                statusMessage.textContent = 'Thank you! You are now subscribed to our newsletter.';
                statusMessage.classList.remove('pending', 'error');
                statusMessage.classList.add('success');
                form.reset();
            } else {
                throw new Error(data.message || 'Failed to subscribe. Please try again.');
            }
        })
        .catch(error => {
            statusMessage.textContent = error.message;
            statusMessage.classList.remove('pending');
            statusMessage.classList.add('error');
        })
        .finally(() => {
            // Re-enable form
            emailInput.disabled = false;
            submitBtn.disabled = false;
        });
        */
    });
}