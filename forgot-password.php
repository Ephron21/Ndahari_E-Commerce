<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email']);
    
    try {
        // Check both employers and job_seekers tables
        $tables = ['employers', 'job_seekers'];
        $user = null;
        $user_type = '';
        
        foreach ($tables as $table) {
            $stmt = $pdo->prepare("SELECT id, email, full_name FROM $table WHERE email = ?");
            if ($table === 'employers') {
                $stmt = $pdo->prepare("SELECT id, email, contact_person as full_name FROM $table WHERE email = ?");
            }
            $stmt->execute([$email]);
            $result = $stmt->fetch();
            
            if ($result) {
                $user = $result;
                $user_type = $table;
                break;
            }
        }
        
        if ($user) {
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store reset token in database
            $stmt = $pdo->prepare("UPDATE $user_type SET 
                reset_token = ?,
                reset_token_expires = ?
                WHERE id = ?");
            
            if ($stmt->execute([$token, $expires, $user['id']])) {
                // Send reset email
                $reset_link = SITE_URL . "/reset-password.php?token=" . urlencode($token) . "&email=" . urlencode($email);
                $to = $email;
                $subject = "Password Reset Request";
                $message = "
                <html>
                <head>
                    <title>Password Reset Request</title>
                </head>
                <body>
                    <h2>Password Reset Request</h2>
                    <p>Dear {$user['full_name']},</p>
                    <p>We received a request to reset your password. Click the link below to set a new password:</p>
                    <p><a href='$reset_link'>Reset Your Password</a></p>
                    <p>This link will expire in 1 hour.</p>
                    <p>If you didn't request this, please ignore this email.</p>
                    <p>Best regards,<br>Ndahari Team</p>
                </body>
                </html>
                ";
                
                // Headers for HTML email
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= 'From: Ndahari <noreply@ndahari.com>' . "\r\n";
                
                if(mail($to, $subject, $message, $headers)) {
                    $success_message = "Password reset instructions have been sent to your email.";
                } else {
                    // Log mail error for debugging
                    error_log("Failed to send password reset email to: $email");
                    throw new Exception("Failed to send reset email");
                }
            } else {
                // Log database error for debugging
                error_log("Failed to update reset token for user ID: " . $user['id']);
                throw new Exception("Failed to process reset request");
            }
        } else {
            // Don't reveal whether the email exists or not
            $success_message = "If your email exists in our system, you will receive password reset instructions.";
        }
    } catch (Exception $e) {
        error_log("Password reset error: " . $e->getMessage());
        $error_message = "An error occurred while processing your request. Please try again later.";
    }
}

require_once 'includes/header.php';
?>

<main class="forgot-password-page">
    <div class="container">
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <div class="forgot-password-content">
            <div class="forgot-password-header">
                <h1>Forgot Password</h1>
                <p class="text-muted">Enter your email address to reset your password</p>
            </div>

            <form action="forgot-password.php" method="POST" class="forgot-password-form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required 
                           placeholder="Enter your registered email">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Send Reset Link</button>
                    <p class="login-link">
                        Remember your password? <a href="login.php">Login here</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</main>

<style>
.forgot-password-page {
    padding: 2rem 0;
    background-color: #f8f9fa;
    min-height: calc(100vh - 60px);
    display: flex;
    align-items: center;
}

.forgot-password-content {
    background: white;
    border-radius: 10px;
    padding: 2.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    max-width: 500px;
    margin: 0 auto;
    width: 100%;
}

.forgot-password-header {
    text-align: center;
    margin-bottom: 2rem;
}

.forgot-password-header h1 {
    font-size: 2rem;
    color: #333;
    margin-bottom: 0.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #333;
    font-weight: 500;
}

.form-group input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-group input:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
}

.form-actions {
    text-align: center;
}

.btn {
    padding: 0.75rem 2rem;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
    transform: translateY(-2px);
}

.login-link {
    margin-top: 1.5rem;
    color: #666;
    font-size: 0.9rem;
}

.login-link a {
    color: #007bff;
    text-decoration: none;
}

.login-link a:hover {
    text-decoration: underline;
}

.alert {
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 4px;
    text-align: center;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

<?php require_once 'includes/footer.php'; ?> 