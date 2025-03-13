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

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate input
        $required_fields = ['title', 'description', 'job_type', 'location', 'salary_range', 'requirements'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("All fields are required");
            }
        }

        // Validate job type
        $valid_job_types = ['full_time', 'part_time', 'temporary', 'contract', 'weekend', 'evening', 'seasonal', 'internship'];
        if (!in_array($_POST['job_type'], $valid_job_types)) {
            throw new Exception("Invalid job type. Must be one of: " . implode(', ', $valid_job_types));
        }

        // Sanitize input
        $job_title = htmlspecialchars(trim($_POST['title']));
        $job_description = htmlspecialchars(trim($_POST['description']));
        $job_type = htmlspecialchars(trim($_POST['job_type']));
        $location = htmlspecialchars(trim($_POST['location']));
        $salary_range = htmlspecialchars(trim($_POST['salary_range']));
        $requirements = htmlspecialchars(trim($_POST['requirements']));
        $deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;

        // Insert job posting
        $stmt = $pdo->prepare("
            INSERT INTO jobs (
                employer_id, title, description, job_type, 
                location, salary_range, requirements, responsibilities,
                application_deadline, status, posted_date
            ) VALUES (
                :employer_id, :title, :description, :job_type,
                :location, :salary_range, :requirements, :responsibilities,
                :deadline, 'open', NOW()
            )
        ");

        $stmt->execute([
            'employer_id' => $_SESSION['user_id'],
            'title' => $job_title,
            'description' => $job_description,
            'job_type' => $job_type,
            'location' => $location,
            'salary_range' => $salary_range,
            'requirements' => $requirements,
            'responsibilities' => $requirements,
            'deadline' => $deadline
        ]);

        $success = "Job posted successfully!";
        
        // Redirect to manage jobs page after successful posting
        header("Location: manage-jobs.php?success=1");
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
            <div class="form-group">
                <label for="title">Job Title *</label>
                <input type="text" id="title" name="title" class="form-control" 
                       value="<?php echo $_POST['title'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="job_type">Job Type *</label>
                <select id="job_type" name="job_type" class="form-control" required>
                    <option value="">Select Job Type</option>
                    <option value="full_time">Full Time</option>
                    <option value="part_time">Part Time</option>
                    <option value="temporary">Temporary</option>
                    <option value="contract">Contract</option>
                    <option value="weekend">Weekend</option>
                    <option value="evening">Evening</option>
                    <option value="seasonal">Seasonal</option>
                    <option value="internship">Internship</option>
                </select>
            </div>

            <div class="form-group">
                <label for="location">Location *</label>
                <input type="text" id="location" name="location" class="form-control" 
                       value="<?php echo $_POST['location'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="salary_range">Salary Range *</label>
                <input type="text" id="salary_range" name="salary_range" class="form-control" 
                       placeholder="e.g., $50,000 - $70,000" 
                       value="<?php echo $_POST['salary_range'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Job Description *</label>
                <textarea id="description" name="description" class="form-control" 
                          rows="6" required><?php echo $_POST['description'] ?? ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="requirements">Requirements *</label>
                <textarea id="requirements" name="requirements" class="form-control" 
                          rows="4" required><?php echo $_POST['requirements'] ?? ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="deadline">Application Deadline</label>
                <input type="date" id="deadline" name="deadline" class="form-control" 
                       value="<?php echo $_POST['deadline'] ?? ''; ?>">
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
}

textarea.form-control {
    resize: vertical;
}

.form-control:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
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