<?php
// Start session
session_start();

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Check if header file exists and include it, otherwise create a basic HTML header
if (file_exists('includes/header.php')) {
    include 'includes/header.php';
} else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Ndahari Job Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #007bff, #00ff88);
            color: #fff;
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }
        .login-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            background: rgba(255, 255, 255, 0.9);
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .btn {
            padding: 8px 20px;
            border-radius: 4px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
            transition: background-color 0.3s ease-in-out;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .back-to-home {
            text-align: center;
            margin-top: 20px;
        }
        .back-to-home a {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s ease-in-out;
        }
        .back-to-home a:hover {
            background-color: #0056b3;
        }
        .moving-text {
            white-space: nowrap;
            overflow: hidden;
            box-sizing: border-box;
            animation: moveText 10s linear infinite;
        }
        @keyframes moveText {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        .animated-image {
            text-align: center;
            margin-bottom: 20px;
        }
        .animated-image img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            animation: bounce 2s infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body>
    <header class="bg-dark text-white p-3">
        <div class="container">
            <h1 class="animate__animated animate__fadeInDown">Ndahari Job Portal</h1>
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="signin.php">Sign In</a></li>
                    <li class="nav-item"><a class="nav-link" href="signup.php">Sign Up</a></li>
                </ul>
            </nav>
        </div>
    </header>
<?php
}
?>

<div class="container">
    <h2 class="text-center mb-4 animate__animated animate__fadeIn">Sign In</h2>
    
    <?php
    // Display error message if any
    if(isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger animate__animated animate__shakeX">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }
    
    // Display success message if any
    if(isset($_SESSION['success'])) {
        echo '<div class="alert alert-success animate__animated animate__fadeIn">' . $_SESSION['success'] . '</div>';
        unset($_SESSION['success']);
    }
    ?>
    
    <div class="login-container animate__animated animate__fadeInUp">
        <div class="animated-image">
            <img src="https://via.placeholder.com/100" alt="Welcome Image" class="animate__animated animate__bounce">
        </div>
        <div class="mb-4">
            <div class="text-center">
                <h4>Login to Your Account</h4>
                <p class="moving-text">Welcome to Ndahari Job Portal! Find your dream job or hire the best talent.</p>
            </div>
        </div>
        
        <form action="process_login.php" method="post">
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>Account Type *</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="user_type" id="job_seeker" value="job_seeker" checked>
                    <label class="form-check-label" for="job_seeker">
                        Job Seeker
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="user_type" id="employer" value="employer">
                    <label class="form-check-label" for="employer">
                        Employer
                    </label>
                </div>
            </div>
            
            <div class="text-end mb-3">
                <a href="forgot_password.php">Forgot Password?</a>
            </div>
            
            <div class="d-grid">
                <button type="submit" name="login" class="btn btn-primary animate__animated animate__pulse">Sign In</button>
            </div>
        </form>
        
        <div class="text-center mt-3">
            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
        </div>
    </div>
    
    <!-- Back to Home Button -->
    <div class="back-to-home">
        <a href="index.php">Back to Home</a>
    </div>
</div>

<?php
// Check if footer file exists and include it, otherwise create a basic HTML footer
if (file_exists('includes/footer.php')) {
    include 'includes/footer.php';
} else {
?>
    <footer class="bg-dark text-white text-center p-3 mt-4">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Ndahari Job Portal. All rights reserved.</p>
        </div>
    </footer>
    </body>
    </html>
<?php
}
?>