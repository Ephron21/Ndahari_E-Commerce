<?php
class Database {
    private $host = "localhost";
    private $db_name = "ndahari";
    private $username = "root";
    private $password = "Diano21@Esron21%";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // Enable error reporting
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            
            // Log connection attempt
            error_log("[Database] Attempting to connect to MySQL database: {$this->db_name}");
            
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                )
            );
            
            // Log successful connection
            error_log("[Database] Successfully connected to MySQL database");
            
        } catch(PDOException $e) {
            error_log("[Database] Connection Error: " . $e->getMessage());
            throw new PDOException("Connection failed: " . $e->getMessage());
        }
        return $this->conn;
    }
}
?>
