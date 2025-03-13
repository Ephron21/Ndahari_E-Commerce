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
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $user_type = sanitize_input($_POST['user_type'] ?? 'job_seeker'); // Default to job_seeker if not set
    $remember_me = isset($_POST['remember_me']);

    // Validate required fields
    if (empty($email) || empty($password)) {
        $error_message = "Please fill in all required fields.";
    } else {
        try {
            // Get user from appropriate table based on user type
            $table = ($user_type === 'employer') ? 'employers' : 'job_seekers';
            $name_field = ($user_type === 'employer') ? 'contact_person' : 'full_name';
            
            $stmt = $pdo->prepare("SELECT * FROM $table WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_type'] = $user_type;
                $_SESSION['name'] = $user[$name_field];
                $_SESSION['email'] = $user['email'];

                // Update last login timestamp
                $update_stmt = $pdo->prepare("UPDATE $table SET last_login = NOW() WHERE id = ?");
                $update_stmt->execute([$user['id']]);

                // Set remember me cookie if checked
                if ($remember_me) {
                    $token = bin2hex(random_bytes(32));
                    $expires = time() + (30 * 24 * 60 * 60); // 30 days

                    // Store token in database
                    $stmt = $pdo->prepare("UPDATE $table SET remember_token = ? WHERE id = ?");
                    $stmt->execute([$token, $user['id']]);

                    // Set cookie
                    setcookie('remember_token', $token, $expires, '/', '', true, true);
                }

                // Redirect to dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                $error_message = "Invalid email or password";
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $error_message = "An error occurred during login. Please try again.";
        }
    }
} else {
    // Check for logout success message
    if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
        $success_message = "You have been successfully logged out.";
    }
}

// Include header
require_once 'includes/header.php';
?>

<main class="login-page">
    <div class="container">
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <div class="login-content">
            <div class="login-header">
                <h1>Welcome Back</h1>
                <p class="text-muted">Sign in to continue to your account</p>
            </div>

            <form action="login.php" method="POST" class="login-form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-input-group">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="toggle-password" aria-label="Toggle password visibility">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label>Account Type</label>
                    <div class="user-type-selection">
                        <div class="form-check">
                            <input type="radio" id="job_seeker" name="user_type" value="job_seeker" class="form-check-input" 
                                   <?php echo (!isset($_POST['user_type']) || $_POST['user_type'] === 'job_seeker') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="job_seeker">Job Seeker</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" id="employer" name="user_type" value="employer" class="form-check-input"
                                   <?php echo (isset($_POST['user_type']) && $_POST['user_type'] === 'employer') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="employer">Employer</label>
                        </div>
                    </div>
                </div>

                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember_me" name="remember_me">
                        <label for="remember_me">Remember me</label>
                    </div>
                    <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Sign In</button>
                    <p class="register-link">
                        Don't have an account? <a href="register.php">Register here</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</main>

<style>
.login-page {
    padding: 2rem 0;
    background-color: #f8f9fa;
    min-height: calc(100vh - 60px);
    display: flex;
    align-items: center;
}

.login-content {
    background: white;
    border-radius: 10px;
    padding: 2.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    max-width: 500px;
    margin: 0 auto;
    width: 100%;
}

.login-header {
    text-align: center;
    margin-bottom: 2rem;
}

.login-header h1 {
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

.password-input-group {
    position: relative;
}

.toggle-password {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #666;
    cursor: pointer;
}

.user-type-selection {
    display: flex;
    gap: 2rem;
    margin-top: 0.5rem;
}

.form-check {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.remember-me {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.forgot-password {
    color: #007bff;
    text-decoration: none;
}

.forgot-password:hover {
    text-decoration: underline;
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

.register-link {
    margin-top: 1.5rem;
    color: #666;
}

.register-link a {
    color: #007bff;
    text-decoration: none;
}

.register-link a:hover {
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.querySelector('.toggle-password');
    const passwordInput = document.querySelector('#password');

    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Toggle eye icon
        const icon = this.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });

    // Form validation
    const loginForm = document.querySelector('.login-form');
    loginForm.addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        if (!email || !password) {
            e.preventDefault();
            alert('Please fill in all required fields');
            return;
        }

        // Basic email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Please enter a valid email address');
            return;
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?> 