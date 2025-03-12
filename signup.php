<?php
// Start session
session_start();

// Database connection
$db_host = "localhost";
$db_user = "root"; // Change to your database username
$db_pass = "Diano21@Esron21%"; // Change to your database password
$db_name = "ndahari"; // Change to your database name

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
    <title>Ndahari Job Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }
        fieldset {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        legend {
            font-weight: bold;
            padding: 0 10px;
            width: auto;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .radio-group {
            display: flex;
            gap: 15px;
        }
        .submit-group {
            margin-top: 20px;
        }
        .btn {
            padding: 8px 20px;
            border-radius: 4px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
        }

        /* Back to Home Button */
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

        /* Toggle tabs for user types */
        .registration-type {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        
        .registration-type .btn {
            margin: 0 10px;
        }
        
        .registration-type .btn.active {
            background-color: #0056b3;
            font-weight: bold;
        }
        
        #employer-form {
            display: none;
        }

        /* Form validation styling */
        .is-invalid {
            border-color: #dc3545;
        }
        
        .invalid-feedback {
            color: #dc3545;
            display: block;
            margin-top: 5px;
            font-size: 0.875rem;
        }
        
        /* Form success message */
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        /* Form error message */
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        /* Loading spinner */
        .spinner-border {
            display: none;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <header class="bg-dark text-white p-3">
        <div class="container">
            <h1>Ndahari Job Portal</h1>
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="signin.php">Sign In</a></li>
                        <li class="nav-item"><a class="nav-link" href="signup.php">Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
<?php
}
?>

<div class="container">
    <h2 class="text-center mb-4">Create an Account</h2>
    
    <!-- Success or Error messages will appear here -->
    <div id="form-messages"></div>
    
    <div class="registration-type">
        <button id="job-seeker-btn" class="btn btn-primary active">Job Seeker</button>
        <button id="employer-btn" class="btn btn-primary">Employer</button>
    </div>
    
    <!-- Job Seeker Registration Form -->
    <form id="job-seeker-form" action="process_registration.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="user_type" value="job_seeker">
        
        <!-- Personal Information -->
        <fieldset>
            <legend>Personal Information</legend>
            
            <div class="form-group">
                <label for="firstname">First Name *</label>
                <input type="text" name="firstname" id="firstname" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="lastname">Last Name *</label>
                <input type="text" name="lastname" id="lastname" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" name="email" id="email" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" name="password" id="password" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password *</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number *</label>
                <input type="tel" name="phone" id="phone" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="age">Age *</label>
                <input type="number" name="age" id="age" class="form-control" min="18" max="100" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label>Gender *</label>
                <div class="radio-group">
                    <label><input type="radio" name="gender" value="male" required> Male</label>
                    <label><input type="radio" name="gender" value="female"> Female</label>
                    <label><input type="radio" name="gender" value="other"> Other</label>
                </div>
                <div class="invalid-feedback"></div>
            </div>
        </fieldset>

        <!-- Professional Information -->
        <fieldset>
            <legend>Professional Information</legend>
            
            <div class="form-group">
                <label for="job_title">Current/Desired Job Title *</label>
                <input type="text" name="job_title" id="job_title" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="skills">Skills (comma separated) *</label>
                <textarea name="skills" id="skills" class="form-control" rows="3" required></textarea>
                <small class="form-text text-muted">Example: Web Development, Project Management, Communication</small>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="certifications">Certifications</label>
                <textarea name="certifications" id="certifications" class="form-control" rows="3"></textarea>
                <small class="form-text text-muted">Example: AWS Certified, PMP, CCNA</small>
            </div>
        </fieldset>
        
        <!-- Availability -->
        <fieldset>
            <legend>Availability</legend>
            
            <div class="form-group">
                <label>Available Work Schedule *</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="availability[]" value="full_time" id="full_time">
                    <label class="form-check-label" for="full_time">Full Time</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="availability[]" value="part_time" id="part_time">
                    <label class="form-check-label" for="part_time">Part Time</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="availability[]" value="evenings" id="evenings">
                    <label class="form-check-label" for="evenings">Evenings</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="availability[]" value="weekends" id="weekends">
                    <label class="form-check-label" for="weekends">Weekends</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="availability[]" value="flexible" id="flexible">
                    <label class="form-check-label" for="flexible">Flexible</label>
                </div>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="availability_notes">Additional Availability Notes</label>
                <textarea name="availability_notes" id="availability_notes" class="form-control" rows="2"></textarea>
            </div>
        </fieldset>
        
        <!-- Address Information -->
        <fieldset>
            <legend>Address Information</legend>
            
            <div class="form-group">
                <label for="province">Province *</label>
                <input type="text" name="province" id="province" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="district">District *</label>
                <input type="text" name="district" id="district" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="sector">Sector *</label>
                <input type="text" name="sector" id="sector" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="cell">Cell *</label>
                <input type="text" name="cell" id="cell" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="village">Village *</label>
                <input type="text" name="village" id="village" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
        </fieldset>
        
        <!-- Documents Upload -->
        <fieldset>
            <legend>Documents</legend>
            
            <div class="form-group">
                <label for="id_document">ID Document (PDF, JPG, PNG) *</label>
                <input type="file" name="id_document" id="id_document" class="form-control" required accept=".pdf,.jpg,.jpeg,.png">
                <small class="form-text text-muted">Maximum file size: 2MB</small>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="resume">Resume/CV (PDF) *</label>
                <input type="file" name="resume" id="resume" class="form-control" required accept=".pdf">
                <small class="form-text text-muted">Maximum file size: 2MB</small>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="profile_image">Profile Image (JPG, PNG) *</label>
                <input type="file" name="profile_image" id="profile_image" class="form-control" required accept=".jpg,.jpeg,.png">
                <small class="form-text text-muted">Maximum file size: 1MB</small>
                <div class="invalid-feedback"></div>
            </div>
        </fieldset>
        
        <div class="form-group submit-group">
            <button type="submit" name="submit" class="btn btn-primary">
                Create Job Seeker Account
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </button>
            <button type="reset" class="btn btn-secondary">Reset Form</button>
        </div>
    </form>

    <!-- Employer Registration Form -->
    <form id="employer-form" action="process_registration.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="user_type" value="employer">
        
        <!-- Company Information -->
        <fieldset>
            <legend>Company Information</legend>
            
            <div class="form-group">
                <label for="company_name">Company Name *</label>
                <input type="text" name="company_name" id="company_name" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="industry">Industry *</label>
                <select name="industry" id="industry" class="form-control" required>
                    <option value="">Select Industry</option>
                    <option value="agriculture">Agriculture</option>
                    <option value="construction">Construction</option>
                    <option value="education">Education</option>
                    <option value="finance">Finance & Banking</option>
                    <option value="healthcare">Healthcare</option>
                    <option value="hospitality">Hospitality & Tourism</option>
                    <option value="information_technology">Information Technology</option>
                    <option value="manufacturing">Manufacturing</option>
                    <option value="retail">Retail</option>
                    <option value="telecommunications">Telecommunications</option>
                    <option value="transport">Transport & Logistics</option>
                    <option value="other">Other</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="company_size">Company Size *</label>
                <select name="company_size" id="company_size" class="form-control" required>
                    <option value="">Select Company Size</option>
                    <option value="1-10">1-10 employees</option>
                    <option value="11-50">11-50 employees</option>
                    <option value="51-200">51-200 employees</option>
                    <option value="201-500">201-500 employees</option>
                    <option value="501-1000">501-1000 employees</option>
                    <option value="1000+">1000+ employees</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="company_description">Company Description *</label>
                <textarea name="company_description" id="company_description" class="form-control" rows="4" required></textarea>
                <div class="invalid-feedback"></div>
            </div>
        </fieldset>
        
        <!-- Contact Information -->
        <fieldset>
            <legend>Contact Information</legend>
            
            <div class="form-group">
                <label for="contact_person">Contact Person Name *</label>
                <input type="text" name="contact_person" id="contact_person" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="contact_email">Email Address *</label>
                <input type="email" name="contact_email" id="contact_email" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="company_password">Password *</label>
                <input type="password" name="company_password" id="company_password" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="company_confirm_password">Confirm Password *</label>
                <input type="password" name="company_confirm_password" id="company_confirm_password" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="contact_phone">Phone Number *</label>
                <input type="tel" name="contact_phone" id="contact_phone" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="company_website">Company Website</label>
                <input type="url" name="company_website" id="company_website" class="form-control" placeholder="https://example.com">
            </div>
        </fieldset>
        
        <!-- Address Information -->
        <fieldset>
            <legend>Company Address</legend>
            
            <div class="form-group">
                <label for="company_province">Province *</label>
                <input type="text" name="company_province" id="company_province" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="company_district">District *</label>
                <input type="text" name="company_district" id="company_district" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="company_sector">Sector *</label>
                <input type="text" name="company_sector" id="company_sector" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="company_cell">Cell *</label>
                <input type="text" name="company_cell" id="company_cell" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="company_village">Village *</label>
                <input type="text" name="company_village" id="company_village" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
        </fieldset>
        
        <!-- Documents Upload -->
        <fieldset>
            <legend>Company Documents</legend>
            
            <div class="form-group">
                <label for="company_registration">Business Registration Certificate (PDF) *</label>
                <input type="file" name="company_registration" id="company_registration" class="form-control" required accept=".pdf">
                <small class="form-text text-muted">Maximum file size: 2MB</small>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="company_logo">Company Logo (JPG, PNG) *</label>
                <input type="file" name="company_logo" id="company_logo" class="form-control" required accept=".jpg,.jpeg,.png">
                <small class="form-text text-muted">Maximum file size: 1MB</small>
                <div class="invalid-feedback"></div>
            </div>
        </fieldset>
        
        <div class="form-group submit-group">
            <button type="submit" name="submit" class="btn btn-primary">
                Create Employer Account
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </button>
            <button type="reset" class="btn btn-secondary">Reset Form</button>
        </div>
    </form>

    <!-- Already have an account link -->
    <div class="text-center mt-4">
        <p>Already have an account? <a href="signin.php">Sign In</a></p>
    </div>

    <!-- Back to Home Button -->
    <div class="back-to-home">
        <a href="index.php">Back to Home</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle between job seeker and employer forms
    const jobSeekerBtn = document.getElementById('job-seeker-btn');
    const employerBtn = document.getElementById('employer-btn');
    const jobSeekerForm = document.getElementById('job-seeker-form');
    const employerForm = document.getElementById('employer-form');
    
    jobSeekerBtn.addEventListener('click', function() {
        jobSeekerForm.style.display = 'block';
        employerForm.style.display = 'none';
        jobSeekerBtn.classList.add('active');
        employerBtn.classList.remove('active');
    });
    
    employerBtn.addEventListener('click', function() {
        jobSeekerForm.style.display = 'none';
        employerForm.style.display = 'block';
        employerBtn.classList.add('active');
        jobSeekerBtn.classList.remove('active');
    });
    
    // Helper function for form validation
    function showError(element, message) {
        element.classList.add('is-invalid');
        const feedback = element.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.textContent = message;
        }
    }
    
    function clearErrors() {
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(el => {
            el.textContent = '';
        });
    }
    
    // Job Seeker Form Validation
    jobSeekerForm.addEventListener('submit', function(event) {
        event.preventDefault();
        clearErrors();
        
        let isValid = true;
        
        // Password match validation
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        
        if (password.value !== confirmPassword.value) {
            showError(confirmPassword, 'Passwords do not match');
            isValid = false;
        }
        
        // Validate file size for uploads
        const idDocument = document.getElementById('id_document');
        const resume = document.getElementById('resume');
        const profileImage = document.getElementById('profile_image');
        
        if (idDocument.files[0] && idDocument.files[0].size > 2 * 1024 * 1024) {
            showError(idDocument, 'ID Document file size exceeds 2MB limit');
            isValid = false;
        }
        
        if (resume.files[0] && resume.files[0].size > 2 * 1024 * 1024) {
            showError(resume, 'Resume file size exceeds 2MB limit');
            isValid = false;
        }
        
        if (profileImage.files[0] && profileImage.files[0].size > 1 * 1024 * 1024) {
            showError(profileImage, 'Profile Image file size exceeds 1MB limit');
            isValid = false;
        }
        
        // Validate phone number format
        const phoneRegex = /^\+?[0-9]{10,15}$/;
        const phone = document.getElementById('phone');
        
        if (!phoneRegex.test(phone.value)) {
            showError(phone, 'Please enter a valid phone number (10-15 digits)');
            isValid = false;
        }
        
        // Validate at least one availability option is selected
        const availabilityCheckboxes = document.querySelectorAll('input[name="availability[]"]:checked');
        if (availabilityCheckboxes.length === 0) {
            const availabilityContainer = document.querySelector('label:contains("Available Work Schedule")').closest('.form-group');
            showError(availabilityContainer, 'Please select at least one availability option');
            isValid = false;
        }
        
        if (isValid) {
            // Show loading spinner
            this.querySelector('.spinner-border').style.display = 'inline-block';
            
            // Submit the form using AJAX
            const formData = new FormData(this);
            
            fetch('process_registration.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Hide loading spinner
                this.querySelector('.spinner-border').style.display = 'none';
                
                // Display message
                const messageContainer = document.getElementById('form-messages');
                if (data.status === 'success') {
                    messageContainer.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    // Reset form on success
                    this.reset();
                    // Redirect to login page after short delay
                    setTimeout(() => {
                        window.location.href = 'signin.php';
                    }, 2000);
                } else {
                    messageContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    // Handle specific field errors
                    if (data.errors) {
                        for (const field in data.errors) {
                            const element = document.getElementById(field);
                            if (element) {
                                showError(element, data.errors[field]);
                            }
                        }
                    }
                }
            })
            .catch(error => {
                // Hide loading spinner
                this.querySelector('.spinner-border').style.display = 'none';
                document.getElementById('form-messages').innerHTML = `<div class="alert alert-danger">An error occurred. Please try again later.</div>`;
                console.error('Error:', error);
            });
        }
    });
    
    // Employer Form Validation
    employerForm.addEventListener('submit', function(event) {
        event.preventDefault();
        clearErrors();
        
        let isValid = true;
        
        // Password match validation
        const password = document.getElementById('company_password');
        const confirmPassword = document.getElementById('company_confirm_password');
        
        if (password.value !== confirmPassword.value) {
            showError(confirmPassword, 'Passwords do not match');
            isValid = false;
        }
        
        // Validate file size for uploads
        const companyRegistration = document.getElementById('company_registration');
        const companyLogo = document.getElementById('company_logo');
        
        if (companyRegistration.files[0] && companyRegistration.files[0].size > 2 * 1024 * 1024) {
            showError(companyRegistration, 'Company Registration file size exceeds 2MB limit');
            isValid = false;
        }
        
        if (companyLogo.files[0] && companyLogo.files[0].size > 1 * 1024 * 1024) {
            showError(companyLogo, 'Company Logo file size exceeds 1MB limit');
            isValid = false;
        }
        
        // Validate phone number format
        const phoneRegex = /^\+?[0-9]{10,15}$/;
        const phone = document.getElementById('contact_phone');
        
        if (!phoneRegex.test(phone.value)) {
            showError(phone, 'Please enter a valid phone number (10-15 digits)');
            isValid = false;
        }
        
        if (isValid) {
            // Show loading spinner
            this.querySelector('.spinner-border').style.display = 'inline-block';
            
            // Submit the form using AJAX
            const formData = new FormData(this);
            
            fetch('process_registration.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Hide loading spinner
                this.querySelector('.spinner-border').style.display = 'none';
                
                // Display message
                const messageContainer = document.getElementById('form-messages');
                if (data.status === 'success') {
                    messageContainer.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    // Reset form on success
                    this.reset();
                    // Redirect to login page after short delay
                    setTimeout(() => {
                        window.location.href = 'signin.php';
                    }, 2000);
                } else {
                    messageContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    // Handle specific field errors
                    if (data.errors) {
                        for (const field in data.errors) {
                            const element = document.getElementById(field);
                            if (element) {
                                showError(element, data.errors[field]);
                            }
                        }
                    }
                }
            })
            .catch(error => {
                // Hide loading spinner
                this.querySelector('.spinner-border').style.display = 'none';
                document.getElementById('form-messages').innerHTML = `<div class="alert alert-danger">An error occurred. Please try again later.</div>`;
                console.error('Error:', error);
            });
        }
    });
});
</script>

<?php
// Check if footer file exists an