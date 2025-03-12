<?php
// Include database connection
require_once 'includes/db_connection.php';

// Get database connection
$conn = get_db_connection();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input data
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $message = htmlspecialchars($_POST['message']);

    // Validate input data
    if (empty($name) || empty($email) || empty($phone) || empty($message)) {
        die("Please fill in all fields.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email address.");
    }

    // Insert data into the database
    try {
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO user_messages (name, email, phone, message) VALUES (?, ?, ?, ?)");
        
        // Bind parameters
        $stmt->bind_param("ssss", $name, $email, $phone, $message);

        // Execute the statement
        if ($stmt->execute()) {
            // Success message with auto-redirect to home page
            echo "
                <div style='text-align: center; margin-top: 20px;'>
                    <p>Thank you for contacting us! We will get back to you soon.</p>
                    <p>You will be redirected to the home page in 5 seconds...</p>
                </div>
                <script>
                    setTimeout(function() {
                        window.location.href = 'index.php';
                    }, 5000); // Redirect after 5 seconds
                </script>
            ";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } catch (Exception $e) {
        die("Database error: " . $e->getMessage());
    }

    // Close the database connection
    $conn->close();
} else {
    // Redirect if the form was not submitted
    header("Location: contact.php");
    exit();
}
?>