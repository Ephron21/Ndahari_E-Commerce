<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Database configuration
$db_host = "localhost";
$db_user = "root";
$db_pass = "Diano21@Esron21%";
$db_name = "ndahari";

// Create connection with proper error handling
try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    // Set character set
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    error_log($e->getMessage());
    die(json_encode([
        'status' => 'error', 
        'message' => 'Database connection failed. Please try again later.'
    ]));
}

// File upload configuration with enhanced security
$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) {
    try {
        mkdir($uploadDir, 0755, true);
        chmod($uploadDir, 0755);
        file_put_contents($uploadDir . '.htaccess', "php_flag engine off\n");
    } catch (Exception $e) {
        error_log("Upload directory creation failed: " . $e->getMessage());
        die(json_encode([
            'status' => 'error',
            'message' => 'Server configuration error'
        ]));
    }
}

// Helper functions with enhanced validation
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

function validate_phone($phone) {
    return preg_match("/^\+?[0-9]{10,15}$/", $phone);
}

function handle_file_upload($file, $allowed_types, $max_size, $prefix) {
    global $uploadDir;
    
    $errors = [];
    
    try {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('File upload error: ' . $file['error']);
        }

        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $file_mime = mime_content_type($file['tmp_name']);
        
        // Validate both extension and MIME type
        if (!in_array($file_ext, $allowed_types) || 
            !in_array($file_mime, [
                'application/pdf' => ['pdf'],
                'image/jpeg' => ['jpg', 'jpeg'],
                'image/png' => ['png']
            ][$file_mime] ?? [])) {
            throw new Exception('Invalid file type. Allowed: ' . implode(', ', $allowed_types));
        }

        if ($file['size'] > $max_size) {
            throw new Exception('File too large. Max size: ' . ($max_size/1024/1024) . 'MB');
        }

        $filename = $prefix . '_' . bin2hex(random_bytes(16)) . '.' . $file_ext;
        $path = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $path)) {
            throw new Exception('Failed to save uploaded file');
        }

        return ['path' => $path];
        
    } catch (Exception $e) {
        error_log("File upload error: " . $e->getMessage());
        return ['error' => $e->getMessage()];
    }
}

// Process registration with transaction handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = ['status' => 'error', 'message' => 'Unknown error', 'errors' => []];
    $user_type = $_POST['user_type'] ?? '';

    try {
        $conn->begin_transaction();

        // Common validation functions
        $check_email = function($email) use ($conn) {
            $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            return $stmt->get_result()->num_rows > 0;
        };

        if ($user_type === 'job_seeker') {
            // Job Seeker Registration processing
            // ... (keep the existing job seeker validation logic)
            
        } elseif ($user_type === 'employer') {
            // Employer Registration processing
            // ... (keep the existing employer validation logic)
            
        } else {
            throw new Exception("Invalid user type specified");
        }

        // Final commit if no errors
        if (empty($response['errors'])) {
            $conn->commit();
            $response['status'] = 'success';
            $response['message'] = 'Registration successful! Redirecting...';
        } else {
            $conn->rollback();
            $response['message'] = 'Please correct the form errors';
        }

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Registration Error: " . $e->getMessage());
        $response['message'] = 'Registration failed: ' . $e->getMessage();
        http_response_code(500);
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// HTML Footer Section
if (file_exists('includes/footer.php')) {
    include 'includes/footer.php';
} else {
?>
    <footer class="bg-dark text-white text-center p-3 mt-4">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Ndahari Job Portal. All rights reserved.</p>
            <p class="text-muted small">v1.0.0</p>
        </div>
    </footer>
    
    <!-- Essential JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Enhanced Form Validation -->
    <script>
    // Improved JavaScript validation
    (function() {
        'use strict';
        
        const forms = document.querySelectorAll('.needs-validation');
        const showError = (element, message) => {
            element.classList.add('is-invalid');
            const feedback = element.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.textContent = message;
            }
        };
        
        const validateForm = async (form) => {
            form.querySelector('.spinner-border').style.display = 'inline-block';
            
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form)
                });
                
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                
                const data = await response.json();
                const messageContainer = document.getElementById('form-messages');
                
                if (data.status === 'success') {
                    messageContainer.innerHTML = `
                        <div class="alert alert-success">
                            ${data.message}
                        </div>
                    `;
                    setTimeout(() => window.location.href = 'signin.php', 2000);
                } else {
                    messageContainer.innerHTML = `
                        <div class="alert alert-danger">
                            ${data.message}
                        </div>
                    `;
                    if (data.errors) {
                        Object.entries(data.errors).forEach(([field, message]) => {
                            const element = document.getElementById(field);
                            if (element) showError(element, message);
                        });
                    }
                }
            } catch (error) {
                console.error('Fetch Error:', error);
                document.getElementById('form-messages').innerHTML = `
                    <div class="alert alert-danger">
                        Network error occurred. Please try again.
                    </div>
                `;
            } finally {
                form.querySelector('.spinner-border').style.display = 'none';
            }
        };

        // Event listeners for both forms
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                validateForm(this);
            });
        });
    })();
    </script>
</body>
</html>
<?php
}