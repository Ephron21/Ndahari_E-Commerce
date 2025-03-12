<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session securely
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();

// Include header (using relative path for portability)
include('includes/header.php');

// Database configuration
require_once 'includes/db_connection.php'; // Ensure this file contains your database connection details

// Initialize database connection
$conn = get_db_connection();

// Function to check if user is logged in
function isUserLoggedIn() {
    return isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
}

// Function to get job icon based on job type
function getJobIcon($jobType) {
    $iconMap = [
        'hospitality' => '🍽️',
        'retail' => '🛍️',
        'events' => '🎪',
        'construction' => '🏗️',
        'education' => '📚',
        'technology' => '💻',
        'healthcare' => '🏥',
        'office' => '📊',
        'default' => '💼'
    ];
    
    return $iconMap[$jobType] ?? $iconMap['default'];
}

// Fetch featured jobs
$featuredJobs = [];
try {
    $query = "SELECT j.*, e.company_name, e.logo 
              FROM jobs j 
              LEFT JOIN employers e ON j.employer_id = e.id 
              WHERE j.is_featured = 1 
              ORDER BY j.posted_date DESC 
              LIMIT 6"; // Limit to 6 most recent featured jobs
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $featuredJobs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    die("Error fetching featured jobs: " . $e->getMessage());
}

