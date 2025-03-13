<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

try {
    // Create database instance
    $database = new Database();
    $db = $database->getConnection();
    
    echo "Database connection successful!<br>";
    
    // Test table creation
    $createTable = "CREATE TABLE IF NOT EXISTS contact_submissions (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        inquiry_type VARCHAR(50),
        message TEXT NOT NULL,
        ip_address VARCHAR(45),
        user_agent TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $db->exec($createTable);
    echo "Table creation/check successful!<br>";
    
    // Test insert
    $stmt = $db->prepare("INSERT INTO contact_submissions 
        (name, email, phone, inquiry_type, message, ip_address, user_agent)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $result = $stmt->execute([
        'Test User',
        'test@example.com',
        '1234567890',
        'test',
        'This is a test message',
        '127.0.0.1',
        'Test Script'
    ]);
    
    if ($result) {
        echo "Test insert successful!<br>";
        
        // Verify the insert
        $query = "SELECT * FROM contact_submissions ORDER BY id DESC LIMIT 1";
        $stmt = $db->query($query);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "Retrieved test record:<br>";
        echo "<pre>";
        print_r($row);
        echo "</pre>";
    } else {
        echo "Insert failed!<br>";
        print_r($stmt->errorInfo());
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 