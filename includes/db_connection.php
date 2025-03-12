<?php
/**
 * Database connection configuration file
 * 
 * This file establishes a connection to the MySQL database
 */

// Enable MySQLi exceptions for better error handling
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');          // Default XAMPP username
define('DB_PASS', 'Diano21@Esron21%'); // Your MySQL root password
define('DB_NAME', 'ndahari');       // Database to connect to

/**
 * Get database connection
 * 
 * @return mysqli A database connection
 */
function get_db_connection() {
    try {
        // Attempt to connect directly to the database
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (mysqli_sql_exception $e) {
        // If error is "Unknown database", create it
        if ($e->getCode() === 1049) { // 1049 = "Unknown database" error code
            // Connect without specifying the database
            $connWithoutDb = new mysqli(DB_HOST, DB_USER, DB_PASS);
            
            // Create the database
            $connWithoutDb->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
            
            // Close the temporary connection
            $connWithoutDb->close();
            
            // Reconnect with the database now that it exists
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            $conn->set_charset("utf8mb4");
            return $conn;
        } else {
            // Re-throw other errors (e.g., wrong credentials)
            throw $e;
        }
    }
}
?>