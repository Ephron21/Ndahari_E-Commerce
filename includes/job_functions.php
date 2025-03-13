<?php
/**
 * Job-related functions
 */

/**
 * Get featured jobs from the database
 * 
 * @param PDO $conn Database connection
 * @param int $limit Number of jobs to retrieve
 * @return array Array of featured jobs
 */
if (!function_exists('getFeaturedJobs')) {
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
}

/**
 * Get recent jobs from the database
 * 
 * @param PDO $conn Database connection
 * @param int $limit Number of jobs to retrieve
 * @return array Array of recent jobs
 */
if (!function_exists('getRecentJobs')) {
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
}

/**
 * Get job categories from the database
 * 
 * @param PDO $conn Database connection
 * @return array Array of job categories
 */
if (!function_exists('getJobCategories')) {
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
}

/**
 * Get locations from the database
 * 
 * @param PDO $conn Database connection
 * @return array Array of locations
 */
if (!function_exists('getLocations')) {
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
}

/**
 * Get job icon based on job type
 * 
 * @param string $jobType Job type/category
 * @return string Icon emoji for the job type
 */
if (!function_exists('getJobIcon')) {
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
        
        return $iconMap[strtolower($jobType)] ?? $iconMap['default'];
    }
}
?>