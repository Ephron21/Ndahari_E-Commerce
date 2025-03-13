<?php
// Start session
session_start();

// Database connection
$db_host = "localhost";
$db_user = "root";
$db_pass = "Diano21@Esron21%";
$db_name = "ndahari";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create job_seekers table if not exists
$createTable = "
CREATE TABLE IF NOT EXISTS job_seeker (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(30) NOT NULL,
    lastname VARCHAR(30) NOT NULL,
    email VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    age INT(3) NOT NULL,
    gender ENUM('male','female','other') NOT NULL,
    job_title VARCHAR(100) NOT NULL,
    skills TEXT NOT NULL,
    certifications TEXT,
    availability TEXT NOT NULL,
    availability_notes TEXT,
    province VARCHAR(50) NOT NULL,
    district VARCHAR(50) NOT NULL,
    sector VARCHAR(50) NOT NULL,
    cell VARCHAR(50) NOT NULL,
    village VARCHAR(50) NOT NULL,
    id_document VARCHAR(255) NOT NULL,
    resume VARCHAR(255) NOT NULL,
    profile_image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($createTable)) {
    die("Error creating table: " . $conn->error);
}

// Include header
if (file_exists('includes/header.php')) {
    include 'includes/header.php';
} else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ndahari Job Portal - Job Seeker Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --accent-color: #60a5fa;
            --text-dark: #1f2937;
            --text-light: #f3f4f6;
        }

        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .container:hover {
            transform: translateY(-5px);
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }

        .form-header h2 {
            color: var(--primary-color);
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .progress-bar {
            height: 8px;
            background-color: #e2e8f0;
            border-radius: 4px;
            margin: 2rem 0;
            overflow: hidden;
        }

        .progress {
            height: 100%;
            background-color: var(--primary-color);
            width: 0;
            transition: width 0.5s ease;
        }

        fieldset {
            border: 2px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease;
        }

        fieldset.active {
            opacity: 1;
            transform: translateY(0);
        }

        legend {
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 1.1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
            font-weight: 500;
        }

        .form-control {
            transition: all 0.3s ease;
            border: 2px solid #cbd5e1;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .animated-checkbox {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .animated-checkbox label {
            background: #f1f5f9;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .animated-checkbox input:checked + label {
            background: var(--primary-color);
            color: white;
            border-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .file-upload {
            border: 2px dashed #cbd5e1;
            padding: 2rem;
            text-align: center;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .file-upload:hover {
            border-color: var(--primary-color);
            background: #f8fafc;
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 1rem 2rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
        }

        .form-navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
        }

        .password-strength {
            height: 4px;
            background: #e2e8f0;
            border-radius: 2px;
            margin-top: 0.5rem;
            overflow: hidden;
        }

        .strength-bar {
            height: 100%;
            width: 0;
            transition: width 0.3s ease;
        }

        .tooltip-icon {
            cursor: help;
            margin-left: 0.5rem;
            color: var(--primary-color);
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .floating-image {
            animation: float 3s ease-in-out infinite;
            max-width: 200px;
            margin: 0 auto;
        }

        .is-invalid {
            border-color: #ef4444 !important;
        }
    </style>
</head>
<body>
<?php } ?>

<div class="container">
    <div class="form-header">
        <img src="https://via.placeholder.com/200" alt="Career Path" class="floating-image">
        <h2 class="animate__animated animate__fadeInDown">Start Your Career Journey</h2>
        <div class="progress-bar">
            <div class="progress" id="form-progress"></div>
        </div>
    </div>

    <form id="job-seeker-form" action="process_registration.php" method="POST" enctype="multipart/form-data">
        <!-- Form steps -->
        <div class="form-steps">
            <!-- Step 1 - Personal Info -->
            <fieldset class="active" data-step="1">
                <legend>Personal Information</legend>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="firstname" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="firstname" name="firstname" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="lastname" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="lastname" name="lastname" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="password-strength mt-2">
                                <div class="strength-bar"></div>
                            </div>
                            <small class="text-muted">Must be at least 8 characters with uppercase, number, and special character.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="confirm_password" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="age" class="form-label">Age <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="age" name="age" min="16" max="100" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <div class="animated-checkbox">
                                <input type="radio" id="gender_male" name="gender" value="male" class="d-none" required>
                                <label for="gender_male">Male</label>
                                
                                <input type="radio" id="gender_female" name="gender" value="female" class="d-none">
                                <label for="gender_female">Female</label>
                                
                                <input type="radio" id="gender_other" name="gender" value="other" class="d-none">
                                <label for="gender_other">Other</label>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>

            <!-- Step 2 - Professional Info -->
            <fieldset data-step="2">
                <legend>Professional Information</legend>
                <div class="form-group">
                    <label for="job_title" class="form-label">Desired Job Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="job_title" name="job_title" required>
                </div>
                
                <div class="form-group">
                    <label for="skills" class="form-label">Skills <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="skills" name="skills" rows="3" placeholder="List your skills separated by commas" required></textarea>
                    <small class="text-muted">Enter your skills separated by commas (e.g., Web Development, Project Management, Customer Service)</small>
                </div>
                
                <div class="form-group">
                    <label for="certifications" class="form-label">Certifications</label>
                    <textarea class="form-control" id="certifications" name="certifications" rows="3" placeholder="List your certifications"></textarea>
                    <small class="text-muted">Enter your certifications, one per line</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Availability <span class="text-danger">*</span></label>
                    <div class="animated-checkbox">
                        <input type="checkbox" id="avail_full_time" name="availability[]" value="Full Time" class="d-none">
                        <label for="avail_full_time">Full Time</label>
                        
                        <input type="checkbox" id="avail_part_time" name="availability[]" value="Part Time" class="d-none">
                        <label for="avail_part_time">Part Time</label>
                        
                        <input type="checkbox" id="avail_contract" name="availability[]" value="Contract" class="d-none">
                        <label for="avail_contract">Contract</label>
                        
                        <input type="checkbox" id="avail_freelance" name="availability[]" value="Freelance" class="d-none">
                        <label for="avail_freelance">Freelance</label>
                        
                        <input type="checkbox" id="avail_remote" name="availability[]" value="Remote" class="d-none">
                        <label for="avail_remote">Remote</label>
                    </div>
                    <div class="form-check mt-3">
                        <input type="checkbox" class="form-check-input" id="avail_check" required>
                        <label class="form-check-label" for="avail_check">I confirm my availability selection <span class="text-danger">*</span></label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="availability_notes" class="form-label">Availability Notes</label>
                    <textarea class="form-control" id="availability_notes" name="availability_notes" rows="2" placeholder="Any specific details about your availability"></textarea>
                </div>
            </fieldset>

            <!-- Step 3 - Address Info -->
            <fieldset data-step="3">
                <legend>Address Information</legend>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="province" class="form-label">Province <span class="text-danger">*</span></label>
                            <select class="form-select" id="province" name="province" required>
                                <option value="">Select Province</option>
                                <option value="Kigali">Kigali</option>
                                <option value="Northern">Northern</option>
                                <option value="Southern">Southern</option>
                                <option value="Eastern">Eastern</option>
                                <option value="Western">Western</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="district" class="form-label">District <span class="text-danger">*</span></label>
                            <select class="form-select" id="district" name="district" required>
                                <option value="">Select District</option>
                                <!-- Districts will be populated based on province -->
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="sector" class="form-label">Sector <span class="text-danger">*</span></label>
                            <select class="form-select" id="sector" name="sector" required>
                                <option value="">Select Sector</option>
                                <!-- Sectors will be populated based on district -->
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="cell" class="form-label">Cell <span class="text-danger">*</span></label>
                            <select class="form-select" id="cell" name="cell" required>
                                <option value="">Select Cell</option>
                                <!-- Cells will be populated based on sector -->
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="village" class="form-label">Village <span class="text-danger">*</span></label>
                            <select class="form-select" id="village" name="village" required>
                                <option value="">Select Village</option>
                                <!-- Villages will be populated based on cell -->
                            </select>
                        </div>
                    </div>
                </div>
            </fieldset>

            <!-- Step 4 - Documents -->
            <fieldset data-step="4">
                <legend>Document Upload</legend>
                <div class="form-group">
                    <label class="form-label">Profile Picture <span class="text-danger">*</span></label>
                    <div class="file-upload">
                        <i class="fas fa-user-circle fa-3x mb-3 text-primary"></i>
                        <h5>Upload your profile photo</h5>
                        <p class="text-muted">Accepted formats: JPG, PNG (Max size: 2MB)</p>
                        <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/jpeg,image/png" required>
                    </div>
                </div>
                
                <div class="form-group mt-4">
                    <label class="form-label">ID Document <span class="text-danger">*</span></label>
                    <div class="file-upload">
                        <i class="fas fa-id-card fa-3x mb-3 text-primary"></i>
                        <h5>Upload your ID document</h5>
                        <p class="text-muted">Accepted formats: PDF, JPG, PNG (Max size: 5MB)</p>
                        <input type="file" class="form-control" id="id_document" name="id_document" accept="application/pdf,image/jpeg,image/png" required>
                    </div>
                </div>
                
                <div class="form-group mt-4">
                    <label class="form-label">Resume/CV <span class="text-danger">*</span></label>
                    <div class="file-upload">
                        <i class="fas fa-file-alt fa-3x mb-3 text-primary"></i>
                        <h5>Upload your Resume/CV</h5>
                        <p class="text-muted">Accepted formats: PDF, DOCX (Max size: 5MB)</p>
                        <input type="file" class="form-control" id="resume" name="resume" accept="application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document" required>
                    </div>
                </div>
            </fieldset>
        </div>

        <div class="form-navigation">
            <button type="button" class="btn btn-secondary" id="prev-btn" disabled>Previous</button>
            <button type="button" class="btn btn-primary" id="next-btn">Next</button>
            <button type="submit" class="btn btn-success" id="submit-btn" style="display: none;">Submit Application</button>
        </div>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const steps = document.querySelectorAll('fieldset');
    let currentStep = 0;
    const progressBar = document.getElementById('form-progress');

    function updateProgress() {
        const progress = ((currentStep + 1) / steps.length) * 100;
        progressBar.style.width = `${progress}%`;
    }

    function showStep(stepIndex) {
        steps.forEach((step, index) => {
            step.classList.toggle('active', index === stepIndex);
        });
        document.getElementById('prev-btn').disabled = stepIndex === 0;
        document.getElementById('next-btn').style.display = stepIndex === steps.length - 1 ? 'none' : 'block';
        document.getElementById('submit-btn').style.display = stepIndex === steps.length - 1 ? 'block' : 'none';
        updateProgress();
    }

    // Password strength indicator
    document.getElementById('password').addEventListener('input', function(e) {
        const strength = calculateStrength(e.target.value);
        const strengthBar = document.querySelector('.strength-bar');
        strengthBar.style.width = `${strength}%`;
        strengthBar.style.backgroundColor = strength < 50 ? '#ef4444' : strength < 75 ? '#f59e0b' : '#10b981';
    });

    function calculateStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength += 25;
        if (password.match(/[A-Z]/)) strength += 25;
        if (password.match(/[0-9]/)) strength += 25;
        if (password.match(/[^A-Za-z0-9]/)) strength += 25;
        return Math.min(strength, 100);
    }

    // Confirm password validation
    document.getElementById('confirm_password').addEventListener('input', function(e) {
        const password = document.getElementById('password').value;
        if (e.target.value !== password) {
            e.target.setCustomValidity("Passwords do not match");
        } else {
            e.target.setCustomValidity("");
        }
    });

    // Form navigation
    document.getElementById('next-btn').addEventListener('click', function() {
        if (validateStep(currentStep)) {
            currentStep++;
            showStep(currentStep);
        }
    });

    document.getElementById('prev-btn').addEventListener('click', function() {
        currentStep--;
        showStep(currentStep);
    });

    function validateStep(stepIndex) {
        const currentFields = steps[stepIndex].querySelectorAll('[required]');
        let isValid = true;

        currentFields.forEach(field => {
            if (!field.checkValidity()) {
                field.reportValidity();
                isValid = false;
            }
        });

        // Special case for availability checkboxes
        if (stepIndex === 1) {
            const availCheckboxes = document.querySelectorAll('input[name="availability[]"]:checked');
            if (availCheckboxes.length === 0) {
                document.getElementById('avail_check').setCustomValidity("Please select at least one availability option");
                document.getElementById('avail_check').reportValidity();
                isValid = false;
            } else {
                document.getElementById('avail_check').setCustomValidity("");
            }
        }

        return isValid;
    }

    // Real-time validation
    document.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('input', () => {
            if (field.checkValidity()) {
                field.classList.remove('is-invalid');
            } else {
                field.classList.add('is-invalid');
            }
        });
    });

    // Location cascading dropdowns
    const provinceDropdown = document.getElementById('province');
    const districtDropdown = document.getElementById('district');
    const sectorDropdown = document.getElementById('sector');
    const cellDropdown = document.getElementById('cell');
    const villageDropdown = document.getElementById('village');

    // Rwanda administrative divisions (simplified)
    const rwandaLocations = {
        'Kigali': {
            'Nyarugenge': {
                'Gitega': {
                    'Akabahizi': ['Gihanga', 'Iterambere', 'Umucyo'],
                    'Akabeza': ['Amahoro', 'Ubumwe', 'Urukundo']
                },
                'Nyamirambo': {
                    'Mumena': ['Isangano', 'Muhabura', 'Rugenge'],
                    'Rugarama': ['Mukoni', 'Rebero', 'Rukurazo']
                }
            },
            'Gasabo': {
                'Kimironko': {
                    'Bibare': ['Gasiza', 'Kamahina', 'Nyagatovu'],
                    'Kibagabaga': ['Kabarera', 'Kigufi', 'Urugwiro']
                },
                'Kacyiru': {
                    'Kamatamu': ['Indatwa', 'Inkingi', 'Rugero'],
                    'Kamutwa': ['Amajyambere', 'Intsinzi', 'Umutekano']
                }
            }
        },
        'Northern': {
            'Musanze': {
                'Kinigi': {
                    'Kaguhu': ['Kabeza', 'Nyange', 'Rubindi'],
                    'Nyonirima': ['Gakoro', 'Muhabura', 'Rukore']
                },
                'Muhoza': {
                    'Cyabararika': ['Gacuba', 'Ruhengeri', 'Tero'],
                    'Ruhengeri': ['Buhuye', 'Kabaya', 'Karuganda']
                }
            }
        },
        'Southern': {
            'Huye': {
                'Ngoma': {
                    'Butare': ['Akaburaro', 'Intiganda', 'Taba'],
                    'Matyazo': ['Gatoki', 'Rugarama', 'Rusisiro']
                }
            }
        },
        'Eastern': {
            'Kayonza': {
                'Mukarange': {
                    'Bwiza': ['Amarembo', 'Rebero', 'Ruturusu'],
                    'Kayonza': ['Gahini', 'Nyagatovu', 'Rugendabari']
                }
            }
        },
        'Western': {
            'Rubavu': {
                'Gisenyi': {
                    'Mbugangari': ['Abateye', 'Gikombe', 'Kivumu'],
                    'Rubavu': ['Buhaza', 'Muhato', 'Rukoko']
                }
            }
        }
    };

    // Update districts based on province
    provinceDropdown.addEventListener('change', function() {
        const selectedProvince = this.value;
        districtDropdown.innerHTML = '<option value="">Select District</option>';
        sectorDropdown.innerHTML = '<option value="">Select Sector</option>';
        cellDropdown.innerHTML = '<option value="">Select Cell</option>';
        villageDropdown.innerHTML = '<option value="">Select Village</option>';
        
        if (selectedProvince && rwandaLocations[selectedProvince]) {
            const districts = Object.keys(rwandaLocations[selectedProvince]);
            districts.forEach(district => {
                const option = document.createElement('option');
                option.value = district;
                option.textContent = district;
                districtDropdown.appendChild(option);
            });
        }
    });

    // Update sectors based on district
    districtDropdown.addEventListener('change', function() {
        const selectedProvince = provinceDropdown.value;
        const selectedDistrict = this.value;
        sectorDropdown.innerHTML = '<option value="">Select Sector</option>';
        cellDropdown.innerHTML = '<option value="">Select Cell</option>';
        villageDropdown.innerHTML = '<option value="">Select Village</option>';
        
        if (selectedProvince && selectedDistrict && 
            rwandaLocations[selectedProvince] && 
            rwandaLocations[selectedProvince][selectedDistrict]) {
            const sectors = Object.keys(rwandaLocations[selectedProvince][selectedDistrict]);
            sectors.forEach(sector => {
                const option = document.createElement('option');
                option.value = sector;
                option.textContent = sector;
                sectorDropdown.appendChild(option);
            });
        }
    });

    // Update cells based on sector
    sectorDropdown.addEventListener('change', function() {
        const selectedProvince = provinceDropdown.value;
        const selectedDistrict = districtDropdown.value;
        const selectedSector = this.value;
        cellDropdown.innerHTML = '<option value="">Select Cell</option>';
        villageDropdown.innerHTML = '<option value="">Select Village</option>';
        
        if (selectedProvince && selectedDistrict && selectedSector && 
            rwandaLocations[selectedProvince] && 
            rwandaLocations[selectedProvince][selectedDistrict] &&
            rwandaLocations[selectedProvince][selectedDistrict][selectedSector]) {
            const cells = Object.keys(rwandaLocations[selectedProvince][selectedDistrict][selectedSector]);
            cells.forEach(cell => {
                const option = document.createElement('option');
                option.value = cell;
                option.textContent = cell;
                cellDropdown.appendChild(option);
            });)