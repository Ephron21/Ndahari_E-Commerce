<?php
/**
 * Database connection handling
 */

/**
 * Get database connection
 * 
 * @return PDO Database connection
 */
function get_db_connection() {
    static $conn = null;
    
    if ($conn === null) {
        $db_host = 'localhost';
        $db_name = 'ndahari';
        $db_user = 'root';
        $db_pass = 'Diano21@Esron21%'; // In production, use environment variables
        
        try {
            $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            die("Database connection failed. Please try again later.");
        }
    }
    
    return $conn;
}
