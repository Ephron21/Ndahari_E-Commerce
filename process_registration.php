<?php
session_start();

// Database configuration
$db_host = "localhost";
$db_user = "root";
$db_pass = "Diano21@Esron21%";
$db_name = "ndahari";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

$response = [];
$errors = [];

// Process form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Personal Information
    $firstname = sanitizeInput($_POST['firstname']);
    $lastname = sanitizeInput($_POST['lastname']);
    $email = sanitizeInput($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = sanitizeInput($_POST['phone']);
    $age = intval($_POST['age']);
    $gender = sanitizeInput($_POST['gender']);

    // File Upload Handling
    $uploadDir = 'uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $idDoc = handleUpload('id_document', ['pdf', 'jpg', 'jpeg', 'png'], 2);
    $resume = handleUpload('resume', ['pdf'], 2);
    $profileImg = handleUpload('profile_image', ['jpg', 'jpeg', 'png'], 1);

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO job_seeker (firstname, lastname, email, password, phone, age, gender, id_document, resume, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssissss", $firstname, $lastname, $email, $password, $phone, $age, $gender, $idDoc, $resume, $profileImg);

    if ($stmt->execute()) {
        $response = ['status' => 'success', 'message' => 'Registration successful!'];
    } else {
        $response = ['status' => 'error', 'message' => 'Registration failed: ' . $stmt->error];
    }

    $stmt->close();
}

function handleUpload($field, $allowedTypes, $maxSizeMB) {
    global $errors;
    if (!isset($_FILES[$field])) return null;

    $file = $_FILES[$field];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $maxSize = $maxSizeMB * 1024 * 1024;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[$field] = 'File upload error';
        return null;
    }

    if (!in_array($ext, $allowedTypes)) {
        $errors[$field] = 'Invalid file type';
        return null;
    }

    if ($file['size'] > $maxSize) {
        $errors[$field] = "File too large (max {$maxSizeMB}MB)";
        return null;
    }

    $filename = uniqid() . '.' . $ext;
    move_uploaded_file($file['tmp_name'], 'uploads/' . $filename);
    return $filename;
}

header('Content-Type: application/json');
echo json_encode($response);
$conn->close();
?>