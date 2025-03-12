<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ndahari</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .site-header{
            background-color: #2c3e50;
    color: white;
    padding: 15px 0;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 50px;
            margin-right: 10px;
        }

        .logo h1 {
            margin: 0;
            font-size: 24px;
            color: #fff; /* White text color for logo */
        }

        .main-nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .main-nav ul li {
            margin-left: 20px;
            position: relative;
        }

        .main-nav ul li a {
            color: #fff; /* White text color for links */
            text-decoration: none;
            padding: 10px 15px;
            display: block;
        }

        .main-nav ul li a:hover {
            background-color: #555; /* Darker background on hover */
            border-radius: 5px;
        }

        /* Dropdown Styles */
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #333; /* Dark background for dropdown */
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 5px;
        }

        .dropdown-content a {
            color: #fff; /* White text color for dropdown links */
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #555; /* Darker background on hover */
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        /* Responsive Navbar */
        .nav-toggle {
            display: none;
        }

        .nav-toggle-label {
            display: none;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .nav-toggle-label {
                display: block;
            }

            .main-nav ul {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 60px;
                right: 0;
                background-color: #333; /* Dark background for mobile menu */
                width: 100%;
            }

            .main-nav ul li {
                margin: 0;
            }

            .main-nav ul li a {
                padding: 15px;
            }

            .nav-toggle:checked ~ ul {
                display: flex;
            }
        }
    </style>
    <script>
        // JavaScript for additional interactivity (if needed)
        document.addEventListener("DOMContentLoaded", function () {
            // Add any JavaScript functionality here
        });
    </script>
</head>
<body>
    <header class="site-header">
        <div class="container">
            <div class="logo">
                <a href="index.php">
                <h1>Ndahari</h1>
                    <img src="public/images/logo.png" alt="Ndahari Logo">
                   
                </a>
            </div>
            <nav class="main-nav">
                <input type="checkbox" id="nav-toggle" class="nav-toggle">
                <label for="nav-toggle" class="nav-toggle-label">
                    <span></span>
                </label>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="services.php">Services</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropbtn">Register</a>
                        <div class="dropdown-content">
                            <a href="signup.php">Job Seeker</a>
                            <a href="employer_registration.php">Employer</a>
                        </div>
                    </li>
                    <li><a href="Contact.php">Contact</a></li>
                    <li><a href="signin.php">Sign In</a></li>
                    <li><a href="admin_login.php">CSM</a></li>
                </ul>
            </nav>
        </div>
    </header>
</body>
</html>