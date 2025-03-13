<?php
// Start session
session_start();

// Database connection
$db_host = "localhost";
$db_user = "root"; // Change to your database username
$db_pass = "Diano21@Esron21%"; // Change to your database password (removed for security)
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
    <title>Ndahari Job Portal - Employer Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }
        
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        
        h2 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 10px;
        }
        
        h2:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background-color: var(--primary-color);
        }
        
        fieldset {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        fieldset:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        legend {
            font-weight: bold;
            padding: 0 15px;
            width: auto;
            color: var(--primary-color);
            font-size: 1.2rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-control {
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }
        
        .form-control.is-invalid {
            border-color: var(--danger-color);
            background-image: none;
        }
        
        label {
            font-weight: 500;
            color: #555;
            margin-bottom: 8px;
            display: block;
        }
        
        .btn {
            padding: 12px 25px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
        
        .registration-type {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }
        
        .registration-type .btn {
            margin: 0 15px;
            min-width: 150px;
            position: relative;
            overflow: hidden;
        }
        
        .registration-type .btn:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }
        
        .registration-type .btn:hover:before {
            left: 100%;
        }
        
        .registration-type .btn.active {
            background-color: #0056b3;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 86, 179, 0.3);
        }
        
        .invalid-feedback {
            color: var(--danger-color);
            display: block;
            margin-top: 5px;
            font-size: 0.875rem;
        }
        
        .alert {
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
            animation: fadeIn 0.5s;
        }
        
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        
        .spinner-border {
            width: 1.5rem;
            height: 1.5rem;
            margin-left: 10px;
            vertical-align: middle;
        }
        
        .password-toggle {
            position: relative;
        }
        
        .password-toggle-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }
        
        .progress-container {
            margin-top: 10px;
            height: 5px;
            background-color: #e0e0e0;
            border-radius: 3px;
            margin-bottom: 10px;
        }
        
        .progress-bar {
            height: 100%;
            border-radius: 3px;
            transition: width 0.3s, background-color 0.3s;
        }
        
        .password-strength-text {
            font-size: 0.8rem;
            margin-top: 5px;
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .step {
            text-align: center;
            flex: 1;
            position: relative;
        }
        
        .step-number {
            width: 30px;
            height: 30px;
            background-color: #e0e0e0;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 5px;
            color: white;
            transition: background-color 0.3s;
        }
        
        .step.active .step-number {
            background-color: var(--primary-color);
        }
        
        .step.completed .step-number {
            background-color: var(--success-color);
        }
        
        .step-title {
            font-size: 0.9rem;
            color: #555;
        }
        
        .step.active .step-title {
            color: var(--primary-color);
            font-weight: bold;
        }
        
        .step.completed .step-title {
            color: var(--success-color);
        }
        
        .step-connector {
            position: absolute;
            top: 15px;
            height: 2px;
            background-color: #e0e0e0;
            width: 100%;
            left: 50%;
            z-index: -1;
        }
        
        .step:last-child .step-connector {
            display: none;
        }
        
        .step.completed .step-connector {
            background-color: var(--success-color);
        }
        
        .form-navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        .shake {
            animation: shake 0.5s;
        }
        
        .tooltip-icon {
            margin-left: 5px;
            color: #6c757d;
            cursor: pointer;
        }
        
        .back-to-home {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-to-home a {
            background-color: var(--dark-color);
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .back-to-home a:hover {
            background-color: #23272b;
            transform: translateY(-2px);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .registration-type {
                flex-direction: column;
                gap: 10px;
            }
            
            .registration-type .btn {
                margin: 5px 0;
            }
            
            .container {
                padding: 15px;
            }
            
            fieldset {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <header class="bg-dark text-white p-3">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Ndahari Job Portal</h1>
                <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                    <ul class="navbar-nav ms-auto">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt me-1"></i>Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i>Logout</a></li>
                        <?php else: ?>
                            <li class="nav-item"><a class="nav-link" href="signin.php"><i class="fas fa-sign-in-alt me-1"></i>Sign In</a></li>
                            <li class="nav-item"><a class="nav-link" href="signup.php"><i class="fas fa-user-plus me-1"></i>Sign Up</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
<?php
}
?>

<div class="container">
    <!-- <h2 class="text-center mb-4">Create an Employer Account</h2> -->

    <!-- <div class="step-indicator">
        <div class="step active" id="step1">
            <div class="step-number">1</div>
            <div class="step-connector"></div>
            <div class="step-title">Company Info</div>
        </div>
        <div class="step" id="step2">
            <div class="step-number">2</div>
            <div class="step-connector"></div>
            <div class="step-title">Contact Details</div>
        </div>
        <div class="step" id="step3">
            <div class="step-number">3</div>
            <div class="step-title">Address</div>
        </div>
    </div> -->
    
    <!-- Employer Registration Form -->
    <form id="employer-form" action="process_registration.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="user_type" value="employer">
        
        <!-- Step 1: Company Information -->
        <fieldset id="company-info-section">
            <legend><i class="fas fa-building me-2"></i>Company Information</legend>
            
            <div class="form-group">
                <label for="company_name">Company Name <span class="text-danger">*</span></label>
                <input type="text" name="company_name" id="company_name" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="industry">Industry <span class="text-danger">*</span></label>
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
                <label for="company_size">Company Size <span class="text-danger">*</span></label>
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
                <label for="company_description">Company Description <span class="text-danger">*</span>
                    <i class="fas fa-info-circle tooltip-icon" title="Provide a brief overview of your company, including its mission, values, and what makes it unique."></i>
                </label>
                <textarea name="company_description" id="company_description" class="form-control" rows="4" required></textarea>
                <div class="invalid-feedback"></div>
                <small class="text-muted">Min 100 characters. <span id="description-counter">0</span>/1000 characters</small>
            </div>
            
            <div class="form-group">
                <label for="company_logo">Company Logo</label>
                <input type="file" name="company_logo" id="company_logo" class="form-control" accept="image/*">
                <div class="invalid-feedback"></div>
                <small class="text-muted">Recommended size: 200x200 pixels. Max file size: 2MB</small>
            </div>
            
            <div class="form-navigation">
                <div></div> <!-- Empty div for spacing -->
                <button type="button" id="next-step1" class="btn btn-primary">Next <i class="fas fa-arrow-right ms-2"></i></button>
            </div>
        </fieldset>
        
        <!-- Step 2: Contact Information -->
        <fieldset id="contact-info-section" style="display: none;">
            <legend><i class="fas fa-address-card me-2"></i>Contact Information</legend>
            
            <div class="form-group">
                <label for="contact_person">Contact Person Name <span class="text-danger">*</span></label>
                <input type="text" name="contact_person" id="contact_person" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="contact_email">Email Address <span class="text-danger">*</span></label>
                <input type="email" name="contact_email" id="contact_email" class="form-control" required>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="company_password">Password <span class="text-danger">*</span>
                    <i class="fas fa-info-circle tooltip-icon" title="Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character."></i>
                </label>
                <div class="password-toggle">
                    <input type="password" name="company_password" id="company_password" class="form-control" required>
                    <i class="fas fa-eye password-toggle-icon" id="toggle-password"></i>
                </div>
                <div class="progress-container">
                    <div class="progress-bar" id="password-strength"></div>
                </div>
                <div class="password-strength-text" id="password-strength-text"></div>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="company_confirm_password">Confirm Password <span class="text-danger">*</span></label>
                <div class="password-toggle">
                    <input type="password" name="company_confirm_password" id="company_confirm_password" class="form-control" required>
                    <i class="fas fa-eye password-toggle-icon" id="toggle-confirm-password"></i>
                </div>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="contact_phone">Phone Number <span class="text-danger">*</span></label>
                <input type="tel" name="contact_phone" id="contact_phone" class="form-control" required>
                <div class="invalid-feedback"></div>
                <small class="text-muted">Format: +250 XXXXXXXXX</small>
            </div>
            
            <div class="form-group">
                <label for="company_website">Company Website</label>
                <input type="url" name="company_website" id="company_website" class="form-control" placeholder="https://example.com">
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-navigation">
                <button type="button" id="prev-step2" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i> Previous</button>
                <button type="button" id="next-step2" class="btn btn-primary">Next <i class="fas fa-arrow-right ms-2"></i></button>
            </div>
        </fieldset>
        
        <!-- Step 3: Address Information -->
        <fieldset id="address-info-section" style="display: none;">
            <legend><i class="fas fa-map-marker-alt me-2"></i>Company Address</legend>
            
            <div class="form-group">
                <label for="company_province">Province <span class="text-danger">*</span></label>
                <select name="company_province" id="company_province" class="form-control" required>
                    <option value="">Select Province</option>
                    <option value="Kigali">Kigali</option>
                    <option value="Northern">Northern Province</option>
                    <option value="Southern">Southern Province</option>
                    <option value="Eastern">Eastern Province</option>
                    <option value="Western">Western Province</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="company_district">District <span class="text-danger">*</span></label>
                <select name="company_district" id="company_district" class="form-control" required>
                    <option value="">Select District</option>
                    <!-- Districts will be populated via JavaScript based on selected province -->
                </select>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="company_sector">Sector <span class="text-danger">*</span></label>
                <select name="company_sector" id="company_sector" class="form-control" required>
                    <option value="">Select Sector</option>
                    <!-- Sectors will be populated via JavaScript based on selected district -->
                </select>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-group">
                <label for="company_cell">Cell</label>
                <select name="company_cell" id="company_cell" class="form-control">
                    <option value="">Select Cell</option>
                    <!-- Cells will be populated via JavaScript based on selected sector -->
                </select>
            </div>
            
            <div class="form-group">
                <label for="company_street">Street/Road</label>
                <input type="text" name="company_street" id="company_street" class="form-control">
            </div>
            
            <div class="form-group form-check">
                <input type="checkbox" name="terms_agreement" id="terms_agreement" class="form-check-input" required>
                <label for="terms_agreement" class="form-check-label">I agree to the <a href="terms.php" target="_blank">Terms and Conditions</a> <span class="text-danger">*</span></label>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="form-navigation">
                <button type="button" id="prev-step3" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i> Previous</button>
                <button type="submit" id="submit-btn" class="btn btn-primary">
                    Register <i class="fas fa-check ms-2"></i>
                    <span class="spinner-border spinner-border-sm" id="loading-spinner" role="status" aria-hidden="true" style="display: none;"></span>
                </button>
            </div>
        </fieldset>
    </form>
    
    <!-- <div class="back-to-home">
        <a href="index.php"><i class="fas fa-home me-2"></i>Back to Home</a>
    </div> -->
</div>

<!-- JavaScript Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<!-- Previous PHP and HTML code remains the same until the Rwanda Locations data -->

<script>
$(document).ready(function() {
    // Complete Rwanda Location Data
    const rwandaLocations = {
        'Kigali': {
            'Nyarugenge': ['Gitega', 'Kanyinya', 'Kigali', 'Kimisagara', 'Mageragere', 'Muhima', 'Nyakabanda', 'Nyamirambo', 'Nyarugenge', 'Rwezamenyo'],
            'Gasabo': ['Bumbogo', 'Gatsata', 'Gikomero', 'Gisozi', 'Jabana', 'Jali', 'Kacyiru', 'Kimihurura', 'Kimironko', 'Kinyinya', 'Ndera', 'Nduba', 'Remera', 'Rusororo', 'Rutunga'],
            'Kicukiro': ['Gahanga', 'Gatenga', 'Gikondo', 'Kagarama', 'Kanombe', 'Kicukiro', 'Kigarama', 'Masaka', 'Niboye', 'Nyarugunga']
        },
        'Northern': {
            'Burera': ['Butaro', 'Cyanika', 'Cyeru', 'Gahunga', 'Gatebe', 'Gitovu', 'Kagogo', 'Kinoni', 'Kinyababa', 'Kivuye', 'Nemba', 'Rugarama', 'Rugengabari', 'Ruhunde', 'Rusarabuye'],
            'Gakenke': ['Busengo', 'Coko', 'Cyabingo', 'Gakenke', 'Gashenyi', 'Janja', 'Kamubuga', 'Karambo', 'Kivuruga', 'Mataba', 'Minazi', 'Mugunga', 'Muhondo', 'Muyongwe', 'Muzo', 'Nemba', 'Ruli', 'Rusasa', 'Rushashi'],
            'Gicumbi': ['Bukure', 'Bwisige', 'Byumba', 'Cyumba', 'Giti', 'Kageyo', 'Kaniga', 'Manyagiro', 'Miyove', 'Mukarange', 'Muko', 'Mutete', 'Nyamiyaga', 'Nyankenke', 'Rubaya', 'Rukomo', 'Rushaki', 'Rutare', 'Ruvune', 'Rwamiko', 'Shangasha'],
            'Musanze': ['Busogo', 'Cyuve', 'Gacaca', 'Gashaki', 'Gataraga', 'Kimonyi', 'Kinigi', 'Muhoza', 'Muko', 'Musanze', 'Nkotsi', 'Nyange', 'Remera', 'Rwaza', 'Shingiro'],
            'Rulindo': ['Base', 'Burega', 'Bushoki', 'Buyoga', 'Cyinzuzi', 'Cyungo', 'Kisaro', 'Kinihira', 'Masoro', 'Mbogo', 'Murambi', 'Ngoma', 'Ntarabana', 'Rukozo', 'Rusiga', 'Shyorongi', 'Tumba']
        },
        'Southern': {
            'Gisagara': ['Gikonko', 'Gishubi', 'Kansi', 'Kibilizi', 'Kigembe', 'Mamba', 'Muganza', 'Mugombwa', 'Mukindo', 'Musha', 'Ndora', 'Nyanza', 'Save'],
            'Huye': ['Gishamvu', 'Huye', 'Karama', 'Kigoma', 'Kinazi', 'Maraba', 'Mbazi', 'Mukura', 'Ngoma', 'Ruhashya', 'Rusatira', 'Rwaniro', 'Simbi', 'Tumba'],
            'Kamonyi': ['Gacurabwenge', 'Karama', 'Kayenzi', 'Kayumbu', 'Mugina', 'Musambira', 'Ngamba', 'Nyamiyaga', 'Nyarubaka', 'Rugalika', 'Rukoma', 'Runda'],
            'Muhanga': ['Cyeza', 'Kabacuzi', 'Kibangu', 'Kiyumba', 'Muhanga', 'Mushishiro', 'Nyabinoni', 'Nyamabuye', 'Nyarusange', 'Rongi', 'Rugendabari', 'Shyogwe'],
            'Nyamagabe': ['Buruhukiro', 'Cyanika', 'Gasaka', 'Gatare', 'Kaduha', 'Kamegeri', 'Kibirizi', 'Kibumbwe', 'Kitabi', 'Mbazi', 'Mugano', 'Musange', 'Musebeya', 'Mushubi', 'Nkomane', 'Tare', 'Uwinkingi'],
            'Nyanza': ['Busasamana', 'Busoro', 'Cyabakamyi', 'Kibilizi', 'Kigoma', 'Mukingo', 'Muyira', 'Ntyazo', 'Nyagisozi', 'Rwabicuma'],
            'Nyaruguru': ['Busanze', 'Cyahinda', 'Kibeho', 'Kibingo', 'Mata', 'Munini', 'Ngera', 'Ngoma', 'Nyabimata', 'Nyagisozi', 'Ruheru', 'Ruramba', 'Rusenge']
        },
        'Eastern': {
            'Bugesera': ['Gashora', 'Juru', 'Kamabuye', 'Ntarama', 'Mareba', 'Mayange', 'Musenyi', 'Mwogo', 'Ngeruka', 'Nyamata', 'Ruhuha', 'Rweru'],
            'Gatsibo': ['Gatsibo', 'Gituza', 'Kabarore', 'Kageyo', 'Kiramuruzi', 'Kiziguro', 'Muhura', 'Murambi', 'Ngarama', 'Nyagihanga', 'Remera', 'Rugarama'],
            'Kayonza': ['Gahini', 'Kabare', 'Kabuga', 'Karangazi', 'Kayonza', 'Kibungo', 'Mukarange', 'Murama', 'Murundi', 'Mwiri', 'Ndego', 'Nyamirama', 'Rukara', 'Ruramira', 'Rwinkwavu'],
            'Kirehe': ['Gahara', 'Gatore', 'Kigarama', 'Kigina', 'Kirehe', 'Mahama', 'Mpanga', 'Musaza', 'Mushikiri', 'Nasho', 'Nyamugari', 'Nyarubuye'],
            'Ngoma': ['Gashanda', 'Jarama', 'Kaziba', 'Kibungo', 'Mugesera', 'Murama', 'Mutenderi', 'Remera', 'Rukira', 'Rukumberi', 'Rurenge', 'Sake', 'Zaza'],
            'Nyagatare': ['Fumbwe', 'Gahengeri', 'Kiyombe', 'Karama', 'Katabagemu', 'Matimba', 'Mimuri', 'Mukama', 'Musheli', 'Nyagatare', 'Rukomo', 'Rwempasha', 'Rwimiyaga', 'Tabagwe']
        },
        'Western': {
            'Karongi': ['Bwishyura', 'Gashari', 'Gishyita', 'Gitesi', 'Mubuga', 'Murambi', 'Murundi', 'Mutuntu', 'Rubengera', 'Rugabano', 'Ruganda', 'Rwankuba'],
            'Ngororero': ['Bwira', 'Gatumba', 'Hindiro', 'Kageyo', 'Kavumu', 'Matyazo', 'Muhanda', 'Muhororo', 'Ndaro', 'Ngororero', 'Nyange', 'Sovu'],
            'Nyabihu': ['Bigogwe', 'Jenda', 'Jomba', 'Kabatwa', 'Karago', 'Kintobo', 'Mukamira', 'Muringa', 'Rambura', 'Rugera', 'Rurembo', 'Shyira'],
            'Nyamasheke': ['Buruhukiro', 'Cyanjongo', 'Gihombo', 'Kagano', 'Kanjongo', 'Karambi', 'Karengera', 'Kirimbi', 'Macuba', 'Mahembe', 'Nyabitekeri', 'Rangiro', 'Ruharambuga', 'Shangi'],
            'Rubavu': ['Bugeshi', 'Busasamana', 'Cyanzarwe', 'Gisenyi', 'Kanama', 'Kanzenze', 'Mudende', 'Nyakiriba', 'Nyamyumba', 'Rubavu', 'Rugerero'],
            'Rutsiro': ['Boneza', 'Gihango', 'Kigeyo', 'Kivumu', 'Manihira', 'Mukura', 'Murunda', 'Musasa', 'Mushonyi', 'Mushubati', 'Nyabirasi', 'Ruhango', 'Rusebeya'],
            'Rusizi': ['Bugarama', 'Butare', 'Bweyeye', 'Gashonga', 'Giheke', 'Gihundwe', 'Gikundamvura', 'Gitambi', 'Kamembe', 'Muganza', 'Mururu', 'Nkanka', 'Nkungu', 'Nyakabuye', 'Nyakarenzo', 'Nzahaha']
        }
    };

    // Location dropdown population function
    function updateLocationDropdowns() {
        const province = $('#company_province').val();
        const district = $('#company_district').val();
        
        // Update districts
        $('#company_district').empty().append('<option value="">Select District</option>');
        if (province && rwandaLocations[province]) {
            Object.keys(rwandaLocations[province]).forEach(district => {
                $('#company_district').append(`<option value="${district}">${district}</option>`);
            });
        }

        // Update sectors
        $('#company_sector').empty().append('<option value="">Select Sector</option>');
        if (province && district && rwandaLocations[province][district]) {
            rwandaLocations[province][district].forEach(sector => {
                $('#company_sector').append(`<option value="${sector}">${sector}</option>`);
            });
        }
    }

    // Province change handler
    $('#company_province').change(updateLocationDropdowns);
    $('#company_district').change(updateLocationDropdowns);

    // Initialize Bootstrap tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Form step navigation
    let currentStep = 1;
    
    function showStep(step) {
        $('fieldset').hide();
        $(`#step${step} .step-number`).addClass('active');
        $(`#step${step}`).addClass('active');
        
        switch(step) {
            case 1:
                $('#company-info-section').show();
                break;
            case 2:
                $('#contact-info-section').show();
                break;
            case 3:
                $('#address-info-section').show();
                break;
        }
    }

    // Next/Previous step handlers
    $('#next-step1').click(() => { currentStep = 2; showStep(2); });
    $('#next-step2').click(() => { currentStep = 3; showStep(3); });
    $('#prev-step2').click(() => { currentStep = 1; showStep(1); });
    $('#prev-step3').click(() => { currentStep = 2; showStep(2); });

    // Password strength indicator
    $('#company_password').on('input', function() {
        const password = $(this).val();
        let strength = 0;
        
        if (password.length >= 8) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[\W_]/.test(password)) strength++;

        const strengthText = ['Very Weak', 'Weak', 'Moderate', 'Strong', 'Very Strong'][strength];
        const strengthColors = ['#dc3545', '#ffc107', '#ffc107', '#28a745', '#28a745'];
        const width = ((strength + 1) * 20) + '%';
        
        $('#password-strength').css({
            width: width,
            'background-color': strengthColors[strength]
        });
        $('#password-strength-text').text(`Password Strength: ${strengthText}`);
    });

    // Password toggle visibility
    $('#toggle-password, #toggle-confirm-password').click(function() {
        const input = $(this).prev('input');
        const type = input.attr('type') === 'password' ? 'text' : 'password';
        input.attr('type', type);
        $(this).toggleClass('fa-eye fa-eye-slash');
    });

    // Form submission handler
    $('#employer-form').submit(function(e) {
        e.preventDefault();
        $('#loading-spinner').show();
        
        // Add your AJAX form submission logic here
        // Example:
        /*
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(response) {
                // Handle success response
            },
            error: function(error) {
                // Handle error
            },
            complete: function() {
                $('#loading-spinner').hide();
            }
        });
        */
    });

    // Character counter for description
    $('#company_description').on('input', function() {
        const length = $(this).val().length;
        $('#description-counter').text(length);
    });
});
</script>

</body>
</html>