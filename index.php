
<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session securely
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();

// Include necessary files
require_once 'includes/db_connection.php';
require_once 'includes/function.php';
require_once 'includes/header.php';

// Initialize database connection
$conn = get_db_connection();

// Fetch data for the homepage
$featuredJobs = getFeaturedJobs($conn, 6);
$successStories = getSuccessStories($conn, 3);
$jobCategories = getJobCategories($conn);
$locations = getLocations($conn);
$recentJobs = getRecentJobs($conn, 4);
$stats = getStatistics($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ndahari Platform - Connect with Part-Time Opportunities</title>
    
    <!-- External CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="public/css/main.css">
    <link rel="stylesheet" href="public/css/home.css">
</head>
<body>
    <?php include 'components/hero_section.php'; ?>
    
    <?php include 'components/features_section.php'; ?>
    
    <?php include 'components/how_it_works.php'; ?>
    
    <?php include 'components/jobs_section.php'; ?>
    
    <?php include 'components/categories_section.php'; ?>
    
    <?php include 'components/testimonials_section.php'; ?>
    
    <?php include 'components/statistics_section.php'; ?>
    
    <?php include 'components/cta_section.php'; ?>
    
    <?php include 'components/search_modal.php'; ?>
    
    <?php include 'components/post_job_modal.php'; ?>
    
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="public/js/main.js"></script>
    <script src="public/js/home.js"></script>
</body>
</html>
