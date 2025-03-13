<?php
/**
 * Statistics-related functions
 */

/**
 * Get success stories from the database
 * 
 * @param PDO $conn Database connection
 * @param int $limit Number of stories to retrieve
 * @return array Array of success stories
 */
if (!function_exists('getSuccessStories')) {
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
}

/**
 * Get platform statistics
 * 
 * @param PDO $conn Database connection
 * @return array Array of statistics
 */
if (!function_exists('getStatistics')) {
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
}

/**
 * Count rows in a table
 * 
 * @param PDO $conn Database connection
 * @param string $table Table name
 * @return int Number of rows
 */
if (!function_exists('countTableRows')) {
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
}

/**
 * Get count of completed jobs
 * 
 * @param PDO $conn Database connection
 * @return int Number of completed jobs
 */
if (!function_exists('getCompletedJobsCount')) {
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
}

/**
 * Format date for display
 * 
 * @param string $date Date string
 * @return string Formatted date
 */
if (!function_exists('formatDate')) {
    function formatDate($date) {
        return date('M d, Y', strtotime($date));
    }
}
?>