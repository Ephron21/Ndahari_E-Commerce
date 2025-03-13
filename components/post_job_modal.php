<!-- Post Job Modal -->
<div id="post-job-modal" class="modal">
    <div class="modal-content">
        <span class="modal-close">&times;</span>
        <h2 class="modal-title">Post a New Job</h2>
        
        <div class="modal-body">
            <?php if (!isUserLoggedIn()): ?>
                <div class="login-notice">
                    <p>You need to be logged in to post a job.</p>
                    <div class="form-buttons">
                        <a href="login.php?redirect=post-job.php" class="btn btn-primary">Sign In</a>
                        <a href="register.php?type=employer" class="btn btn-secondary">Register as Employer</a>
                    </div>
                </div>
            <?php else: ?>
                <form id="job-post-form">
                    <div class="form-group">
                        <label for="job-title">Job Title</label>
                        <input type="text" id="job-title" name="title" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="job-category">Category</label>
                        <select id="job-category" name="category" class="form-control" required>
                            <option value="">Select Category</option>
                            <?php foreach ($jobCategories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['id']); ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="job-location">Location</label>
                        <input type="text" id="job-location" name="location" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="job-type">Job Type</label>
                        <select id="job-type" name="job_type" class="form-control" required>
                            <option value="part_time">Part Time</option>
                            <option value="full_time">Full Time</option>
                            <option value="contract">Contract</option>
                            <option value="temporary">Temporary</option>
                            <option value="internship">Internship</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="job-salary">Salary Range</label>
                        <input type="text" id="job-salary" name="salary_range" class="form-control" placeholder="e.g. $15-$20 per hour" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="job-description">Job Description</label>
                        <textarea id="job-description" name="description" class="form-control" rows="5" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="job-requirements">Requirements</label>
                        <textarea id="job-requirements" name="requirements" class="form-control" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="job-benefits">Benefits (Optional)</label>
                        <textarea id="job-benefits" name="benefits" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="application-email">Application Email</label>
                        <input type="email" id="application-email" name="application_email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_featured" value="1"> Feature this job (additional fee applies)
                        </label>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="submit" class="btn btn-primary btn-large">Post Job</button>
                        <button type="button" class="btn btn-secondary modal-close">Cancel</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
