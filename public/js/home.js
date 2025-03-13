/**
 * JavaScript specific to the homepage
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize search functionality
    initSearchFunctionality();
    
    // Initialize job post form
    initJobPostForm();
    
    // Initialize counters animation
    initCountersAnimation();
    
    // Initialize testimonial slider
    initTestimonialSlider();
});

/**
 * Initialize search functionality
 */
function initSearchFunctionality() {
    const searchButton = document.querySelector('.search-button');
    
    if (searchButton) {
        searchButton.addEventListener('click', function() {
            const searchInput = document.querySelector('.search-input').value;
            const categorySelect = document.querySelector('.filter-select[name="category"]').value;
            const locationSelect = document.querySelector('.filter-select[name="location"]').value;
            
            // Redirect to search results page with parameters
            window.location.href = `find-jobs.php?q=${encodeURIComponent(searchInput)}&category=${encodeURIComponent(categorySelect)}&location=${encodeURIComponent(locationSelect)}`;
        });
    }
    
    // Advanced search form in modal
    const advancedSearchForm = document.getElementById('advanced-search-form');
    
    if (advancedSearchForm) {
        advancedSearchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get all form inputs and build query string
            const formData = new FormData(advancedSearchForm);
            const params = new URLSearchParams(formData);
            
            // Redirect to search results page
            window.location.href = `find-jobs.php?${params.toString()}`;
        });
    }
}

/**
 * Initialize job post form submission
 */
function initJobPostForm() {
    const jobPostForm = document.getElementById('job-post-form');
    
    if (jobPostForm) {
        jobPostForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Check if user is logged in (this would be set in PHP)
            const isLoggedIn = document.body.getAttribute('data-logged-in') === 'true';
            
            if (!isLoggedIn) {
                showNotification('Please sign in to post a job', 'warning');
                
                // Redirect to login page after a short delay
                setTimeout(() => {
                    window.location.href = 'login.php?redirect=post-job.php';
                }, 2000);
                
                return;
            }
            
            // Here you would normally use AJAX to submit the form
            // For demonstration, we'll just show a success message
            
            const formData = new FormData(jobPostForm);
            
            // Simulated AJAX request
            setTimeout(() => {
                // Clear form
                jobPostForm.reset();
                
                // Show success message
                showNotification('Your job has been posted successfully!', 'success');
                
                // Close modal
                const modal = document.getElementById('post-job-modal');
                closeModal(modal);
            }, 1000);
        });
    }
}

/**
 * Animate counter numbers on scroll
 */
function initCountersAnimation() {
    const stats = document.querySelectorAll('.stat-number');
    
    if (stats.length) {
        // Set up intersection observer
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Start the animation when element is visible
                    const statElement = entry.target;
                    const finalValue = parseInt(statElement.getAttribute('data-count'), 10);
                    
                    animateCounter(statElement, 0, finalValue, 2000);
                    
                    // Unobserve after starting animation
                    observer.unobserve(statElement);
                }
            });
        }, { threshold: 0.5 });
        
        // Observe each stat number element
        stats.forEach(stat => {
            observer.observe(stat);
        });
    }
}

/**
 * Animate a counter from start to end
 * @param {HTMLElement} element - The element to update
 * @param {number} start - Starting value
 * @param {number} end - Ending value
 * @param {number} duration - Animation duration in milliseconds
 */
function animateCounter(element, start, end, duration) {
    let startTimestamp = null;
    
    // Format numbers with commas
    const formatNumber = (num) => {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    };
    
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const currentValue = Math.floor(progress * (end - start) + start);
        
        element.textContent = formatNumber(currentValue);
        
        if (progress < 1) {
            window.requestAnimationFrame(step);
        } else {
            element.textContent = formatNumber(end);
        }
    };
    
    window.requestAnimationFrame(step);
}

/**
 * Initialize testimonial slider
 */
function initTestimonialSlider() {
    const testimonials = document.querySelectorAll('.testimonial-card');
    let currentIndex = 0;
    
    if (testimonials.length <= 1) return;
    
    // Create navigation dots
    const dotsContainer = document.createElement('div');
    dotsContainer.className = 'testimonial-dots';
    
    testimonials.forEach((_, index) => {
        const dot = document.createElement('button');
        dot.className = 'testimonial-dot';
        dot.setAttribute('aria-label', `Testimonial ${index + 1}`);
        dot.addEventListener('click', () => {
            goToSlide(index);
        });
        dotsContainer.appendChild(dot);
    });
    
    // Add dots to the document
    const testimonialsSection = document.querySelector('.testimonials');
    if (testimonialsSection) {
        testimonialsSection.appendChild(dotsContainer);
    }
    
    // Show initial testimonial
    showTestimonial(currentIndex);
    
    // Set up automatic rotation
    const interval = setInterval(() => {
        nextTestimonial();
    }, 8000);
    
    // Navigation functions
    function showTestimonial(index) {
        testimonials.forEach((testimonial, i) => {
            testimonial.style.display = i === index ? 'block' : 'none';
        });
        
        // Update dots
        const dots = document.querySelectorAll('.testimonial-dot');
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });
    }
    
    function nextTestimonial() {
        currentIndex = (currentIndex + 1) % testimonials.length;
        showTestimonial(currentIndex);
    }
    
    function goToSlide(index) {
        currentIndex = index;
        showTestimonial(currentIndex);
        
        // Reset interval to prevent jumping
        clearInterval(interval);
        setInterval(() => {
            nextTestimonial();
        }, 8000);
    }
    
    // Add prev/next buttons
    const prevButton = document.createElement('button');
    prevButton.className = 'testimonial-nav testimonial-prev';
    prevButton.innerHTML = '&#10094;';
    prevButton.setAttribute('aria-label', 'Previous testimonial');
    
    const nextButton = document.createElement('button');
    nextButton.className = 'testimonial-nav testimonial-next';
    nextButton.innerHTML = '&#10095;';
    nextButton.setAttribute('aria-label', 'Next testimonial');
    
    prevButton.addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + testimonials.length) % testimonials.length;
        showTestimonial(currentIndex);
    });
    
    nextButton.addEventListener('click', () => {
        nextTestimonial();
    });
    
    if (testimonialsSection) {
        testimonialsSection.appendChild(prevButton);
        testimonialsSection.appendChild(nextButton);
    }
}
