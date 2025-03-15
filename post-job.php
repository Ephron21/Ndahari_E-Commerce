<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check if user is logged in and is an employer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'employer') {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

// Check if hours_per_week column exists
try {
    $checkColumn = $pdo->query("SHOW COLUMNS FROM jobs LIKE 'hours_per_week'");
    $hoursColumnExists = $checkColumn->rowCount() > 0;
} catch (PDOException $e) {
    $hoursColumnExists = false;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate input
        $required_fields = ['title', 'description', 'job_type', 'location', 'salary_range', 'requirements', 'responsibilities', 'deadline'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("All fields marked with * are required");
            }
        }

        // Validate job type
        $valid_job_types = [
            'IT & Software', 'Education & Training', 'Healthcare', 'Design & Creative',
            'Customer Service', 'Sales & Marketing', 'Administrative', 'Hospitality & Tourism',
            'Retail', 'Finance & Accounting', 'Engineering', 'Other'
        ];
        
        $job_type = trim($_POST['job_type']);
        if (!in_array($job_type, $valid_job_types)) {
            throw new Exception("Invalid job type. Please select from the provided options.");
        }

        // Sanitize input
        $job_title = htmlspecialchars(trim($_POST['title']));
        $job_description = htmlspecialchars(trim($_POST['description']));
        $location = htmlspecialchars(trim($_POST['location']));
        $salary_range = htmlspecialchars(trim($_POST['salary_range']));
        $requirements = htmlspecialchars(trim($_POST['requirements']));
        $responsibilities = htmlspecialchars(trim($_POST['responsibilities']));
        $deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;
        
        // Only get hours_per_week if the column exists
        $hours_per_week = ($hoursColumnExists && !empty($_POST['hours_per_week'])) ? 
                          intval($_POST['hours_per_week']) : null;

        // Prepare SQL based on whether hours_per_week column exists
        if ($hoursColumnExists) {
            $sql = "
                INSERT INTO jobs (
                    employer_id, title, description, job_type, 
                    location, salary_range, hours_per_week, requirements, responsibilities,
                    application_deadline, status, posted_date
                ) VALUES (
                    :employer_id, :title, :description, :job_type,
                    :location, :salary_range, :hours_per_week, :requirements, :responsibilities,
                    :deadline, 'open', NOW()
                )
            ";
            $params = [
                'employer_id' => $_SESSION['user_id'],
                'title' => $job_title,
                'description' => $job_description,
                'job_type' => $job_type,
                'location' => $location,
                'salary_range' => $salary_range,
                'hours_per_week' => $hours_per_week,
                'requirements' => $requirements,
                'responsibilities' => $responsibilities,
                'deadline' => $deadline
            ];
        } else {
            $sql = "
                INSERT INTO jobs (
                    employer_id, title, description, job_type, 
                    location, salary_range, requirements, responsibilities,
                    application_deadline, status, posted_date
                ) VALUES (
                    :employer_id, :title, :description, :job_type,
                    :location, :salary_range, :requirements, :responsibilities,
                    :deadline, 'open', NOW()
                )
            ";
            $params = [
                'employer_id' => $_SESSION['user_id'],
                'title' => $job_title,
                'description' => $job_description,
                'job_type' => $job_type,
                'location' => $location,
                'salary_range' => $salary_range,
                'requirements' => $requirements,
                'responsibilities' => $responsibilities,
                'deadline' => $deadline
            ];
        }

        // Insert job posting
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $success = "Job posted successfully!";
        
        // Redirect to home page after successful posting
        header("Location: index.php?success=1");
        exit();

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Include header
require_once 'includes/header.php';
?>

<main class="post-job">
    <div class="container">
        <h1>Post a New Job</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="job-form">
            <div class="form-tips">
                <h3>Tips for Creating an Effective Job Posting</h3>
                <p>Well-structured job postings attract more qualified candidates. Follow these guidelines:</p>
                <ul>
                    <li>Be specific and detailed in your descriptions</li>
                    <li>Use bullet points (•) for requirements and responsibilities</li>
                    <li>Include information about work schedule and location</li>
                    <li>Clearly state qualifications and experience needed</li>
                </ul>
            </div>

            <div class="form-group">
                <label for="title">Job Title *</label>
                <input type="text" id="title" name="title" class="form-control" 
                       placeholder="e.g., Part-Time Web Developer, Weekend Nurse Assistant"
                       value="<?php echo $_POST['title'] ?? ''; ?>" required>
                <small>Be specific and include key terms job seekers might search for</small>
            </div>

            <div class="form-group">
                <label for="job_type">Job Category *</label>
                <select id="job_type" name="job_type" class="form-control" required>
                    <option value="">Select Job Category</option>
                    <option value="IT & Software">IT & Software</option>
                    <option value="Education & Training">Education & Training</option>
                    <option value="Healthcare">Healthcare</option>
                    <option value="Design & Creative">Design & Creative</option>
                    <option value="Customer Service">Customer Service</option>
                    <option value="Sales & Marketing">Sales & Marketing</option>
                    <option value="Administrative">Administrative</option>
                    <option value="Hospitality & Tourism">Hospitality & Tourism</option>
                    <option value="Retail">Retail</option>
                    <option value="Finance & Accounting">Finance & Accounting</option>
                    <option value="Engineering">Engineering</option>
                    <option value="Other">Other</option>
                </select>
                <small>Choose the category that best matches the job</small>
            </div>

            <div class="form-group">
                <label for="location">Location *</label>
                <input type="text" id="location" name="location" class="form-control" 
                       placeholder="e.g., Nairobi, Mombasa, Kisumu"
                       value="<?php echo $_POST['location'] ?? ''; ?>" required>
                <small>Specify the city or region where the job is located</small>
            </div>

            <div class="form-group">
                <label for="salary_range">Salary Range *</label>
                <input type="text" id="salary_range" name="salary_range" class="form-control" 
                       placeholder="e.g., 30,000 - 45,000 KSH per month" 
                       value="<?php echo $_POST['salary_range'] ?? ''; ?>" required>
                <small>Including salary information increases application rates</small>
            </div>

            <?php if ($hoursColumnExists): ?>
            <div class="form-group">
                <label for="hours_per_week">Hours Per Week *</label>
                <input type="number" id="hours_per_week" name="hours_per_week" class="form-control"
                       min="1" max="40" placeholder="e.g., 20"
                       value="<?php echo $_POST['hours_per_week'] ?? ''; ?>" required>
                <small>For part-time positions, typically between 10-30 hours</small>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="description">Job Description *</label>
                <textarea id="description" name="description" class="form-control" 
                          rows="8" required placeholder="Provide a detailed overview of the job. Include:

• Brief introduction about your company
• Overview of the role and its importance
• Work schedule (hours per week, days, shifts)
• Work location (office, remote, hybrid)
• Contract type and duration

Example:
[Your Company] is seeking a [Job Title] to join our team. This position offers flexible hours and the opportunity to work on [type of projects/tasks].

The ideal candidate will have [key skills/qualities]. This role is perfect for professionals looking to [benefit of the job].

Hours: [X] hours per week
Location: [Location] (with [any remote options])
Contract: [contract details]"><?php echo $_POST['description'] ?? ''; ?></textarea>
                <small>A well-written description helps attract qualified candidates</small>
            </div>

            <div class="form-group">
                <label for="requirements">Requirements *</label>
                <textarea id="requirements" name="requirements" class="form-control" 
                          rows="8" required placeholder="List qualifications needed for this role. Use bullet points (•) for better readability:

• Education requirements
• Years of experience
• Technical skills
• Certifications
• Soft skills
• Language proficiency

Example:
• Bachelor's degree in [field] or equivalent experience
• Minimum [X] years of experience in [relevant area]
• Proficiency in [specific skills/tools]
• Strong communication and teamwork abilities
• [Any certifications or specific requirements]"><?php echo $_POST['requirements'] ?? ''; ?></textarea>
                <small>Be clear about which requirements are essential vs. preferred</small>
            </div>

            <div class="form-group">
                <label for="responsibilities">Responsibilities *</label>
                <textarea id="responsibilities" name="responsibilities" class="form-control" 
                          rows="8" required placeholder="List the main duties and tasks for this position. Use bullet points (•) for better readability:

• Daily tasks and activities
• Projects they will work on
• Teams they will collaborate with
• Reporting structure
• Goals and objectives

Example:
• [Specific task or responsibility]
• Collaborate with [team/department] to [achieve what]
• Maintain [systems/records/equipment]
• Report to [position] on [frequency/basis]
• Ensure [quality/compliance/standards]"><?php echo $_POST['responsibilities'] ?? ''; ?></textarea>
                <small>Be specific about what the job entails day-to-day</small>
            </div>

            <div class="form-group">
                <label for="deadline">Application Deadline *</label>
                <input type="date" id="deadline" name="deadline" class="form-control" 
                       value="<?php echo $_POST['deadline'] ?? ''; ?>" required>
                <small>Recommended: 2-4 weeks from posting date</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Post Job</button>
                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</main>

<style>
.post-job {
    padding: 2rem 0;
    background-color: #f8f9fa;
    min-height: calc(100vh - 60px);
}

.post-job h1 {
    margin-bottom: 2rem;
    color: #333;
    text-align: center;
}

.job-form {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    max-width: 800px;
    margin: 0 auto;
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

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
    font-family: inherit;
}

.form-group small {
    display: block;
    color: #666;
    font-size: 0.85rem;
    margin-top: 0.5rem;
}

textarea.form-control {
    resize: vertical;
    line-height: 1.5;
}

.form-control:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

.form-tips {
    background-color: #f0f7ff;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 2rem;
    border-left: 4px solid #007bff;
}

.form-tips h3 {
    margin-top: 0;
    color: #0056b3;
    font-size: 1.2rem;
}

.form-tips p {
    margin-bottom: 0.75rem;
}

.form-tips ul {
    margin: 0;
    padding-left: 1.5rem;
}

.form-tips li {
    margin-bottom: 0.5rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
    font-weight: 500;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #545b62;
}

.alert {
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 4px;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

@media (max-width: 768px) {
    .job-form {
        padding: 1.5rem;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
        text-align: center;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?> 