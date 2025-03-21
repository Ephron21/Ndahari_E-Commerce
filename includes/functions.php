<?php
// Useful functions for the application

// Function to sanitize input
function sanitize_input($input) {
    if ($input === null) {
        return '';
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Function to handle file uploads
function handle_file_upload($file, $admin_id, $file_type) {
    $upload_dir = "../uploads/";
    $target_dir = "";
    
    // Determine target directory based on file type
    switch($file_type) {
        case 'id':
            $target_dir = $upload_dir . "id_documents/";
            break;
        case 'diploma':
            $target_dir = $upload_dir . "diplomas/";
            break;
        case 'image':
            $target_dir = $upload_dir . "profile_images/";
            break;
        default:
            return false;
    }
    
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Generate unique filename
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = $file_type . '_' . $user_id . '_' . time() . '.' . $file_extension;
    $destination = $target_dir . $new_filename;
    
    // Move file to destination
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $destination;
    }
    
    return false;
}

function getPublicFiles($conn, $limit = 6) {
    $query = "SELECT * FROM uploaded_files WHERE is_public = 1 ORDER BY upload_date DESC LIMIT :limit";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function uploadFile($conn, $file, $title, $description, $isPublic, $userId) {
    $uploadDir = 'uploads/';
    $filename = uniqid() . '_' . basename($file['name']);
    $targetPath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $query = "INSERT INTO uploaded_files 
                  (filename, title, description, file_type, uploaded_by, is_public, upload_date) 
                  VALUES (:filename, :title, :description, :file_type, :uploaded_by, :is_public, NOW())";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':filename', $filename);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':file_type', $file['type']);
        $stmt->bindParam(':uploaded_by', $userId);
        $stmt->bindParam(':is_public', $isPublic, PDO::PARAM_BOOL);
        
        return $stmt->execute();
    }
    return false;
}

function deleteFile($conn, $fileId, $userId, $isAdmin = false) {
    // Check file ownership or admin status
    $checkQuery = $isAdmin 
        ? "SELECT filename FROM uploaded_files WHERE id = :file_id" 
        : "SELECT filename FROM uploaded_files WHERE id = :file_id AND uploaded_by = :user_id";
    
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bindParam(':file_id', $fileId);
    if (!$isAdmin) {
        $checkStmt->bindParam(':user_id', $userId);
    }
    $checkStmt->execute();
    $file = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($file) {
        // Delete file from filesystem
        unlink('uploads/' . $file['filename']);
        
        // Delete file record from database
        $deleteQuery = "DELETE FROM uploaded_files WHERE id = :file_id";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bindParam(':file_id', $fileId);
        return $deleteStmt->execute();
    }
    
    return false;
}

function updateFileDetails($conn, $fileId, $userId, $title, $description, $isPublic) {
    $query = "UPDATE uploaded_files 
              SET title = :title, 
                  description = :description, 
                  is_public = :is_public 
              WHERE id = :file_id AND uploaded_by = :user_id";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':is_public', $isPublic, PDO::PARAM_BOOL);
    $stmt->bindParam(':file_id', $fileId);
    $stmt->bindParam(':user_id', $userId);
    
    return $stmt->execute();
}

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
