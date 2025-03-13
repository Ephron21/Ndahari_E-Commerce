<footer class="site-footer">
    <div class="container">
        <div class="footer-top">
            <div class="footer-column">
            <link rel="stylesheet" href="public/css/footer.css">
                <h3>Ndahari</h3>
                <p>Connecting skilled individuals with businesses looking for part-time workers. Creating opportunities, one job at a time.</p>
                <div class="social-links">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            
            <div class="footer-column">
                <h3>For Job Seekers</h3>
                <ul>
                    <li><a href="find-jobs.php">Browse Jobs</a></li>
                    <li><a href="register.php?type=jobseeker">Create Account</a></li>
                    <li><a href="jobseeker-faq.php">FAQ</a></li>
                    <li><a href="success-stories.php">Success Stories</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h3>For Employers</h3>
                <ul>
                    <li><a href="post-job.php">Post a Job</a></li>
                    <li><a href="register.php?type=employer">Create Account</a></li>
                    <li><a href="employer-faq.php">FAQ</a></li>
                    <li><a href="pricing.php">Pricing Plans</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h3>Contact Us</h3>
                <address>
                    <p><i class="fas fa-map-marker-alt"></i> 123 Main Street, Kigali, Rwanda</p>
                    <p><i class="fas fa-phone"></i> <a href="tel:+2507878446344">+250 787846344</a></p>
                    <p><i class="fas fa-envelope"></i> <a href="mailto:info@ndahari.com">info@ndahari.com</a></p>
                </address>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="copyright">
                &copy; <?php echo date('Y'); ?> Ndahari. All Rights Reserved.
            </div>
            <div class="footer-links">
                <a href="terms.php">Terms of Service</a>
                <a href="privacy.php">Privacy Policy</a>
                <a href="accessibility.php">Accessibility</a>
            </div>
        </div>
    </div>
    
    <!-- Back to top button -->
    <a href="#" id="back-to-top" class="back-to-top" aria-label="Back to top">
        <i class="fas fa-arrow-up"></i>
    </a>
</footer>

<!-- Mobile Menu for Responsive Design -->
<div class="mobile-menu">
    <div class="mobile-menu-header">
        <div class="mobile-menu-close">
            <span>&times;</span>
        </div>
    </div>
    <nav class="mobile-nav">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="find-jobs.php">Find Jobs</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="contact.php">Contact</a></li>
            <?php if (isUserLoggedIn()): ?>
                <li class="has-dropdown">
                    <a href="#">My Account</a>
                    <ul class="mobile-dropdown">
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="profile.php">Profile</a></li>
                        <li><a href="messages.php">Messages</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a href="login.php">Sign In</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<!-- Notification container -->
<div class="notification-container"></div>

<script>
    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function() {
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
        
        // Mobile dropdown toggle
        const mobileDropdownItems = document.querySelectorAll('.mobile-nav .has-dropdown > a');
        
        mobileDropdownItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                this.parentNode.classList.toggle('active');
            });
        });
        
        // Back to top button
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
    });
</script>
