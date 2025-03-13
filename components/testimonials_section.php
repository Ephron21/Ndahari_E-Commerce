<!-- Job Categories Section -->
<section class="categories-section" id="categories">
    <div class="container">
        <h2 data-aos="fade-up">Browse By Category</h2>
        <div class="categories-grid">
            <?php if (empty($jobCategories)): ?>
                <p>No categories available.</p>
            <?php else: ?>
                <?php foreach ($jobCategories as $index => $category): ?>
                    <div class="category-card" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                        <div class="category-icon">
                            <?php echo getJobIcon($category['slug']); ?>
                        </div>
                        <h3 class="category-name"><?php echo htmlspecialchars($category['name']); ?></h3>
                        <div class="category-count">
                            <?php echo isset($category['job_count']) ? $category['job_count'] : '0'; ?> Jobs Available
                        </div>
                        <a href="find-jobs.php?category=<?php echo urlencode($category['id']); ?>" class="category-link">Browse Jobs</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
