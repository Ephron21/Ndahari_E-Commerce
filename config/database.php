<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');  // Change to your database username
define('DB_PASS', 'Diano21@Esron21%');      // Change to your database password
define('DB_NAME', 'Ndahari');

// Create database connection
function get_db_connection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}
?>