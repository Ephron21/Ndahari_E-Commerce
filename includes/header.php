<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ndahari</title>
    <link rel="stylesheet" href="public/css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body <?php echo isset($_SESSION['user_id']) ? 'data-logged-in="true"' : ''; ?>>
    <header class="site-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="index.php">
                        <h1>Ndahari</h1>
                        <img src="images/logo.png" alt="Ndahari Logo" onerror="this.onerror=null; this.src='images/placeholder-logo.png';">
                    </a>
                </div>
                
                <nav class="main-nav">
                    <ul class="nav-menu">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="find-jobs.php">Find Jobs</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle">My Account</a>
                                <ul class="dropdown-menu">
                                    <li><a href="dashboard.php">Dashboard</a></li>
                                    <li><a href="profile.php">Profile</a></li>
                                    <li><a href="messages.php">Messages</a></li>
                                    <li><a href="logout.php">Logout</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li><a href="login.php">Sign In</a></li>
                            <li><a href="register.php" class="btn btn-primary">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                
                <div class="mobile-menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </header>
    
    <script src="public/js/header.js"></script>