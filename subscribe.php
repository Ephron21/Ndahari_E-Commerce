<?php
session_start();

// Set response header
header('Content-Type: application/json');

// Simulate processing delay
sleep(1);

// Get email from POST request
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

// Validate email
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Please enter a valid email address.'
    ]);
    exit;
}

// In a real application, you would:
// 1. Check if the email already exists in your database
// 2. Save the email to your subscribers table
// 3. Possibly send a confirmation email
// 4. Handle any errors appropriately

// For this demo, we'll just simulate success
echo json_encode([
    'success' => true,
    'message' => 'Thank you for subscribing to our newsletter! We\'ll keep you updated with the latest from Ndahari.'
]);
?>
