<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

// Initialize search parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$job_type = isset($_GET['job_type']) ? $_GET['job_type'] : '';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';

// Base query
$query = "SELECT j.*, e.company_name 
          FROM jobs j 
          LEFT JOIN employers e ON j.employer_id = e.id 
          WHERE j.status = 'open'";

// Add search conditions
if (!empty($search)) {
    $query .= " AND (j.title LIKE ? OR j.description LIKE ?)";
}
if (!empty($job_type)) {
    $query .= " AND j.job_type = ?";
}
if (!empty($location)) {
    $query .= " AND j.location LIKE ?";
}

$query .= " ORDER BY j.posted_date DESC";

// Prepare and execute query
try {
    $stmt = $pdo->prepare($query);
    
    $paramIndex = 1;
    if (!empty($search)) {
        $searchParam = "%$search%";
        $stmt->bindValue($paramIndex++, $searchParam);
        $stmt->bindValue($paramIndex++, $searchParam);
    }
    if (!empty($job_type)) {
        $stmt->bindValue($paramIndex++, $job_type);
    }
    if (!empty($location)) {
        $stmt->bindValue($paramIndex++, "%$location%");
    }
    
    $stmt->execute();
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Error in job search: " . $e->getMessage());
    $jobs = [];
}

// Get all job types for filter
$job_types = $pdo->query("SELECT DISTINCT job_type FROM jobs WHERE job_type IS NOT NULL ORDER BY job_type")->fetchAll(PDO::FETCH_COLUMN);
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
                    
                    <div class="job-type-select">
                        <i class="fas fa-briefcase"></i>
                        <select name="job_type">
                            <option value="">All Job Types</option>
                            <?php foreach ($job_types as $type): ?>
                                <option value="<?php echo htmlspecialchars($type); ?>" 
                                    <?php echo $job_type === $type ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $type))); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="search-button">
                        <i class="fas fa-search"></i> Search Jobs
                    </button>
                </div>
            </form>
        </section>

        <section class="results-section">
            <div class="results-header">
                <h2>Search Results</h2>
                <p class="results-count"><?php echo count($jobs); ?> jobs found</p>
            </div>

            <div class="jobs-grid">
                <?php if ($jobs): ?>
                    <?php foreach ($jobs as $job): ?>
                        <div class="job-card">
                            <div class="job-header">
                                <div class="job-title-company">
                                    <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                                    <p class="company-name"><?php echo htmlspecialchars($job['company_name']); ?></p>
                                </div>
                            </div>
                            <div class="job-details">
                                <p class="location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location']); ?></p>
                                <p class="job-type"><i class="fas fa-briefcase"></i> <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $job['job_type']))); ?></p>
                                <p class="salary"><i class="fas fa-money-bill-wave"></i> <?php echo htmlspecialchars($job['salary_range']); ?></p>
                            </div>
                            <div class="job-description">
                                <?php echo substr(htmlspecialchars($job['description']), 0, 150) . '...'; ?>
                            </div>
                            <div class="job-footer">
                                <a href="job-details.php?id=<?php echo $job['id']; ?>" class="btn btn-primary">View Details</a>
                                <span class="posted-date">Posted <?php echo timeAgo($job['posted_date']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-jobs">No jobs found matching your criteria.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</main>

<style>
.find-jobs-page {
    padding: 2rem 0;
    background-color: #f8f9fa;
}

.search-section {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.search-section h1 {
    text-align: center;
    margin-bottom: 2rem;
    color: #333;
}

.search-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr auto;
    gap: 1rem;
    align-items: center;
}

.search-input,
.location-input,
.job-type-select {
    position: relative;
}

.search-input i,
.location-input i,
.job-type-select i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
}

input[type="text"],
select {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.search-button {
    background: #007bff;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.search-button:hover {
    background: #0056b3;
}

.results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.results-count {
    color: #666;
}

.jobs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
}

.job-card {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.job-card:hover {
    transform: translateY(-5px);
}

.no-jobs {
    text-align: center;
    color: #666;
    grid-column: 1 / -1;
    padding: 2rem;
}

@media (max-width: 768px) {
    .search-grid {
        grid-template-columns: 1fr;
    }
    
    .jobs-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?> 