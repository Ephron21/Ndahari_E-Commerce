<?php
require_once 'includes/config.php';

try {
    // Drop the existing jobs table if it exists
    $pdo->exec("DROP TABLE IF EXISTS jobs");
    
    // Create the jobs table with the correct structure
    $sql = "CREATE TABLE jobs (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        employer_id INT(11) UNSIGNED NOT NULL,
        job_title VARCHAR(255) NOT NULL,
        job_description TEXT NOT NULL,
        job_type VARCHAR(50) NOT NULL,
        location VARCHAR(255) NOT NULL,
        salary_range VARCHAR(100) NOT NULL,
        requirements TEXT NOT NULL,
        responsibilities TEXT,
        application_deadline DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status VARCHAR(20) DEFAULT 'active',
        is_featured TINYINT(1) DEFAULT 0,
        FOREIGN KEY (employer_id) REFERENCES employers(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "Jobs table has been reset and recreated successfully!";
    
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?> 