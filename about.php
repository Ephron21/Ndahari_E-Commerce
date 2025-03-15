<?php
session_start();
require_once 'includes/header.php';
?>
<link rel="stylesheet" href="public/css/about.css">
<main class="about-page">
    <!-- Hero Section with Parallax -->
    <section class="hero-section">
        <div class="parallax-bg"></div>
        <div class="container">
            <div class="hero-content animate-fade-in">
                <h1>About Ndahari</h1>
                <p class="subtitle">Connecting Communities Through Commerce</p>
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number" data-count="15000">0</span>
                        <span class="stat-label">Products</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" data-count="8500">0</span>
                        <span class="stat-label">Happy Customers</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" data-count="120">0</span>
                        <span class="stat-label">Vendors</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Mission Section with Video -->
    <section class="our-mission">
        <div class="container">
            <div class="section-grid">
                <div class="grid-content animate-from-left">
                    <h2>Our Mission</h2>
                    <p class="lead-text">Transforming how people discover, shop, and connect.</p>
                    <p>At Ndahari, we're dedicated to creating an exceptional e-commerce ecosystem that empowers both shoppers and sellers. Our platform serves as a marketplace that celebrates quality products while making shopping convenient, personalized, and enjoyable.</p>
                    <p>We believe in sustainable commerce that benefits communities and creates economic opportunities across Rwanda and beyond.</p>
                </div>
                <div class="grid-media animate-from-right">
                    <div class="video-container">
                        <iframe src="https://www.youtube.com/embed/your-video-id" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="our-values">
        <div class="container">
            <h2 class="text-center animate-fade-in">Our Core Values</h2>
            <div class="values-slider">
                <div class="values-track">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h3>Customer First</h3>
                        <p>Every decision we make starts with our customers' needs and experience in mind.</p>
                    </div>
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-gem"></i>
                        </div>
                        <h3>Quality Assurance</h3>
                        <p>We ensure every product on our platform meets rigorous quality standards.</p>
                    </div>
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h3>Community Impact</h3>
                        <p>We build meaningful relationships with local communities and support economic growth.</p>
                    </div>
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h3>Trust & Security</h3>
                        <p>We create a safe marketplace where users can shop with confidence and peace of mind.</p>
                    </div>
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <h3>Innovation</h3>
                        <p>We constantly explore new ideas to enhance how people discover and shop for products.</p>
                    </div>
                </div>
                <div class="slider-controls">
                    <button class="slider-arrow prev" aria-label="Previous slide"><i class="fas fa-chevron-left"></i></button>
                    <div class="slider-dots"></div>
                    <button class="slider-arrow next" aria-label="Next slide"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us with Interactive Cards -->
    <section class="why-choose-us">
        <div class="container">
            <h2 class="text-center animate-fade-in">Why Choose Ndahari</h2>
            <div class="features-grid">
                <div class="feature-card hover-scale">
                    <div class="feature-icon">
                        <i class="fas fa-truck-fast"></i>
                    </div>
                    <h3>Fast Delivery</h3>
                    <p>We partner with reliable logistics providers to ensure your orders reach you quickly and safely.</p>
                    <div class="card-hover-content">
                        <p>Most orders delivered within 24-48 hours in Kigali and 3-5 days nationwide.</p>
                    </div>
                </div>
                <div class="feature-card hover-scale">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Secure Shopping</h3>
                    <p>Shop with confidence knowing that your personal information and payments are highly protected.</p>
                    <div class="card-hover-content">
                        <p>End-to-end encryption and fraud detection systems to keep your data safe.</p>
                    </div>
                </div>
                <div class="feature-card hover-scale">
                    <div class="feature-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h3>Best Prices</h3>
                    <p>Discover competitive prices and exclusive deals you won't find elsewhere.</p>
                    <div class="card-hover-content">
                        <p>Price match guarantee and regular flash sales to maximize your savings.</p>
                    </div>
                </div>
                <div class="feature-card hover-scale">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>24/7 Support</h3>
                    <p>Our dedicated customer support team is always available to assist you with any questions.</p>
                    <div class="card-hover-content">
                        <p>Reach us via chat, email, or phone any day, any time.</p>
                    </div>
                </div>
                <div class="feature-card hover-scale">
                    <div class="feature-icon">
                        <i class="fas fa-rotate-left"></i>
                    </div>
                    <h3>Easy Returns</h3>
                    <p>Not satisfied with your purchase? Our hassle-free return policy has you covered.</p>
                    <div class="card-hover-content">
                        <p>30-day return policy with free return shipping on most items.</p>
                    </div>
                </div>
                <div class="feature-card hover-scale">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Community Focused</h3>
                    <p>Join a growing community of shoppers and sellers committed to quality and authenticity.</p>
                    <div class="card-hover-content">
                        <p>Regular community events and seller spotlights to strengthen our ecosystem.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Story Section with Timeline -->
    <section class="our-story">
        <div class="container">
            <h2 class="text-center animate-fade-in">Our Journey</h2>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-date">2018</div>
                    <div class="timeline-content">
                        <h3>The Beginning</h3>
                        <p>Ndahari was founded with a vision to create a modern e-commerce platform tailored for Rwanda.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-date">2019</div>
                    <div class="timeline-content">
                        <h3>Market Launch</h3>
                        <p>We officially launched our marketplace with 20 trusted sellers and 500 products.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-date">2020</div>
                    <div class="timeline-content">
                        <h3>Growth & Expansion</h3>
                        <p>Expanded our operations to reach customers nationwide, partnering with local delivery services.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-date">2021</div>
                    <div class="timeline-content">
                        <h3>Mobile App Launch</h3>
                        <p>Introduced our mobile application to make shopping on-the-go even easier for our customers.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-date">2022</div>
                    <div class="timeline-content">
                        <h3>Partnerships & Integration</h3>
                        <p>Formed strategic partnerships with financial institutions to offer secure payment options.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-date">2023</div>
                    <div class="timeline-content">
                        <h3>Today & Beyond</h3>
                        <p>Continuing our mission to revolutionize e-commerce in Rwanda and planning regional expansion.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="our-team">
        <div class="container">
            <h2 class="text-center animate-fade-in">Meet Our Team</h2>
            <p class="text-center">The passionate individuals behind Ndahari's success</p>
            
            <div class="team-carousel">
                <div class="team-member">
                    <div class="member-image">
                        <img src="images/team/team1.jpg" alt="Team Member" onerror="this.onerror=null; this.src='images/placeholder-team.jpg';">
                    </div>
                    <div class="member-info">
                        <h3>John Doe</h3>
                        <p class="member-title">Founder & CEO</p>
                        <div class="member-social">
                            <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                            <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
                <div class="team-member">
                    <div class="member-image">
                        <img src="images/team/team2.jpg" alt="Team Member" onerror="this.onerror=null; this.src='images/placeholder-team.jpg';">
                    </div>
                    <div class="member-info">
                        <h3>Jane Smith</h3>
                        <p class="member-title">Chief Operations Officer</p>
                        <div class="member-social">
                            <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                            <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
                <div class="team-member">
                    <div class="member-image">
                        <img src="images/team/team3.jpg" alt="Team Member" onerror="this.onerror=null; this.src='images/placeholder-team.jpg';">
                    </div>
                    <div class="member-info">
                        <h3>Robert Johnson</h3>
                        <p class="member-title">Chief Technology Officer</p>
                        <div class="member-social">
                            <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                            <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
                <div class="team-member">
                    <div class="member-image">
                        <img src="images/team/team4.jpg" alt="Team Member" onerror="this.onerror=null; this.src='images/placeholder-team.jpg';">
                    </div>
                    <div class="member-info">
                        <h3>Sarah Williams</h3>
                        <p class="member-title">Head of Marketing</p>
                        <div class="member-social">
                            <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                            <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <div class="container">
            <h2 class="text-center animate-fade-in">What Our Customers Say</h2>
            <div class="testimonial-slider">
                <div class="testimonial-track">
                    <div class="testimonial-item">
                        <div class="testimonial-content">
                            <p>"Ndahari has transformed how I shop online. The product quality and delivery speed are exceptional, and their customer service is top-notch!"</p>
                        </div>
                        <div class="testimonial-author">
                            <img src="images/testimonials/client1.jpg" alt="Customer" onerror="this.onerror=null; this.src='images/placeholder-avatar.jpg';">
                            <div>
                                <h4>Marie Uwase</h4>
                                <p>Regular Customer</p>
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-item">
                        <div class="testimonial-content">
                            <p>"As a vendor, Ndahari has helped me reach customers I never would have found otherwise. Their platform is easy to use and the support team is always ready to help."</p>
                        </div>
                        <div class="testimonial-author">
                            <img src="images/testimonials/client2.jpg" alt="Customer" onerror="this.onerror=null; this.src='images/placeholder-avatar.jpg';">
                            <div>
                                <h4>Jean Claude Mutoni</h4>
                                <p>Vendor Partner</p>
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-item">
                        <div class="testimonial-content">
                            <p>"The variety of products available on Ndahari is impressive. I appreciate how they showcase local artisans and businesses, making it easy to support our community."</p>
                        </div>
                        <div class="testimonial-author">
                            <img src="images/testimonials/client3.jpg" alt="Customer" onerror="this.onerror=null; this.src='images/placeholder-avatar.jpg';">
                            <div>
                                <h4>Patricia Ingabire</h4>
                                <p>Loyal Customer</p>
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="slider-controls testimonial-controls">
                    <button class="slider-arrow prev" aria-label="Previous testimonial"><i class="fas fa-chevron-left"></i></button>
                    <div class="slider-dots"></div>
                    <button class="slider-arrow next" aria-label="Next testimonial"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </section>

    <!-- Join Us Section with Parallax -->
    <section class="join-us">
        <div class="container">
            <div class="join-content animate-fade-in">
                <h2>Join Our Ndahari Community</h2>
                <p class="lead-text">Whether you're a shopper looking for quality products or a seller wanting to expand your reach, Ndahari is here to connect you.</p>
                <div class="cta-buttons">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="register.php" class="btn btn-primary pulse">Create Account</a>
                        <a href="shop.php" class="btn btn-secondary">Start Shopping</a>
                        <a href="become-seller.php" class="btn btn-outline">Become a Seller</a>
                    <?php else: ?>
                        <a href="dashboard.php" class="btn btn-primary">My Account</a>
                        <a href="shop.php" class="btn btn-secondary">Discover Products</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section with Accordion -->
    <section class="faq-section">
        <div class="container">
            <h2 class="text-center animate-fade-in">Frequently Asked Questions</h2>
            <div class="faq-container">
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>How do I place an order on Ndahari?</h3>
                        <span class="faq-icon"><i class="fas fa-plus"></i></span>
                    </div>
                    <div class="faq-answer">
                        <p>Placing an order is simple! Browse our products, add items to your cart, and proceed to checkout. You can pay using mobile money, credit/debit cards, or choose cash on delivery for eligible areas.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>What are the delivery options and timeframes?</h3>
                        <span class="faq-icon"><i class="fas fa-plus"></i></span>
                    </div>
                    <div class="faq-answer">
                        <p>We offer standard delivery (2-5 business days) and express delivery (24-48 hours) in major cities. Delivery times may vary based on your location and product availability. You can track your order in real-time through your account dashboard.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>How can I become a seller on Ndahari?</h3>
                        <span class="faq-icon"><i class="fas fa-plus"></i></span>
                    </div>
                    <div class="faq-answer">
                        <p>To become a seller, create an account and select the "Become a Seller" option. Complete your business profile, submit required documentation for verification, and once approved, you can start listing your products on our marketplace.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>What is your return and refund policy?</h3>
                        <span class="faq-icon"><i class="fas fa-plus"></i></span>
                    </div>
                    <div class="faq-answer">
                        <p>We offer a 30-day return policy for most items. Products must be unused and in original packaging. Once your return is received and inspected, we'll process your refund to the original payment method within 5-7 business days.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>How do I contact customer support?</h3>
                        <span class="faq-icon"><i class="fas fa-plus"></i></span>
                    </div>
                    <div class="faq-answer">
                        <p>Our customer support team is available 24/7. You can reach us through live chat on our website, email at support@ndahari.com, or call our customer service line at +250 78XX XXXX. We typically respond to inquiries within 1-2 hours.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-content">
                <h2>Stay Updated</h2>
                <p>Subscribe to our newsletter for exclusive deals, new arrivals, and marketplace insights.</p>
                <form id="newsletterForm" class="newsletter-form" action="subscribe.php" method="POST">
                    <div class="form-group">
                        <input type="email" name="email" id="newsletter-email" placeholder="Your email address" required>
                        <button type="submit" class="btn-submit">Subscribe</button>
                    </div>
                    <div class="form-feedback" id="newsletter-feedback"></div>
                </form>
            </div>
        </div>
    </section>
</main>

<!-- Include the Footer -->
<?php
require_once 'includes/footer.php';
?>

<!-- Custom Scripts -->
<link rel="stylesheet" href="public/css/about.css">
<script src="public/js/about.js" defer></script>
