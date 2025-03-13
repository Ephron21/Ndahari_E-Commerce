<?php
session_start();

// Get user type before clearing session
$user_type = $_SESSION['user_type'] ?? 'job_seeker';
$table = ($user_type === 'employer') ? 'employers' : 'job_seekers';

// Clear remember me token from database if it exists
if (isset($_SESSION['user_id'])) {
    require_once 'includes/config.php';
    try {
        $stmt = $pdo->prepare("UPDATE $table SET remember_token = NULL WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    } catch (PDOException $e) {
        error_log("Error clearing remember token: " . $e->getMessage());
    }
}

// Remove remember me cookie if it exists
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to login page with success message
header("Location: login.php?logout=success");
exit(); 