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

$response = ['status' => 'error', 'message' => 'Unknown error'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $companyName = cleanInput($_POST['company_name']);
    $industry = cleanInput($_POST['industry']);
    $companySize = cleanInput($_POST['company_size']);
    $description = cleanInput($_POST['company_description']);
    $contactPerson = cleanInput($_POST['contact_person']);
    $email = cleanInput($_POST['contact_email']);
    $password = $_POST['company_password'];
    $phone = cleanInput($_POST['contact_phone']);
    $website = cleanInput($_POST['company_website']);
    $province = cleanInput($_POST['company_province']);
    $district = cleanInput($_POST['company_district']);
    $sector = cleanInput($_POST['company_sector']);
    $cell = cleanInput($_POST['company_cell']);
    $street = cleanInput($_POST['company_street']);
    $terms = isset($_POST['terms_agreement']) ? 1 : 0;

    // Validate required fields
    if (empty($companyName)) $errors['company_name'] = 'Company name is required';
    if (empty($industry)) $errors['industry'] = 'Industry is required';
    if (empty($contactPerson)) $errors['contact_person'] = 'Contact person is required';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['contact_email'] = 'Invalid email address';
    if (strlen($password) < 8) $errors['company_password'] = 'Password must be at least 8 characters';
    if (!preg_match('/^\+?\d{10,15}$/', $phone)) $errors['contact_phone'] = 'Invalid phone number';
    if (empty($province)) $errors['company_province'] = 'Province is required';
    if (empty($district)) $errors['company_district'] = 'District is required';
    if (empty($sector)) $errors['company_sector'] = 'Sector is required';
    if (!$terms) $errors['terms_agreement'] = 'You must agree to the terms';

    // Handle file upload
    $logoPath = null;
    if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($fileInfo, $_FILES['company_logo']['tmp_name']);
        
        if (!in_array($mime, $allowedTypes)) {
            $errors['company_logo'] = 'Invalid file type. Only JPG, PNG, and GIF are allowed.';
        } elseif ($_FILES['company_logo']['size'] > $maxSize) {
            $errors['company_logo'] = 'File size exceeds 2MB limit';
        } else {
            $extension = pathinfo($_FILES['company_logo']['name'], PATHINFO_EXTENSION);
            $logoPath = 'uploads/' . uniqid() . '.' . $extension;
            move_uploaded_file($_FILES['company_logo']['tmp_name'], $logoPath);
        }
    }

    if (empty($errors)) {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM employers WHERE contact_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $errors['contact_email'] = 'Email already registered';
        } else {
            // Insert into database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO employers (
                company_name, company_logo, industry, company_size, company_description,
                contact_person, contact_email, password, contact_phone, website,
                province, district, sector, cell, street, terms_agreement
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->bind_param("sssssssssssssssi", 
                $companyName, $logoPath, $industry, $companySize, $description,
                $contactPerson, $email, $hashedPassword, $phone, $website,
                $province, $district, $sector, $cell, $street, $terms
            );
            
            if ($stmt->execute()) {
                $response = [
                    'status' => 'success',
                    'message' => 'Registration successful! Redirecting...'
                ];
            } else {
                $response['message'] = 'Database error: ' . $stmt->error;
            }
        }
        $stmt->close();
    } else {
        $response['errors'] = $errors;
        $response['message'] = 'Please correct the errors in the form';
    }
}

function cleanInput($data) {
    return htmlspecialchars(trim($data ?? ''));
}

header('Content-Type: application/json');
echo json_encode($response);
$conn->close();
?>