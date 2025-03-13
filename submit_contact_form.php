<?php
session_start();

// Include database configuration
require_once 'config/database.php';

// Include header
include('includes/header.php');

// Process form submission
$formSubmitted = false;
$formErrors = [];
$formSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debug: Log form submission
    error_log("Form submitted via POST");
    
    // Validate form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $inquiry = trim($_POST['inquiry']);
    $message = trim($_POST['message']);

    // Debug: Log form data
    error_log("Form data - Name: $name, Email: $email, Phone: $phone, Inquiry: $inquiry");

    // Validation
    if (empty($name)) {
        $formErrors['name'] = "Name is required";
    }

    if (empty($email)) {
        $formErrors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $formErrors['email'] = "Invalid email format";
    }

    if (empty($phone)) {
        $formErrors['phone'] = "Phone number is required";
    }

    if (empty($message)) {
        $formErrors['message'] = "Message is required";
    } elseif (strlen($message) < 10) {
        $formErrors['message'] = "Message must be at least 10 characters";
    }

    // If no errors, process the form
    if (empty($formErrors)) {
        try {
            // Debug: Log database connection attempt
            error_log("Attempting database connection");
            
            // Create database instance
            $database = new Database();
            $db = $database->getConnection();

            // Debug: Log successful connection
            error_log("Database connection successful");

            // Check if table exists
            $tableCheck = $db->query("SHOW TABLES LIKE 'contact_submissions'");
            
            // Debug: Log table check
            error_log("Table check result: " . $tableCheck->rowCount());

            if ($tableCheck->rowCount() == 0) {
                // Debug: Log table creation
                error_log("Creating contact_submissions table");
                
                // Create table if it doesn't exist
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
            }

            // Insert into database
            $stmt = $db->prepare("INSERT INTO contact_submissions 
                (name, email, phone, inquiry_type, message, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?, ?, ?)");

            // Debug: Log insert attempt
            error_log("Attempting to insert data into contact_submissions");

            $result = $stmt->execute([
                $name,
                $email,
                $phone,
                $inquiry,
                $message,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);

            // Debug: Log insert result
            error_log("Insert result: " . ($result ? "Success" : "Failed"));
            if (!$result) {
                error_log("Insert error info: " . print_r($stmt->errorInfo(), true));
            }

            $formSuccess = true;
            $formSubmitted = true;

        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            $formSuccess = false;
            $formSubmitted = true;
            $formErrors['database'] = "Error submitting form. Please try again later.";
        }
    } else {
        $formSubmitted = true;
    }
}
?>
