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
    $user_type = sanitize_input($_POST['user_type']);
    $email = sanitize_input($_POST['email']);
    $transaction_started = false;
    
    try {
        // Check if email already exists in both tables
        $tables = ['employers', 'job_seekers'];
        $email_exists = false;
        
        foreach ($tables as $table) {
            $stmt = $pdo->prepare("SELECT id FROM $table WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $email_exists = true;
                break;
            }
        }
        
        if ($email_exists) {
            $error_message = "This email is already registered. Please use a different email or login.";
        } else {
            // Validate required fields
            $required_fields = ['first_name', 'last_name', 'email', 'password', 'phone'];
            $missing_fields = [];
            
            foreach ($required_fields as $field) {
                if (empty($_POST[$field])) {
                    $missing_fields[] = $field;
                }
            }
            
            if (!empty($missing_fields)) {
                $error_message = "Please fill in the following required fields: " . implode(', ', $missing_fields);
            } else {
                // Hash password
                $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                
                // Begin transaction
                $pdo->beginTransaction();
                $transaction_started = true;
                
                if ($user_type === 'employer') {
                    $stmt = $pdo->prepare("
                        INSERT INTO employers (
                            company_name,
                            contact_person,
                            email,
                            phone,
                            password,
                            company_description,
                            industry,
                            website,
                            location,
                            status
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
                    ");
                    
                    $stmt->execute([
                        sanitize_input($_POST['company_name']),
                        sanitize_input($_POST['first_name'] . ' ' . $_POST['last_name']),
                        $email,
                        sanitize_input($_POST['phone']),
                        $password_hash,
                        sanitize_input($_POST['company_description'] ?? ''),
                        sanitize_input($_POST['industry']),
                        sanitize_input($_POST['website'] ?? ''),
                        sanitize_input($_POST['location'] ?? ''),
                    ]);
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO job_seekers (
                            full_name,
                            email,
                            phone,
                            password,
                            skills,
                            experience,
                            education,
                            preferred_job_types,
                            preferred_locations,
                            bio,
                            status
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
                    ");
                    
                    $stmt->execute([
                        sanitize_input($_POST['first_name'] . ' ' . $_POST['last_name']),
                        $email,
                        sanitize_input($_POST['phone']),
                        $password_hash,
                        sanitize_input($_POST['skills']),
                        sanitize_input($_POST['experience'] ?? ''),
                        sanitize_input($_POST['education']),
                        sanitize_input($_POST['preferred_job_type']),
                        sanitize_input($_POST['location'] ?? ''),
                        sanitize_input($_POST['bio'] ?? '')
                    ]);
                }
                
                // Commit transaction
                $pdo->commit();
                $transaction_started = false;
                
                $success_message = "Registration successful! Please login to continue.";
                
                // Redirect to login page after 2 seconds
                header("refresh:2;url=login.php");
            }
        }
    } catch (PDOException $e) {
        // Only rollback if transaction was started
        if ($transaction_started) {
            $pdo->rollBack();
        }
        
        // Log the full error
        error_log("Registration error: " . $e->getMessage());
        
        // During development, show the actual error message
        $error_message = "Database error: " . $e->getMessage();
        
        // In production, use this instead:
        // $error_message = "An error occurred during registration. Please try again.";
    }
}

// Include header
require_once 'includes/header.php';
?>

<main class="register-page">
    <div class="container">
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <div class="register-header">
            <h1>Create an Account</h1>
            <p class="text-muted">Join our community and start your journey</p>
        </div>

        <div class="register-content">
            <div class="user-type-selection">
                <button type="button" class="user-type-btn active" data-type="job_seeker">
                    <i class="fas fa-user"></i>
                    Job Seeker
                </button>
                <button type="button" class="user-type-btn" data-type="employer">
                    <i class="fas fa-building"></i>
                    Employer
                </button>
            </div>

            <form id="registerForm" action="register.php" method="POST" class="register-form">
                <input type="hidden" name="user_type" id="user_type" value="job_seeker">

                <!-- Basic Information -->
                <div class="form-section">
                    <h2>Basic Information</h2>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password *</label>
                            <input type="password" id="password" name="password" required>
                            <small class="password-requirements">
                                Minimum 8 characters, including uppercase, lowercase, number
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password *</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                        <div class="form-group">
                            <label for="location">Location *</label>
                            <input type="text" id="location" name="location" required>
                        </div>
                    </div>
                </div>

                <!-- Job Seeker Fields -->
                <div id="jobSeekerFields" class="form-section user-type-fields">
                    <h2>Professional Information</h2>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="skills">Skills (separate with commas) *</label>
                            <input type="text" id="skills" name="skills" required
                                   placeholder="e.g., Customer Service, Sales, Marketing">
                        </div>
                        <div class="form-group">
                            <label for="experience">Work Experience</label>
                            <textarea id="experience" name="experience" rows="3"
                                    placeholder="Brief description of your work experience"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="education">Education *</label>
                            <select id="education" name="education" required>
                                <option value="">Select Education Level</option>
                                <option value="high_school">High School</option>
                                <option value="diploma">Diploma</option>
                                <option value="bachelors">Bachelor's Degree</option>
                                <option value="masters">Master's Degree</option>
                                <option value="phd">PhD</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="preferred_job_type">Preferred Job Types *</label>
                            <select id="preferred_job_type" name="preferred_job_type" required>
                                <option value="">Select Job Type</option>
                                <option value="part-time">Part Time</option>
                                <option value="one-time">One Time</option>
                                <option value="contract">Contract</option>
                                <option value="seasonal">Seasonal</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="bio">Bio</label>
                            <textarea id="bio" name="bio" rows="3"
                                    placeholder="Tell us about yourself"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Employer Fields -->
                <div id="employerFields" class="form-section user-type-fields" style="display: none;">
                    <h2>Company Information</h2>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="company_name">Company Name *</label>
                            <input type="text" id="company_name" name="company_name">
                        </div>
                        <div class="form-group">
                            <label for="industry">Industry *</label>
                            <select id="industry" name="industry">
                                <option value="">Select Industry</option>
                                <option value="technology">Technology</option>
                                <option value="healthcare">Healthcare</option>
                                <option value="finance">Finance</option>
                                <option value="education">Education</option>
                                <option value="retail">Retail</option>
                                <option value="manufacturing">Manufacturing</option>
                                <option value="construction">Construction</option>
                                <option value="hospitality">Hospitality</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="website">Company Website</label>
                            <input type="url" id="website" name="website" 
                                   placeholder="https://www.example.com">
                        </div>
                        <div class="form-group">
                            <label for="company_description">Company Description *</label>
                            <textarea id="company_description" name="company_description" rows="3"
                                    placeholder="Brief description of your company"></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Create Account</button>
                    <p class="login-link">
                        Already have an account? <a href="login.php">Login here</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</main>

<style>
.register-page {
    padding: 2rem 0;
    background-color: #f8f9fa;
    min-height: calc(100vh - 60px);
}

.register-header {
    text-align: center;
    margin-bottom: 2rem;
}

.register-header h1 {
    font-size: 2rem;
    color: #333;
    margin-bottom: 0.5rem;
}

.register-content {
    background: white;
    border-radius: 10px;
    padding: 2.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    max-width: 800px;
    margin: 0 auto;
}

.user-type-selection {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.user-type-btn {
    padding: 1rem 2rem;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    background: white;
    color: #495057;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.user-type-btn i {
    font-size: 1.2rem;
}

.user-type-btn.active {
    border-color: #007bff;
    color: #007bff;
    background: #f8f9ff;
}

.form-section {
    margin-bottom: 2rem;
}

.form-section h2 {
    font-size: 1.5rem;
    color: #333;
    margin-bottom: 1.5rem;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #333;
    font-weight: 500;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.password-requirements {
    display: block;
    font-size: 0.85rem;
    color: #666;
    margin-top: 0.5rem;
}

.form-actions {
    text-align: center;
    margin-top: 2rem;
}

.btn {
    padding: 0.75rem 2rem;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
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

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .register-content {
        padding: 1.5rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userTypeButtons = document.querySelectorAll('.user-type-btn');
    const userTypeInput = document.getElementById('user_type');
    const jobSeekerFields = document.getElementById('jobSeekerFields');
    const employerFields = document.getElementById('employerFields');
    const form = document.getElementById('registerForm');

    // Toggle user type fields
    userTypeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userType = this.getAttribute('data-type');
            
            // Update active button
            userTypeButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Update hidden input
            userTypeInput.value = userType;
            
            // Show/hide appropriate fields
            if (userType === 'employer') {
                jobSeekerFields.style.display = 'none';
                employerFields.style.display = 'block';
                
                // Update required fields
                document.querySelectorAll('#jobSeekerFields [required]').forEach(field => {
                    field.removeAttribute('required');
                });
                document.querySelectorAll('#employerFields input, #employerFields select, #employerFields textarea').forEach(field => {
                    if (!field.id.includes('website')) {
                        field.setAttribute('required', '');
                    }
                });
            } else {
                jobSeekerFields.style.display = 'block';
                employerFields.style.display = 'none';
                
                // Update required fields
                document.querySelectorAll('#employerFields [required]').forEach(field => {
                    field.removeAttribute('required');
                });
                document.querySelectorAll('#jobSeekerFields input, #jobSeekerFields select').forEach(field => {
                    if (!field.id.includes('bio') && !field.id.includes('experience')) {
                        field.setAttribute('required', '');
                    }
                });
            }
        });
    });

    // Form validation
    form.addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match!');
            return;
        }
        
        if (password.length < 8) {
            e.preventDefault();
            alert('Password must be at least 8 characters long!');
            return;
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?> 