<?php
require_once 'includes/config.php';

try {
    // Drop the existing table
    $pdo->exec("DROP TABLE IF EXISTS jobs");
    
    // Create the table with the correct structure
    $sql = "CREATE TABLE jobs (
        id INT PRIMARY KEY AUTO_INCREMENT,
        employer_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        job_type VARCHAR(50) NOT NULL,
        location VARCHAR(255) NOT NULL,
        salary_range VARCHAR(100) NOT NULL,
        requirements TEXT NOT NULL,
        responsibilities TEXT,
        application_deadline DATE,
        status ENUM('open', 'closed', 'draft') DEFAULT 'open',
        posted_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (employer_id) REFERENCES employers(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    
    // Now alter the job_type column to be an ENUM
    $alter_sql = "ALTER TABLE jobs 
                  MODIFY COLUMN job_type ENUM('full_time', 'part_time', 'temporary', 'contract', 
                                            'weekend', 'evening', 'seasonal', 'internship') NOT NULL";
    
    $pdo->exec($alter_sql);
    
    echo "Jobs table has been reset successfully!";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 