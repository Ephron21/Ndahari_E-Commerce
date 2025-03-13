<!-- Jobs Section -->
<section class="jobs-section" id="jobs">
    <div class="container">
        <h2 data-aos="fade-up">Featured Job Opportunities</h2>
        <div class="search-filters" data-aos="fade-up" data-aos-delay="100">
            <input type="text" class="search-input" placeholder="Search for jobs...">
            <select class="filter-select" name="category">
                <option value="">All Categories</option>
                <?php foreach ($jobCategories as $category): ?>
                    <option value="<?php echo htmlspecialchars($category['id']); ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select class="filter-select" name="location">
                <option value="">All Locations</option>
                <?php foreach ($locations as $location): ?>
                    <option value="<?php echo htmlspecialchars($location); ?>">
                        <?php echo htmlspecialchars($location); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button class="search-button"><i class="fas fa-search"></i> Search</button>
        </div>

        <div class="jobs-grid">
            <?php if (empty($featuredJobs)): ?>
                <div class="no-jobs" data-aos="fade-up">
                    <p>No featured jobs available at the moment. Please check back later.</p>
                </div>
            <?php else: ?>
                <?php foreach ($featuredJobs as $index => $job): ?>
                    <div class="job-card" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                        <div class="job-header">
                            <div class="job-logo">
                                <?php if (!empty($job['logo'])): ?>
                                    <img src="<?php echo htmlspecialchars($job['logo']); ?>" alt="<?php echo htmlspecialchars($job['company_name']); ?>">
                                <?php else: ?>
                                    <?php echo getJobIcon($job['category']); ?>
                                <?php endif; ?>
                            </div>
                            <div class="job-title-company">
                                <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                                <div class="company-name"><?php echo htmlspecialchars($job['company_name']); ?></div>
                            </div>
                        </div>
                        <div class="job-body">
                            <div class="job-details">
                                <span class="job-detail"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location']); ?></span>
                                <span class="job-detail"><i class="fas fa-clock"></i> <?php echo htmlspecialchars($job['job_type']); ?></span>
                                <span class="job-detail"><i class="fas fa-money-bill-wave"></i> <?php echo htmlspecialchars($job['salary_range']); ?></span>
                            </div>
                            <div class="job-description">
                                <?php echo htmlspecialchars(substr($job['description'], 0, 150) . '...'); ?>
                            </div>
                        </div>
                        <div class="job-footer">
                            <div class="job-posted">
                                <i class="far fa-calendar-alt"></i> Posted <?php echo formatDate($job['posted_date']); ?>
                            </div>
                            <a href="job-details.php?id=<?php echo $job['id']; ?>" class="job-apply">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="view-all-jobs" data-aos="fade-up">
            <a href="find-jobs.php" class="cta-button cta-primary">View All Jobs</a>
        </div>
    </div>
</section>
