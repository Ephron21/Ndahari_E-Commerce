<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user data based on user type
try {
    $user_type = $_SESSION['user_type'] ?? 'job_seeker';
    $table = ($user_type === 'employer') ? 'employers' : 'job_seekers';
    $name_field = ($user_type === 'employer') ? 'contact_person' : 'full_name';
    
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user) {
        // Handle case where user is not found
        error_log("User not found: ID {$_SESSION['user_id']} in table $table");
        header("Location: logout.php");
        exit();
    }

    // Initialize variables based on user type
    if ($user_type === 'employer') {
        // Get employer-specific data
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total_jobs FROM jobs WHERE employer_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $jobStats = $stmt->fetch();

        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total_applications 
            FROM job_applications ja 
            JOIN jobs j ON ja.job_id = j.id 
            WHERE j.employer_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $applicationStats = $stmt->fetch();

        // Get recent job applications for employer's jobs
        $stmt = $pdo->prepare("
            SELECT ja.*, j.job_title, js.full_name as applicant_name 
            FROM job_applications ja
            JOIN jobs j ON ja.job_id = j.id
            JOIN job_seekers js ON ja.job_seeker_id = js.id
            WHERE j.employer_id = ?
            ORDER BY ja.created_at DESC
            LIMIT 5
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $applications = $stmt->fetchAll();

    } else {
        // Get job seeker-specific data
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total_applications 
            FROM job_applications 
            WHERE job_seeker_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $applicationStats = $stmt->fetch();

        // Get job seeker's recent applications
        $stmt = $pdo->prepare("
            SELECT ja.*, j.job_title, e.company_name 
            FROM job_applications ja
            JOIN jobs j ON ja.job_id = j.id
            JOIN employers e ON j.employer_id = e.id
            WHERE ja.job_seeker_id = ?
            ORDER BY ja.created_at DESC
            LIMIT 5
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $applications = $stmt->fetchAll();
    }

} catch (PDOException $e) {
    error_log("Error in dashboard.php: " . $e->getMessage());
    $error = "An error occurred while fetching your data. Please try again later.";
}

// Include header
require_once 'includes/header.php';
?>

<main class="dashboard">
    <div class="container">
        <div class="dashboard-header">
            <h1>Welcome back, <?php echo htmlspecialchars($user[$name_field] ?? ''); ?>!</h1>
            <p class="last-login">
                <?php
                $last_login = isset($user['last_login']) && $user['last_login'] ? 
                    date('M d, Y H:i', strtotime($user['last_login'])) : 
                    'First time login';
                echo "Last login: " . $last_login;
                ?>
            </p>
        </div>

        <div class="dashboard-grid">
            <!-- Quick Stats -->
            <div class="dashboard-card stats-card">
                <h2>Your Stats</h2>
                <div class="stats-grid">
                    <?php if ($_SESSION['user_type'] === 'employer'): ?>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo $jobStats['total_jobs'] ?? 0; ?></span>
                            <span class="stat-label">Active Jobs</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo $applicationStats['total_applications'] ?? 0; ?></span>
                            <span class="stat-label">Total Applications</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo $user['profile_views'] ?? 0; ?></span>
                            <span class="stat-label">Profile Views</span>
                        </div>
                    <?php else: ?>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo $applicationStats['total_applications'] ?? 0; ?></span>
                            <span class="stat-label">Applications</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo $user['profile_views'] ?? 0; ?></span>
                            <span class="stat-label">Profile Views</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo $user['jobs_completed'] ?? 0; ?></span>
                            <span class="stat-label">Jobs Completed</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Applications -->
            <div class="dashboard-card applications-card">
                <h2><?php echo $_SESSION['user_type'] === 'employer' ? 'Recent Applications Received' : 'Your Recent Applications'; ?></h2>
                <div class="applications-list">
                    <?php if (!empty($applications)): ?>
                        <?php foreach ($applications as $application): ?>
                            <div class="application-item">
                                <div class="application-details">
                                    <h3><?php echo htmlspecialchars($application['job_title']); ?></h3>
                                    <?php if ($_SESSION['user_type'] === 'employer'): ?>
                                        <p class="applicant-name"><?php echo htmlspecialchars($application['applicant_name']); ?></p>
                                    <?php else: ?>
                                        <p class="company-name"><?php echo htmlspecialchars($application['company_name']); ?></p>
                                    <?php endif; ?>
                                    <p class="application-meta">
                                        Applied: <?php echo date('M d, Y', strtotime($application['created_at'])); ?>
                                    </p>
                                </div>
                                <div class="application-status <?php echo strtolower($application['status']); ?>">
                                    <?php echo ucfirst($application['status']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-data">No applications <?php echo $_SESSION['user_type'] === 'employer' ? 'received' : 'submitted'; ?> yet.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-card actions-card">
                <h2>Quick Actions</h2>
                <div class="quick-actions">
                    <?php if ($_SESSION['user_type'] === 'employer'): ?>
                        <a href="post-job.php" class="action-btn">
                            <i class="fas fa-plus"></i>
                            Post New Job
                        </a>
                        <a href="manage-jobs.php" class="action-btn">
                            <i class="fas fa-briefcase"></i>
                            Manage Jobs
                        </a>
                    <?php else: ?>
                        <a href="find-jobs.php" class="action-btn">
                            <i class="fas fa-search"></i>
                            Find Jobs
                        </a>
                        <a href="saved-jobs.php" class="action-btn">
                            <i class="fas fa-bookmark"></i>
                            Saved Jobs
                        </a>
                    <?php endif; ?>
                    <a href="profile.php" class="action-btn">
                        <i class="fas fa-user-edit"></i>
                        Edit Profile
                    </a>
                    <a href="messages.php" class="action-btn">
                        <i class="fas fa-envelope"></i>
                        Messages
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.dashboard {
    padding: 2rem 0;
    background-color: #f8f9fa;
    min-height: calc(100vh - 60px);
}

.dashboard-header {
    margin-bottom: 2rem;
}

.dashboard-header h1 {
    font-size: 2rem;
    color: #333;
    margin-bottom: 0.5rem;
}

.last-login {
    color: #666;
    font-size: 0.9rem;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

.dashboard-card {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.dashboard-card h2 {
    font-size: 1.25rem;
    color: #333;
    margin-bottom: 1rem;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 0.5rem;
}

/* Stats Card */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.stat-number {
    display: block;
    font-size: 1.5rem;
    font-weight: bold;
    color: #007bff;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
}

/* Applications Card */
.application-item {
    padding: 1rem;
    border-bottom: 1px solid #f0f0f0;
}

.application-details h3 {
    font-size: 1rem;
    margin-bottom: 0.25rem;
}

.applicant-name {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.company-name {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.application-meta {
    font-size: 0.85rem;
    color: #666;
}

/* Status Colors */
.pending {
    background-color: #ffeeba;
    color: #856404;
}

.approved {
    background-color: #d4edda;
    color: #155724;
}

.rejected {
    background-color: #f8d7da;
    color: #721c24;
}

/* Quick Actions */
.quick-actions {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.action-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    color: #333;
    text-decoration: none;
    transition: all 0.3s ease;
}

.action-btn:hover {
    background: #007bff;
    color: white;
    transform: translateY(-2px);
}

.action-btn i {
    margin-right: 0.5rem;
}

.no-data {
    color: #666;
    text-align: center;
    padding: 1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .quick-actions {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?> 