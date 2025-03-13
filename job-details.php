<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

// Get job ID from URL
$job_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch job details with company information
$query = "SELECT j.*, c.company_name, c.logo_url, c.description as company_description, c.website, c.location as company_location
          FROM jobs j
          LEFT JOIN companies c ON j.company_id = c.company_id
          WHERE j.job_id = ? AND j.status = 'active'";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute([$job_id]);
    $job = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$job) {
        header("Location: find-jobs.php");
        exit();
    }

    // Update view count
    $pdo->prepare("UPDATE jobs SET views = views + 1 WHERE job_id = ?")->execute([$job_id]);

} catch (PDOException $e) {
    error_log("Error fetching job details: " . $e->getMessage());
    header("Location: find-jobs.php");
    exit();
}

// Check if user has already applied
$has_applied = false;
if (isset($_SESSION['user_id'])) {
    $check_application = $pdo->prepare("SELECT application_id FROM job_applications WHERE job_id = ? AND user_id = ?");
    $check_application->execute([$job_id, $_SESSION['user_id']]);
    $has_applied = $check_application->rowCount() > 0;
}

// Handle job application submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    try {
        // Handle file upload
        $resume_url = '';
        if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/resumes/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
            $resume_url = $upload_dir . uniqid('resume_') . '.' . $file_extension;
            
            move_uploaded_file($_FILES['resume']['tmp_name'], $resume_url);
        }

        // Insert application
        $stmt = $pdo->prepare("INSERT INTO job_applications (job_id, user_id, cover_letter, resume_url) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $job_id,
            $_SESSION['user_id'],
            $_POST['cover_letter'] ?? '',
            $resume_url
        ]);

        $success_message = "Your application has been submitted successfully!";
        $has_applied = true;
    } catch (PDOException $e) {
        error_log("Error submitting application: " . $e->getMessage());
        $error_message = "There was an error submitting your application. Please try again.";
    }
}
?>

<link rel="stylesheet" href="public/css/job-details.css">

<main class="job-details-page">
    <div class="container">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <div class="job-header">
            <div class="company-info">
                <img src="<?php echo htmlspecialchars($job['logo_url'] ?? 'images/default-company-logo.png'); ?>" 
                     alt="<?php echo htmlspecialchars($job['company_name']); ?>"
                     class="company-logo">
                <div class="company-details">
                    <h1><?php echo htmlspecialchars($job['title']); ?></h1>
                    <p class="company-name">
                        <i class="fas fa-building"></i>
                        <?php echo htmlspecialchars($job['company_name']); ?>
                    </p>
                </div>
            </div>
            
            <div class="job-meta">
                <div class="meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?php echo htmlspecialchars($job['location']); ?></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-clock"></i>
                    <span><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $job['job_type']))); ?></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-money-bill-wave"></i>
                    <span><?php echo htmlspecialchars($job['salary']); ?></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Posted <?php echo timeAgo($job['posted_date']); ?></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-eye"></i>
                    <span><?php echo number_format($job['views']); ?> views</span>
                </div>
            </div>
        </div>

        <div class="job-content">
            <div class="main-content">
                <section class="job-description">
                    <h2>Job Description</h2>
                    <p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
                </section>

                <section class="job-requirements">
                    <h2>Requirements</h2>
                    <ul>
                        <?php 
                        $requirements = explode("\n", $job['requirements']);
                        foreach ($requirements as $requirement):
                            if (trim($requirement)):
                        ?>
                            <li><?php echo htmlspecialchars(trim($requirement)); ?></li>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </ul>
                </section>

                <section class="job-responsibilities">
                    <h2>Responsibilities</h2>
                    <ul>
                        <?php 
                        $responsibilities = explode("\n", $job['responsibilities']);
                        foreach ($responsibilities as $responsibility):
                            if (trim($responsibility)):
                        ?>
                            <li><?php echo htmlspecialchars(trim($responsibility)); ?></li>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </ul>
                </section>

                <section class="company-description">
                    <h2>About <?php echo htmlspecialchars($job['company_name']); ?></h2>
                    <p><?php echo nl2br(htmlspecialchars($job['company_description'])); ?></p>
                    <?php if ($job['website']): ?>
                        <a href="<?php echo htmlspecialchars($job['website']); ?>" target="_blank" class="company-website">
                            <i class="fas fa-globe"></i> Visit Website
                        </a>
                    <?php endif; ?>
                </section>
            </div>

            <aside class="job-sidebar">
                <div class="application-card">
                    <h3>Apply for this position</h3>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <p>Please <a href="login.php">log in</a> or <a href="register.php">register</a> to apply for this job.</p>
                    <?php elseif ($has_applied): ?>
                        <p class="already-applied">
                            <i class="fas fa-check-circle"></i>
                            You have already applied for this position
                        </p>
                    <?php else: ?>
                        <form action="job-details.php?id=<?php echo $job_id; ?>" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="cover_letter">Cover Letter</label>
                                <textarea id="cover_letter" name="cover_letter" rows="6" required></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="resume">Resume (PDF, DOC, DOCX)</label>
                                <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" required>
                            </div>

                            <button type="submit" class="apply-button">Submit Application</button>
                        </form>
                    <?php endif; ?>
                </div>

                <div class="job-overview">
                    <h3>Job Overview</h3>
                    <ul>
                        <li>
                            <i class="fas fa-graduation-cap"></i>
                            <div>
                                <span class="label">Education Level</span>
                                <span class="value"><?php echo htmlspecialchars($job['education_level']); ?></span>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-briefcase"></i>
                            <div>
                                <span class="label">Experience Level</span>
                                <span class="value"><?php echo htmlspecialchars($job['experience_level']); ?></span>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-folder"></i>
                            <div>
                                <span class="label">Category</span>
                                <span class="value"><?php echo htmlspecialchars($job['category']); ?></span>
                            </div>
                        </li>
                        <?php if ($job['deadline_date']): ?>
                        <li>
                            <i class="fas fa-calendar-times"></i>
                            <div>
                                <span class="label">Application Deadline</span>
                                <span class="value"><?php echo date('F j, Y', strtotime($job['deadline_date'])); ?></span>
                            </div>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?> 