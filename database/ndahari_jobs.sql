-- Create companies table
CREATE TABLE IF NOT EXISTS companies (
    company_id INT PRIMARY KEY AUTO_INCREMENT,
    company_name VARCHAR(255) NOT NULL,
    logo_url VARCHAR(255),
    description TEXT,
    website VARCHAR(255),
    location VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create jobs table
CREATE TABLE IF NOT EXISTS jobs (
    job_id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    requirements TEXT,
    responsibilities TEXT,
    location VARCHAR(255) NOT NULL,
    salary VARCHAR(100) NOT NULL,
    job_type ENUM('full_time', 'part_time', 'temporary', 'contract', 'weekend', 'evening', 'seasonal', 'internship') NOT NULL,
    category VARCHAR(100) NOT NULL,
    experience_level VARCHAR(100),
    education_level VARCHAR(100),
    posted_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deadline_date DATE,
    status ENUM('active', 'closed', 'draft') DEFAULT 'active',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(company_id) ON DELETE CASCADE
);

-- Create job_applications table
CREATE TABLE IF NOT EXISTS job_applications (
    application_id INT PRIMARY KEY AUTO_INCREMENT,
    job_id INT NOT NULL,
    user_id INT NOT NULL,
    cover_letter TEXT,
    resume_url VARCHAR(255),
    status ENUM('pending', 'reviewed', 'shortlisted', 'rejected', 'accepted') DEFAULT 'pending',
    applied_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE CASCADE
);

-- Insert sample company data
INSERT INTO companies (company_name, logo_url, description, website, location) VALUES
('Tech Solutions Inc.', 'images/companies/tech-solutions.png', 'Leading technology solutions provider', 'https://techsolutions.com', 'New York, NY'),
('Creative Minds Agency', 'images/companies/creative-minds.png', 'Creative digital agency', 'https://creativeminds.com', 'Los Angeles, CA'),
('Green Earth Co.', 'images/companies/green-earth.png', 'Sustainable solutions provider', 'https://greenearth.com', 'Seattle, WA');

-- Insert sample jobs data
INSERT INTO jobs (company_id, title, description, requirements, responsibilities, location, salary, job_type, category, experience_level, education_level) VALUES
(1, 'Junior Web Developer', 'Looking for a passionate web developer to join our team...', 'HTML, CSS, JavaScript, PHP\nBasic understanding of web frameworks\nProblem-solving skills', 'Develop and maintain web applications\nCollaborate with the design team\nOptimize application performance', 'New York, NY', '$25-35/hour', 'part_time', 'Technology', '0-2 years', 'Bachelor''s degree'),
(2, 'Social Media Manager', 'Help manage our clients'' social media presence...', 'Experience with social media platforms\nExcellent communication skills\nCreative mindset', 'Create and schedule social media content\nAnalyze social media metrics\nEngage with followers', 'Remote', '$20-30/hour', 'contract', 'Marketing', '2-3 years', 'Bachelor''s degree'),
(3, 'Environmental Research Assistant', 'Support our research team in environmental studies...', 'Background in environmental science\nData analysis skills\nAttention to detail', 'Collect and analyze environmental data\nPrepare research reports\nConduct field studies', 'Seattle, WA', '$22-28/hour', 'temporary', 'Science', '1-2 years', 'Bachelor''s degree'); 