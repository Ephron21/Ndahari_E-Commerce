<?php
require_once 'includes/config.php';

try {
    // Function to check if column exists
    function columnExists($pdo, $table, $column) {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM information_schema.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
            AND COLUMN_NAME = ?
        ");
        $stmt->execute([$table, $column]);
        return (bool)$stmt->fetchColumn();
    }

    // Add reset token columns to employers table
    if (!columnExists($pdo, 'employers', 'reset_token')) {
        $pdo->exec("
            ALTER TABLE employers
            ADD COLUMN reset_token VARCHAR(64) DEFAULT NULL,
            ADD COLUMN reset_token_expires DATETIME DEFAULT NULL
        ");
        echo "Reset token columns added to employers table.<br>";
    } else {
        echo "Reset token columns already exist in employers table.<br>";
    }
    
    // Add reset token columns to job_seekers table
    if (!columnExists($pdo, 'job_seekers', 'reset_token')) {
        $pdo->exec("
            ALTER TABLE job_seekers
            ADD COLUMN reset_token VARCHAR(64) DEFAULT NULL,
            ADD COLUMN reset_token_expires DATETIME DEFAULT NULL
        ");
        echo "Reset token columns added to job_seekers table.<br>";
    } else {
        echo "Reset token columns already exist in job_seekers table.<br>";
    }
    
    echo "<br>Setup completed successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    error_log("Database setup error: " . $e->getMessage());
}
?> 