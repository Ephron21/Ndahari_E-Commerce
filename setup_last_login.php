<?php
require_once 'includes/config.php';

try {
    // Read and execute the SQL file
    $sql = file_get_contents('add_last_login_column.sql');
    $pdo->exec($sql);
    
    echo "Successfully added last_login columns to the database tables.<br>";
    echo "<a href='dashboard.php'>Return to Dashboard</a>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    error_log("Error adding last_login columns: " . $e->getMessage());
} 