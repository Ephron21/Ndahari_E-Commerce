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
$valid_token = false;
$email = '';
$token = '';

// Verify token from URL
if (isset($_GET['token']) && isset($_GET['email'])) {
    $token = $_GET['token'];
    $email = $_GET['email'];
    
    try {
        // Check both employers and job_seekers tables
        $tables = ['employers', 'job_seekers'];
        $user = null;
        $user_type = '';
        
        foreach ($tables as $table) {
            $stmt = $pdo->prepare("
                SELECT id, email 
                FROM $table 
                WHERE email = ? 
                AND reset_token = ? 
                AND reset_token_expires > NOW()
            ");
            $stmt->execute([$email, $token]);
            $result = $stmt->fetch();
            
            if ($result) {
                $user = $result;
                $user_type = $table;
                $valid_token = true;
                break;
            }
        }
        
        if (!$valid_token) {
            if ($token === '' || $email === '') {
                $error_message = "Invalid reset link. Please make sure you clicked the complete link from your email.";
            } else {
                // Check if token is expired
                foreach ($tables as $table) {
                    $stmt = $pdo->prepare("
                        SELECT reset_token_expires 
                        FROM $table 
                        WHERE email = ? 
                        AND reset_token = ?
                    ");
                    $stmt->execute([$email, $token]);
                    $result = $stmt->fetch();
                    
                    if ($result) {
                        if (strtotime($result['reset_token_expires']) < time()) {
                            $error_message = "This reset link has expired. Please request a new one.";
                        } else {
                            $error_message = "Invalid reset link. Please request a new one.";
                        }
                        break;
                    }
                }
                
                if (!$error_message) {
                    $error_message = "Invalid reset link. Please request a new one.";
                }
            }
        }
    } catch (Exception $e) {
        error_log("Token verification error: " . $e->getMessage());
        $error_message = "An error occurred while verifying your reset link. Please try again.";
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate password
    if (strlen($password) < 8) {
        $error_message = "Password must be at least 8 characters long.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        try {
            // Update password and clear reset token
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                UPDATE $user_type 
                SET password = ?,
                    reset_token = NULL,
                    reset_token_expires = NULL
                WHERE email = ?
            ");
            
            if ($stmt->execute([$password_hash, $email])) {
                $success_message = "Your password has been successfully reset. You can now login with your new password.";
                // Redirect to login page after 2 seconds
                header("refresh:2;url=login.php");
            } else {
                throw new Exception("Failed to update password");
            }
        } catch (Exception $e) {
            error_log("Password reset error: " . $e->getMessage());
            $error_message = "An error occurred while resetting your password. Please try again.";
        }
    }
}

require_once 'includes/header.php';
?>

<main class="reset-password-page">
    <div class="container">
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if ($valid_token): ?>
            <div class="reset-password-content">
                <div class="reset-password-header">
                    <h1>Reset Password</h1>
                    <p class="text-muted">Enter your new password</p>
                </div>

                <form action="reset-password.php?token=<?php echo urlencode($token); ?>&email=<?php echo urlencode($email); ?>" 
                      method="POST" class="reset-password-form">
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" required 
                               minlength="8">
                        <small class="password-requirements">
                            Minimum 8 characters, including uppercase, lowercase, number
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" 
                               required minlength="8">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Reset Password</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</main>

<style>
.reset-password-page {
    padding: 2rem 0;
    background-color: #f8f9fa;
    min-height: calc(100vh - 60px);
    display: flex;
    align-items: center;
}

.reset-password-content {
    background: white;
    border-radius: 10px;
    padding: 2.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    max-width: 500px;
    margin: 0 auto;
    width: 100%;
}

.reset-password-header {
    text-align: center;
    margin-bottom: 2rem;
}

.reset-password-header h1 {
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

.password-requirements {
    display: block;
    font-size: 0.85rem;
    color: #666;
    margin-top: 0.5rem;
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