<?php
/**
 * Functions for retrieving and processing data
 */
require_once 'user_functions.php';
require_once 'job_functions.php';
require_once 'stat_functions.php';
/**
 * Get featured jobs from the database
 * 
 * @param PDO $conn Database connection
 * @param int $limit Number of jobs to retrieve
 * @return array Array of featured jobs
 */
function getFeaturedJobs($conn, $limit = 6) {
    $featuredJobs = [];
    try {
        $query = "SELECT j.*, e.company_name, e.logo 
                  FROM jobs j 
                  LEFT JOIN employers e ON j.employer_id = e.id 
                  WHERE j.is_featured = 1 
                  ORDER BY j.posted_date DESC 
                  LIMIT :limit";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $featuredJobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error fetching featured jobs: " . $e->getMessage());
        return [];
    }
    return $featuredJobs;
}

/**
 * Get recent jobs from the database
 * 
 * @param PDO $conn Database connection
 * @param int $limit Number of jobs to retrieve
 * @return array Array of recent jobs
 */
function getRecentJobs($conn, $limit = 4) {
    $recentJobs = [];
    try {
        $query = "SELECT j.*, e.company_name, e.logo 
                  FROM jobs j 
                  LEFT JOIN employers e ON j.employer_id = e.id 
                  ORDER BY j.posted_date DESC 
                  LIMIT :limit";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $recentJobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error fetching recent jobs: " . $e->getMessage());
        return [];
    }
    return $recentJobs;
}

/**
 * Get success stories from the database
 * 
 * @param PDO $conn Database connection
 * @param int $limit Number of stories to retrieve
 * @return array Array of success stories
 */
function getSuccessStories($conn, $limit = 3) {
    $successStories = [];
    try {
        $query = "SELECT s.*, js.full_name AS jobseeker_name, e.company_name
                  FROM success_stories s
                  LEFT JOIN job_seekers js ON s.jobseeker_id = js.id
                  LEFT JOIN employers e ON s.employer_id = e.id
                  ORDER BY s.date DESC
                  LIMIT :limit";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $successStories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error fetching success stories: " . $e->getMessage());
        return [];
    }
    return $successStories;
}

/**
 * Get job categories from the database
 * 
 * @param PDO $conn Database connection
 * @return array Array of job categories
 */
function getJobCategories($conn) {
    $categories = [];
    try {
        $query = "SELECT * FROM job_categories ORDER BY name ASC";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error fetching job categories: " . $e->getMessage());
        return [];
    }
    return $categories;
}

/**
 * Get locations from the database
 * 
 * @param PDO $conn Database connection
 * @return array Array of locations
 */
function getLocations($conn) {
    $locations = [];
    try {
        $query = "SELECT DISTINCT location FROM jobs ORDER BY location ASC";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $locations = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (Exception $e) {
        error_log("Error fetching locations: " . $e->getMessage());
        return [];
    }
    return $locations;
}

/**
 * Get platform statistics
 * 
 * @param PDO $conn Database connection
 * @return array Array of statistics
 */
function getStatistics($conn) {
    try {
        // In a real application, you would get these from the database
        $stats = [
            'job_seekers' => countTableRows($conn, 'job_seekers'),
            'businesses' => countTableRows($conn, 'employers'),
            'jobs_completed' => getCompletedJobsCount($conn),
            'satisfaction_rate' => 98 // Could be calculated from ratings
        ];
        return $stats;
    } catch (Exception $e) {
        error_log("Error fetching statistics: " . $e->getMessage());
        return [
            'job_seekers' => '500+',
            'businesses' => '200+',
            'jobs_completed' => '3,500+',
            'satisfaction_rate' => '98%'
        ];
    }
}

/**
 * Count rows in a table
 * 
 * @param PDO $conn Database connection
 * @param string $table Table name
 * @return int Number of rows
 */
function countTableRows($conn, $table) {
    try {
        $query = "SELECT COUNT(*) FROM " . $table;
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        error_log("Error counting rows in {$table}: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get count of completed jobs
 * 
 * @param PDO $conn Database connection
 * @return int Number of completed jobs
 */
function getCompletedJobsCount($conn) {
    try {
        $query = "SELECT COUNT(*) FROM jobs WHERE status = 'completed'";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        error_log("Error counting completed jobs: " . $e->getMessage());
        return 0;
    }
}

/**
 * Function to check if user is logged in
 * 
 * @return bool True if logged in, false otherwise
 */
function isUserLoggedIn() {
    return isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
}

/**
 * Get job icon based on job type
 * 
 * @param string $jobType Job type/category
 * @return string Icon emoji for the job type
 */
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

/**
 * Format date for display
 * 
 * @param string $date Date string
 * @return string Formatted date
 */
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}