// Fetch success stories
$successStories = [];
try {
    $query = "SELECT s.*, js.full_name AS jobseeker_name, e.company_name
              FROM success_stories s
              LEFT JOIN job_seekers js ON s.jobseeker_id = js.id
              LEFT JOIN employers e ON s.employer_id = e.id
              ORDER BY s.date DESC
              LIMIT 3"; // Limit to 3 most recent success stories
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $successStories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    die("Error fetching success stories: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ndahari Platform - Connect with Part-Time Opportunities</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        /* General Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fc;
            color: #333;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Background Image for the Header */
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('images/hero-bg.jpg') no-repeat center center/cover;
            padding: 100px 0;
            color: white;
            text-align: center;
            animation: fadeIn 2s ease-in-out;
        }

        .hero-section h1 {
            font-size: 3rem;
            animation: slideInLeft 1.5s ease-out;
            margin-bottom: 20px;
        }

        .hero-section p {
            font-size: 1.2rem;
            margin-top: 20px;
            animation: slideInRight 1.5s ease-out;
            max-width: 800px;
            margin: 0 auto 30px;
        }

        /* Container for Content */
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 0 15px;
        }

        /* Section Styling */
        section {
            margin: 60px 0;
            padding: 30px 0;
        }

        h2 {
            text-align: center;
            font-size: 2.2rem;
            margin-bottom: 30px;
            color: #0056b3;
        }

        h3 {
            color: #0056b3;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        /* CTA Buttons */
        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 30px 0;
        }

        .cta-button {
            padding: 15px 30px;
            font-size: 1.1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }

        .cta-primary {
            background-color: #0056b3;
            color: white;
        }

        .cta-primary:hover {
            background-color: #004494;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .cta-secondary {
            background-color: white;
            color: #0056b3;
            border: 2px solid #0056b3;
        }

        .cta-secondary:hover {
            background-color: #f0f5ff;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* Features Section */
        .features {
            background-color: white;
            padding: 60px 0;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .feature-card {
            text-align: center;
            padding: 30px 20px;
            border-radius: 8px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: #f9f9f9;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #0056b3;
        }

        /* How It Works Section */
        .how-it-works {
            background-color: #f9f9f9;
            padding: 60px 0;
        }

        .steps {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-top: 40px;
        }

        .step {
            flex: 1;
            min-width: 250px;
            text-align: center;
            padding: 0 20px;
            position: relative;
        }

        .step-number {
            width: 50px;
            height: 50px;
            background-color: #0056b3;
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 20px;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 25px;
            right: 0;
            width: 70%;
            height: 2px;
            background-color: #ddd;
            z-index: -1;
        }

        /* Jobs Section */
        .jobs-section {
            padding: 60px 0;
        }

        .search-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 30px;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .search-input {
            flex: 1;
            min-width: 200px;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .filter-select {
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            min-width: 150px;
        }

        .search-button {
            padding: 12px 20px;
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        .search-button:hover {
            background-color: #004494;
        }

        .jobs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }

        .job-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .job-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .job-header {
            display: flex;
            align-items: center;
            padding: 15px;
            background-color: #f5f5f5;
        }

        .job-logo {
            width: 60px;
            height: 60px;
            border-radius: 5px;
            margin-right: 15px;
            object-fit: cover;
            background-color: #eee;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2rem;
        }

        .job-title-company h3 {
            margin: 0 0 5px;
            font-size: 1.2rem;
        }

        .company-name {
            color: #666;
            font-size: 0.9rem;
        }

        .job-body {
            padding: 15px;
        }

        .job-details {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 10px 0;
        }

        .job-detail {
            background-color: #f0f5ff;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            color: #0056b3;
            display: flex;
            align-items: center;
        }

        .job-detail i {
            margin-right: 5px;
        }

        .job-description {
            margin: 15px 0;
            color: #555;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .job-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-top: 1px solid #eee;
        }

        .job-posted {
            color: #888;
            font-size: 0.85rem;
        }

        .job-apply {
            padding: 8px 15px;
            background-color: #0056b3;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }

        .job-apply:hover {
            background-color: #004494;
        }

        .view-all-jobs {
            margin-top: 30px;
            text-align: center;
        }

        /* Testimonials Section */
        .testimonials {
            background-color: #0056b3;
            color: white;
            padding: 60px 0;
            text-align: center;
        }

        .testimonials h2 {
            color: white;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .testimonial-card {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 10px;
            position: relative;
        }

        .testimonial-text {
            font-style: italic;
            margin-bottom: 20px;
            position: relative;
        }

        .testimonial-text::before,
        .testimonial-text::after {
            content: '"';
            font-size: 1.5rem;
            font-weight: bold;
        }

        .testimonial-author {
            font-weight: bold;
        }

        .testimonial-position {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* Statistics Section */
        .statistics {
            text-align: center;
            padding: 60px 0;
            background-color: #f9f9f9;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .stat-card {
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #0056b3;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #666;
        }

        /* Keyframes for Animations */
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        @keyframes slideInLeft {
            0% { transform: translateX(-100%); opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
        }

        @keyframes slideInRight {
            0% { transform: translateX(100%); opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
        }

        /* Responsive Design Adjustments */
        @media (max-width: 768px) {
            .hero-section {
                padding: 60px 0;
            }

            .hero-section h1 {
                font-size: 2.2rem;
            }

            .cta-buttons {
                flex-direction: column;
                gap: 10px;
            }

            .steps {
                flex-direction: column;
            }

            .step:not(:last-child)::after {
                display: none;
            }
            
            .step {
                margin-bottom: 30px;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1>Find Your Perfect Part-Time Opportunity</h1>
            <p>Ndahari connects skilled individuals with businesses looking for part-time workers. Whether you're a student seeking flexible work, a freelancer, or a business in need of temporary help, Ndahari has you covered.</p>
            <div class="cta-buttons">
                <a href="find-jobs.php" class="cta-button cta-primary"><i class="fas fa-search"></i> Find Jobs</a>
                <a href="post-job.php" class="cta-button cta-secondary"><i class="fas fa-plus-circle"></i> Post a Job</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <h2>Why Choose Ndahari?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-handshake"></i></div>
                    <h3>Trusted Connections</h3>
                    <p>We verify all users, creating a safe and reliable platform for businesses and job seekers.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-bolt"></i></div>
                    <h3>Quick Matching</h3>
                    <p>Our smart algorithm matches skilled workers with the perfect opportunities based on qualifications and preferences.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-calendar-alt"></i></div>
                    <h3>Flexible Scheduling</h3>
                    <p>Find work that fits your availability or workers who can meet your timeline needs.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                    <h3>Secure Payments</h3>
                    <p>Enjoy transparent and secure payment processing for all jobs completed through our platform.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works" id="how-it-works">
        <div class="container">
            <h2>How Ndahari Works</h2>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Create Your Profile</h3>
                    <p>Sign up and build your profile highlighting your skills, experience, and availability.</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Find Opportunities</h3>
                    <p>Browse and apply for jobs that match your skills and schedule preferences.</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Complete the Work</h3>
                    <p>Deliver high-quality work and build your reputation on the platform.</p>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <h3>Get Paid</h3>
                    <p>Receive secure payments for completed work through our trusted payment system.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Jobs Section -->
    <section class="jobs-section" id="jobs">
        <div class="container">
            <h2>Featured Job Opportunities</h2>
            <div class="search-filters">
                <input type="text" class="search-input" placeholder="Search for jobs...">
                <select class="filter-select">
                    <option value="">All Categories</option>
                    <option value="hospitality">Hospitality</option>
                    <option value="retail">Retail</option>
                    <option value="events">Events</option>
                    <option value="construction">Construction</option>
                    <option value="education">Education</option>
                </select>
                <select class="filter-select">
                    <option value="">All Locations</option>
                    <option value="kigali">Kigali</option>
                    <option value="nyanza">Nyanza</option>
                    <option value="huye">Huye</option>
                    <option value="rubavu">Rubavu</option>
                    <option value="musanze">Musanze</option>
                </select>
                <button class="search-button"><i class="fas fa-search"></i> Search</button>
            </div>

            <div class="jobs-grid">
                <?php if (empty($featuredJobs)): ?>
                    <div class="no-jobs">
                        <p>No featured jobs available at the moment. Please check back later.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($featuredJobs as $job): ?>
                        <div class="job-card">
                            <div class="job-header">
                                <div class="job-logo">
                                    <?php if (!empty($job['logo'])): ?>
                                        <img src="<?php echo htmlspecialchars($job['logo']); ?>" alt="<?php echo htmlspecialchars($job['company_name']); ?>">
                                    <?php else: ?>
                                        <?php echo getJobIcon($job['category']); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="job-title-company">
                                    <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                                    <div class="company-name"><?php echo htmlspecialchars($job['company_name']); ?></div>
                                </div>
                            </div>
                            <div class="job-body">
                                <div class="job-details">
                                    <span class="job-detail"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location']); ?></span>
                                    <span class="job-detail"><i class="fas fa-clock"></i> <?php echo htmlspecialchars($job['job_type']); ?></span>
                                    <span class="job-detail"><i class="fas fa-money-bill-wave"></i> <?php echo htmlspecialchars($job['salary_range']); ?></span>
                                </div>
                                <div class="job-description">
                                    <?php echo htmlspecialchars(substr($job['description'], 0, 150) . '...'); ?>
                                </div>
                            </div>
                            <div class="job-footer">
                                <div class="job-posted">
                                    <i class="far fa-calendar-alt"></i> Posted <?php echo date('M d, Y', strtotime($job['posted_date'])); ?>
                                </div>
                                <a href="job-details.php?id=<?php echo $job['id']; ?>" class="job-apply">View Details</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="view-all-jobs">
                <a href="find-jobs.php" class="cta-button cta-primary">View All Jobs</a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials" id="testimonials">
        <div class="container">
            <h2>Success Stories</h2>
            
            <?php if (empty($successStories)): ?>
                <p>Be the first to share your success story with Ndahari!</p>
            <?php else: ?>
                <div class="testimonials-grid">
                    <?php foreach ($successStories as $story): ?>
                        <div class="testimonial-card">
                            <div class="testimonial-text"><?php echo htmlspecialchars($story['testimonial']); ?></div>
                            <div class="testimonial-author"><?php echo htmlspecialchars($story['jobseeker_name']); ?></div>
                            <div class="testimonial-position">
                                <?php echo htmlspecialchars($story['job_title']); ?> at 
                                <?php echo htmlspecialchars($story['company_name']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="statistics">
        <div class="container">
            <h2>Ndahari Impact</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">5,00+</div>
                    <div class="stat-label">Registered Job Seekers</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Businesses</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">3,500+</div>
                    <div class="stat-label">Jobs Completed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">98%</div>
                    <div class="stat-label">Satisfaction Rate</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Include footer (using relative path for portability) -->
    <?php include('includes/footer.php'); ?>

    <script>
        // Smooth Scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // For demonstration purposes - to make the search functional
        const searchButton = document.querySelector('.search-button');
        if (searchButton) {
            searchButton.addEventListener('click', function() {
                const searchInput = document.querySelector('.search-input').value;
                const categorySelect = document.querySelector('.filter-select').value;
                // Implement search functionality or redirect to search page
                window.location.href = `find-jobs.php?q=${searchInput}&category=${categorySelect}`;
            });
        }
    </script>
</body>
</html>