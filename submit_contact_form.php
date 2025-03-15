<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session securely
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();

// Include necessary files
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';

// Initialize database connection
$conn = get_db_connection();

// Initialize variables
$formErrors = [];
$formSuccess = false;

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form data
    if (empty($_POST['name'])) {
        $formErrors['name'] = "Name is required";
    }
    
    if (empty($_POST['email'])) {
        $formErrors['email'] = "Email is required";
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $formErrors['email'] = "Invalid email format";
    }
    
    if (empty($_POST['phone'])) {
        $formErrors['phone'] = "Phone number is required";
    }
    
    if (empty($_POST['message'])) {
        $formErrors['message'] = "Message is required";
    } elseif (strlen($_POST['message']) < 10) {
        $formErrors['message'] = "Message must be at least 10 characters";
    }
    
    // If no errors, process the form
    if (empty($formErrors)) {
        try {
            // Prepare data for database
            $name = htmlspecialchars(strip_tags($_POST['name']));
            $email = htmlspecialchars(strip_tags($_POST['email']));
            $phone = htmlspecialchars(strip_tags($_POST['phone']));
            $inquiry = isset($_POST['inquiry']) ? htmlspecialchars(strip_tags($_POST['inquiry'])) : 'general';
            $message = htmlspecialchars(strip_tags($_POST['message']));
            $created_at = date('Y-m-d H:i:s');
            
            // Insert into database
            $query = "INSERT INTO contact_submissions (name, email, phone, inquiry_type, message, created_at) 
                      VALUES (:name, :email, :phone, :inquiry, :message, :created_at)";
            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':inquiry', $inquiry);
            $stmt->bindParam(':message', $message);
            $stmt->bindParam(':created_at', $created_at);
            
            if ($stmt->execute()) {
                $formSuccess = true;
                
                // Create an array of personalized success messages based on inquiry type
                $successMessages = [
                    'general' => [
                        "Thank you for reaching out, {$name}! Your message has been received, and our team is excited to assist you. We'll respond to {$email} within 24 hours.",
                        "We've got your inquiry, {$name}! Our team appreciates your interest and will get back to you very soon at {$email}.",
                        "Message received loud and clear, {$name}! Someone from our team will be in touch with you shortly at {$email}. Thank you for contacting us!"
                    ],
                    'support' => [
                        "Support request received, {$name}! Our technical team has been notified and will prioritize your issue. We'll contact you at {$email} or {$phone} with a solution soon.",
                        "Thank you for your support request, {$name}! Our specialists are reviewing your message and will reach out with assistance shortly. We appreciate your patience!",
                        "Help is on the way, {$name}! Your support request is now in our system, and a technical expert will contact you at {$email} within the next few hours."
                    ],
                    'jobs' => [
                        "Thank you for your job inquiry, {$name}! Our HR team will review your information and contact you at {$email} to discuss opportunities that match your skills.",
                        "We've received your career interest, {$name}! Someone from our recruiting team will reach out to {$email} to explore how your talents could fit with our organization.",
                        "Your job application has been received, {$name}! We're excited about your interest in joining our team and will contact you at {$email} to discuss next steps."
                    ],
                    'partnership' => [
                        "Thank you for your partnership proposal, {$name}! Our business development team is reviewing your message and will contact you at {$email} to explore collaboration opportunities.",
                        "We're excited about your partnership interest, {$name}! Our team will carefully review your proposal and reach out to {$email} to schedule a discussion soon.",
                        "Partnership request received, {$name}! We're always looking for strategic collaborations and will contact you at {$email} to discuss potential synergies."
                    ]
                ];
                
                // Get random success message based on inquiry type (default to general if type not found)
                $messageType = isset($successMessages[$inquiry]) ? $inquiry : 'general';
                $randomIndex = array_rand($successMessages[$messageType]);
                $personalizedMessage = $successMessages[$messageType][$randomIndex];
                
                // Additional information based on inquiry type
                $additionalInfo = '';
                switch($inquiry) {
                    case 'support':
                        $additionalInfo = "Your support ticket ID is #" . rand(10000, 99999) . ". Please reference this in future communications.";
                        break;
                    case 'jobs':
                        $additionalInfo = "While you wait, feel free to explore our current job listings and learn more about our company culture on our website.";
                        break;
                    case 'partnership':
                        $additionalInfo = "We review all partnership requests thoroughly and typically respond within 3-5 business days with next steps.";
                        break;
                    default:
                        $additionalInfo = "Our office hours are Monday to Friday, 8:00 AM to 6:00 PM (GMT+2).";
                }
                
                // Optional: Send email notification to admin
                $to = "info@edujobsscholars.com";
                $subject = "New Contact Form Submission";
                $email_message = "Name: $name\n";
                $email_message .= "Email: $email\n";
                $email_message .= "Phone: $phone\n";
                $email_message .= "Inquiry Type: $inquiry\n\n";
                $email_message .= "Message:\n$message\n";
                
                $headers = "From: noreply@ndahari.com";
                
                // Uncomment the line below in production to send actual emails
                // mail($to, $subject, $email_message, $headers);
                
                // Store success information in session
                $_SESSION['contact_form_status'] = [
                    'success' => true,
                    'message' => $personalizedMessage,
                    'additional_info' => $additionalInfo,
                    'name' => $name,
                    'inquiry_type' => $inquiry,
                    'submission_time' => date('F j, Y, g:i a')
                ];
            } else {
                $formErrors['database'] = "Failed to save your message. Please try again.";
            }
        } catch (PDOException $e) {
            $formErrors['database'] = "Database error: " . $e->getMessage();
        }
    }
    
    // Store form status in session
    if (!$formSuccess) {
        $_SESSION['contact_form_status'] = [
            'success' => false,
            'errors' => $formErrors
        ];
    }
    
    // Redirect back to contact page
    header("Location: contact.php");
    exit();
} else {
    // If not a POST request, redirect to contact page
    header("Location: contact.php");
    exit();
}
?>
