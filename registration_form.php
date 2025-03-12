<?php
// Start session
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'includes/db_connection.php';

// Debug: Check if constants are defined
if (!defined('DB_HOST') || !defined('DB_USER') || !defined('DB_PASS') || !defined('DB_NAME')) {
    die("Database constants are not defined. Please check db_connection.php.");
}

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to upload file
function upload_file($file, $target_dir, $allowed_extensions, $max_size) {
    // Create target directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Get file extension
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Check if file extension is allowed
    if (!in_array($file_extension, $allowed_extensions)) {
        return array('error' => 'Sorry, only ' . implode(', ', $allowed_extensions) . ' files are allowed.');
    }
    
    // Check file size
    if ($file['size'] > $max_size) {
        return array('error' => 'Sorry, your file is too large. Maximum size is ' . ($max_size / (1024 * 1024)) . 'MB.');
    }
    
    // Generate unique filename
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return array('success' => true, 'filename' => $new_filename);
    } else {
        return array('error' => 'Sorry, there was an error uploading your file.');
    }
}

// Check if form was submitted
if (isset($_POST['submit'])) {
    
    // Get user type
    $user_type = sanitize_input($_POST['user_type']);
    
    // Connect to database
    $conn = get_db_connection();
    
    // Process job seeker registration
    if ($user_type === 'job_seeker') {
        
        // Sanitize form data
        $firstname = sanitize_input($_POST['firstname']);
        $lastname = sanitize_input($_POST['lastname']);
        $email = sanitize_input($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $phone = sanitize_input($_POST['phone']);
        $gender = sanitize_input($_POST['gender']);
        $age = (int)sanitize_input($_POST['age']);
        $job_title = sanitize_input($_POST['job_title']);
        $skills = sanitize_input($_POST['skills']);
        $certifications = isset($_POST['certifications']) ? sanitize_input($_POST['certifications']) : '';
        $province = sanitize_input($_POST['province']);
        $district = sanitize_input($_POST['district']);
        $sector = sanitize_input($_POST['sector']);
        $cell = sanitize_input($_POST['cell']);
        $village = sanitize_input($_POST['village']);
        $availability_notes = isset($_POST['availability_notes']) ? sanitize_input($_POST['availability_notes']) : '';
        
        // Check if availability was selected
        if (!isset($_POST['availability']) || !is_array($_POST['availability']) || empty($_POST['availability'])) {
            $_SESSION['error'] = "Please select at least one availability option.";
            header("Location: signup.php");
            exit();
        }
        
        // Sanitize availability array
        $availability = array_map('sanitize_input', $_POST['availability']);
        
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $_SESSION['error'] = "Email already exists. Please use a different email address.";
            header("Location: signup.php");
            exit();
        }
        
        // Upload files
        $id_document_result = upload_file(
            $_FILES['id_document'],
            'uploads/documents/',
            array('pdf', 'jpg', 'jpeg', 'png'),
            2 * 1024 * 1024 // 2MB
        );
        
        $resume_result = upload_file(
            $_FILES['resume'],
            'uploads/resumes/',
            array('pdf'),
            2 * 1024 * 1024 // 2MB
        );
        
        $profile_image_result = upload_file(
            $_FILES['profile_image'],
            'uploads/profile_images/',
            array('jpg', 'jpeg', 'png'),
            1 * 1024 * 1024 // 1MB
        );
        
        // Check if file uploads were successful
        if (isset($id_document_result['error'])) {
            $_SESSION['error'] = $id_document_result['error'];
            header("Location: signup.php");
            exit();
        }
        
        if (isset($resume_result['error'])) {
            $_SESSION['error'] = $resume_result['error'];
            header("Location: signup.php");
            exit();
        }
        
        if (isset($profile_image_result['error'])) {
            $_SESSION['error'] = $profile_image_result['error'];
            header("Location: signup.php");
            exit();
        }
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Insert into users table
            $stmt = $conn->prepare("INSERT INTO users (email, password, user_type, status) VALUES (?, ?, 'job_seeker', 'pending')");
            $stmt->bind_param("ss", $email, $password);
            $stmt->execute();
            echo "User inserted successfully!";
            
            // Get the user ID
            $user_id = $conn->insert_id;
            
            // Insert into job_seekers table
            $stmt = $conn->prepare("INSERT INTO job_seekers (user_id, firstname, lastname, phone, gender, age, job_title, skills, certifications, id_document, resume, profile_image, province, district, sector, cell, village) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssisssssssssss", $user_id, $firstname, $lastname, $phone, $gender, $age, $job_title, $skills, $certifications, $id_document_result['filename'], $resume_result['filename'], $profile_image_result['filename'], $province, $district, $sector, $cell, $village);
            $stmt->execute();
            echo "Job seeker inserted successfully!";
            
            // Get the seeker ID
            $seeker_id = $conn->insert_id;
            
            // Insert into seeker_availability table
            foreach ($availability as $availability_type) {
                $stmt = $conn->prepare("INSERT INTO seeker_availability (seeker_id, availability_type) VALUES (?, ?)");
                $stmt->bind_param("is", $seeker_id, $availability_type);
                $stmt->execute();
                echo "Availability inserted successfully!";
            }
            
            // Insert skills into seeker_skills table
            $skill_array = explode(',', $skills);
            foreach ($skill_array as $skill) {
                $skill = trim($skill);
                if (!empty($skill)) {
                    $stmt = $conn->prepare("INSERT INTO seeker_skills (seeker_id, skill_name) VALUES (?, ?)");
                    $stmt->bind_param("is", $seeker_id, $skill);
                    $stmt->execute();
                    echo "Skill inserted successfully!";
                }
            }
            
            // Commit transaction
            $conn->commit();
            echo "Transaction committed successfully!";
            
            // Set success message
            $_SESSION['success'] = "Registration successful! Your account is pending approval.";
            
            // Redirect to login page
            header("Location: signin.php");
            exit();
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            echo "Error: " . $e->getMessage();
            exit();
        }
    }
    // Process employer registration
    else if ($user_type === 'employer') {
        
        // Sanitize form data
        $company_name = sanitize_input($_POST['company_name']);
        $industry = sanitize_input($_POST['industry']);
        $company_size = sanitize_input($_POST['company_size']);
        $company_description = sanitize_input($_POST['company_description']);
        $contact_person = sanitize_input($_POST['contact_person']);
        $contact_email = sanitize_input($_POST['contact_email']);
        $password = password_hash($_POST['company_password'], PASSWORD_DEFAULT);
        $contact_phone = sanitize_input($_POST['contact_phone']);
        $company_website = isset($_POST['company_website']) ? sanitize_input($_POST['company_website']) : '';
        $province = sanitize_input($_POST['company_province']);
        $district = sanitize_input($_POST['company_district']);
        $sector = sanitize_input($_POST['company_sector']);
        $cell = sanitize_input($_POST['company_cell']);
        $village = sanitize_input($_POST['company_village']);
        
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $contact_email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $_SESSION['error'] = "Email already exists. Please use a different email address.";
            header("Location: signup.php");
            exit();
        }
        
        // Upload files
        $company_registration_result = upload_file(
            $_FILES['company_registration'],
            'uploads/company_documents/',
            array('pdf'),
            2 * 1024 * 1024 // 2MB
        );
        
        $company_logo_result = upload_file(
            $_FILES['company_logo'],
            'uploads/company_logos/',
            array('jpg', 'jpeg', 'png'),
            1 * 1024 * 1024 // 1MB
        );
        
        // Check if file uploads were successful
        if (isset($company_registration_result['error'])) {
            $_SESSION['error'] = $company_registration_result['error'];
            header("Location: signup.php");
            exit();
        }
        
        if (isset($company_logo_result['error'])) {
            $_SESSION['error'] = $company_logo_result['error'];
            header("Location: signup.php");
            exit();
        }
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Insert into users table
            $stmt = $conn->prepare("INSERT INTO users (email, password, user_type, status) VALUES (?, ?, 'employer', 'pending')");
            $stmt->bind_param("ss", $contact_email, $password);
            $stmt->execute();
            echo "User inserted successfully!";
            
            // Get the user ID
            $user_id = $conn->insert_id;
            
            // Insert into employers table
            $stmt = $conn->prepare("INSERT INTO employers (user_id, company_name, industry, company_size, company_description, contact_person, contact_phone, company_website, company_registration, company_logo, province, district, sector, cell, village) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssssssssssss", $user_id, $company_name, $industry, $company_size, $company_description, $contact_person, $contact_phone, $company_website, $company_registration_result['filename'], $company_logo_result['filename'], $province, $district, $sector, $cell, $village);
            $stmt->execute();
            echo "Employer inserted successfully!";
            
            // Commit transaction
            $conn->commit();
            echo "Transaction committed successfully!";
            
            // Set success message
            $_SESSION['success'] = "Registration successful! Your company account is pending approval.";
            
            // Redirect to login page
            header("Location: signin.php");
            exit();
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            echo "Error: " . $e->getMessage();
            exit();
        }
    }
    else {
        $_SESSION['error'] = "Invalid user type.";
        header("Location: signup.php");
        exit();
    }
    
    // Close database connection
    $conn->close();
}
else {
    // Redirect to signup page if form was not submitted
    header("Location: signup.php");
    exit();
}
?>