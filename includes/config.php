<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');  // Default XAMPP username
define('DB_PASS', 'Diano21@Esron21%');      // Default XAMPP password
define('DB_NAME', 'ndahari');

// Establish database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
    die("Sorry, there was a problem connecting to the database. Please try again later.");
}

// Site configuration
define('SITE_NAME', 'Ndahari Jobs');
define('SITE_URL', 'http://localhost/Ndahari_E-Commerce');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Time zone setting
date_default_timezone_set('Africa/Nairobi');

// File upload settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', ['pdf', 'doc', 'docx']);

// Function to format dates
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $difference = time() - $timestamp;
    
    if ($difference < 60) {
        return "just now";
    } elseif ($difference < 3600) {
        return floor($difference / 60) . " minutes ago";
    } elseif ($difference < 86400) {
        return floor($difference / 3600) . " hours ago";
    } elseif ($difference < 604800) {
        return floor($difference / 86400) . " days ago";
    } elseif ($difference < 2592000) {
        return floor($difference / 604800) . " weeks ago";
    } else {
        return date("M j, Y", $timestamp);
    }
} 