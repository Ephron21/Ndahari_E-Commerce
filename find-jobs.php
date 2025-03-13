<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

// Initialize search parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Base query
$query = "SELECT jobs.*, companies.company_name, companies.logo_url 
          FROM jobs 
          LEFT JOIN companies ON jobs.company_id = companies.company_id 
          WHERE jobs.status = 'active'";

// Add search conditions
if (!empty($search)) {
    $query .= " AND (jobs.title LIKE ? OR jobs.description LIKE ?)";
}
if (!empty($category)) {
    $query .= " AND jobs.category = ?";
}
if (!empty($location)) {
    $query .= " AND jobs.location LIKE ?";
}
if (!empty($type)) {
    $query .= " AND jobs.job_type = ?";
}

$query .= " ORDER BY jobs.posted_date DESC";

// Prepare and execute query
try {
    $stmt = $pdo->prepare($query);
    
    $paramIndex = 1;
    if (!empty($search)) {
        $searchParam = "%$search%";
        $stmt->bindValue($paramIndex++, $searchParam);
        $stmt->bindValue($paramIndex++, $searchParam);
    }
    if (!empty($category)) {
        $stmt->bindValue($paramIndex++, $category);
    }
    if (!empty($location)) {
        $stmt->bindValue($paramIndex++, "%$location%");
    }
    if (!empty($type)) {
        $stmt->bindValue($paramIndex++, $type);
    }
    
    $stmt->execute();
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Error in job search: " . $e->getMessage());
    $jobs = [];
}

// Get all categories for filter
$categories = $pdo->query("SELECT DISTINCT category FROM jobs WHERE category IS NOT NULL ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
?>

<link rel="stylesheet" href="public/css/find-jobs.css">

<main class="find-jobs-page">
    <div class="container">
        <section class="search-section">
            <h1>Find Your Perfect Part-Time Job</h1>
            <form class="search-form" action="find-jobs.php" method="GET">
                <div class="search-grid">
                    <div class="search-input">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Job title or keywords" value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    
                    <div class="location-input">
                        <i class="fas fa-map-marker-alt"></i>
                        <input type="text" name="location" placeholder="Location" value="<?php echo htmlspecialchars($location); ?>">
                    </div>
                    
                    <div class="category-select">
                        <i class="fas fa-briefcase"></i>
                        <select name="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>" 
                                    <?php echo $category === $cat ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="type-select">
                        <i class="fas fa-clock"></i>
                        <select name="type">
                            <option value="">All Types</option>
                            <option value="part_time" <?php echo $type === 'part_time' ? 'selected' : ''; ?>>Part Time</option>
                            <option value="temporary" <?php echo $type === 'temporary' ? 'selected' : ''; ?>>Temporary</option>
                            <option value="contract" <?php echo $type === 'contract' ? 'selected' : ''; ?>>Contract</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="search-button">Search Jobs</button>
                </div>
            </form>
        </section>

        <section class="jobs-section">
            <div class="jobs-header">
                <h2>Available Jobs</h2>
                <p><?php echo count($jobs); ?> jobs found</p>
            </div>

            <div class="jobs-grid">
                <?php if (empty($jobs)): ?>
                    <div class="no-jobs-found">
                        <i class="fas fa-search"></i>
                        <h3>No jobs found</h3>
                        <p>Try adjusting your search criteria or browse all available jobs.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($jobs as $job): ?>
                        <div class="job-card">
                            <div class="job-card-header">
                                <div class="company-logo">
                                    <img src="<?php echo htmlspecialchars($job['logo_url'] ?? 'images/default-company-logo.png'); ?>" 
                                         alt="<?php echo htmlspecialchars($job['company_name']); ?>" 
                                         onerror="this.src='images/default-company-logo.png'">
                                </div>
                                <div class="job-info">
                                    <h3 class="job-title">
                                        <a href="job-details.php?id=<?php echo $job['job_id']; ?>">
                                            <?php echo htmlspecialchars($job['title']); ?>
                                        </a>
                                    </h3>
                                    <p class="company-name"><?php echo htmlspecialchars($job['company_name']); ?></p>
                                </div>
                            </div>
                            
                            <div class="job-card-body">
                                <div class="job-meta">
                                    <span class="location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo htmlspecialchars($job['location']); ?>
                                    </span>
                                    <span class="job-type">
                                        <i class="fas fa-clock"></i>
                                        <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $job['job_type']))); ?>
                                    </span>
                                    <span class="salary">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <?php echo htmlspecialchars($job['salary']); ?>
                                    </span>
                                </div>
                                <p class="job-description">
                                    <?php echo htmlspecialchars(substr($job['description'], 0, 150)) . '...'; ?>
                                </p>
                            </div>
                            
                            <div class="job-card-footer">
                                <span class="posted-date">
                                    <i class="fas fa-calendar-alt"></i>
                                    Posted <?php echo timeAgo($job['posted_date']); ?>
                                </span>
                                <a href="job-details.php?id=<?php echo $job['job_id']; ?>" class="view-job-btn">View Details</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?> 