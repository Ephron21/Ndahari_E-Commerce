<?php
session_start();
require_once 'includes/header.php';
?>

<main class="about-page">
    <section class="hero-section">
        <div class="container">
            <h1>About Ndahari</h1>
            <p class="subtitle">Connecting Talent with Part-Time Opportunities</p>
        </div>
    </section>

    <section class="our-mission">
        <div class="container">
            <h2>Our Mission</h2>
            <p>At Ndahari, we're dedicated to bridging the gap between talented individuals and meaningful part-time opportunities. Our platform serves as a catalyst for connecting job seekers with employers, creating valuable partnerships that benefit both parties.</p>
        </div>
    </section>

    <section class="why-choose-us">
        <div class="container">
            <h2>Why Choose Ndahari</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Trusted Platform</h3>
                    <p>We verify all employers and job listings to ensure a safe and reliable job-seeking experience.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Easy Job Search</h3>
                    <p>Our intuitive platform makes it simple to find the perfect part-time opportunity that matches your skills and schedule.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Community Focused</h3>
                    <p>Join a growing community of professionals and businesses committed to mutual growth and success.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="our-story">
        <div class="container">
            <h2>Our Story</h2>
            <div class="story-content">
                <div class="story-text">
                    <p>Founded with a vision to revolutionize the part-time job market, Ndahari has grown into a trusted platform that serves thousands of users. We understand the unique challenges of finding flexible work opportunities and have built our platform to address these needs.</p>
                    <p>Our team is passionate about creating meaningful connections and supporting both job seekers and employers in achieving their goals.</p>
                </div>
                <div class="story-image">
                    <img src="images/about-story.jpg" alt="Ndahari Story" onerror="this.onerror=null; this.src='images/placeholder-about.jpg';">
                </div>
            </div>
        </div>
    </section>

    <section class="join-us">
        <div class="container">
            <h2>Join Our Community</h2>
            <p>Whether you're looking for your next opportunity or seeking talented individuals for your business, Ndahari is here to help.</p>
            <div class="cta-buttons">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="register.php" class="btn btn-primary">Get Started</a>
                    <a href="contact.php" class="btn btn-secondary">Contact Us</a>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<style>
    .about-page {
        padding-top: 2rem;
    }

    .hero-section {
        background-color: #f8f9fa;
        padding: 4rem 0;
        text-align: center;
    }

    .hero-section h1 {
        font-size: 2.5rem;
        color: #333;
        margin-bottom: 1rem;
    }

    .subtitle {
        font-size: 1.2rem;
        color: #666;
    }

    section {
        padding: 4rem 0;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    h2 {
        text-align: center;
        margin-bottom: 2rem;
        color: #333;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    .feature-card {
        text-align: center;
        padding: 2rem;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .feature-icon {
        font-size: 2rem;
        color: #007bff;
        margin-bottom: 1rem;
    }

    .story-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        align-items: center;
    }

    .story-image img {
        width: 100%;
        border-radius: 8px;
    }

    .join-us {
        background-color: #f8f9fa;
        text-align: center;
    }

    .cta-buttons {
        margin-top: 2rem;
        display: flex;
        gap: 1rem;
        justify-content: center;
    }

    .btn {
        padding: 0.8rem 2rem;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 600;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    @media (max-width: 768px) {
        .story-content {
            grid-template-columns: 1fr;
        }

        .features-grid {
            grid-template-columns: 1fr;
        }

        .hero-section {
            padding: 2rem 0;
        }

        .hero-section h1 {
            font-size: 2rem;
        }
    }
</style>

<?php
require_once 'includes/footer.php';
?> 