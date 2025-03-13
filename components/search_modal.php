<!-- Advanced Search Modal -->
<div id="search-modal" class="modal">
    <div class="modal-content">
        <span class="modal-close">&times;</span>
        <h2 class="modal-title">Advanced Job Search</h2>
        <div class="modal-body">
            <form id="advanced-search-form">
                <div class="form-group">
                    <label for="search-keywords">Keywords</label>
                    <input type="text" id="search-keywords" name="q" class="form-control" placeholder="Job title, skills, or keywords">
                </div>
                
                <div class="form-group">
                    <label for="search-category">Category</label>
                    <select id="search-category" name="category" class="form-control">
                        <option value="">All Categories</option>
                        <?php foreach ($jobCategories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['id']); ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="search-location">Location</label>
                    <select id="search-location" name="location" class="form-control">
                        <option value="">All Locations</option>
                        <?php foreach ($locations as $location): ?>
                            <option value="<?php echo htmlspecialchars($location); ?>">
                                <?php echo htmlspecialchars($location); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="search-job-type">Job Type</label>
                    <select id="search-job-type" name="job_type" class="form-control">
                        <option value="">All Types</option>
                        <option value="part_time">Part Time</option>
                        <option value="full_time">Full Time</option>
                        <option value="contract">Contract</option>
                        <option value="temporary">Temporary</option>
                        <option value="internship">Internship</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="search-salary">Minimum Salary</label>
                    <input type="number" id="search-salary" name="min_salary" class="form-control" placeholder="Minimum salary">
                </div>
                
                <div class="form-group">
                    <label>Experience Level</label>
                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" name="experience[]" value="entry"> Entry Level
                        </label>
                        <label>
                            <input type="checkbox" name="experience[]" value="intermediate"> Intermediate
                        </label>
                        <label>
                            <input type="checkbox" name="experience[]" value="expert"> Expert
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Posted Within</label>
                    <select name="posted_within" class="form-control">
                        <option value="">Any Time</option>
                        <option value="1">Last 24 hours</option>
                        <option value="7">Last 7 days</option>
                        <option value="14">Last 14 days</option>
                        <option value="30">Last 30 days</option>
                    </select>
                </div>
                
                <div class="form-buttons">
                    <button type="submit" class="btn btn-primary btn-large">Search Jobs</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>
