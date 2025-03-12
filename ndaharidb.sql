-- Create the Ndahari database
CREATE DATABASE IF NOT EXISTS Ndahari;

-- Use the Ndahari database
USE Ndahari;

-- Users table for authentication
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('job_seeker', 'employer', 'admin') NOT NULL,
    status ENUM('pending', 'active', 'suspended', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Job Seekers table
CREATE TABLE IF NOT EXISTS job_seekers (
    seeker_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    age INT NOT NULL,
    job_title VARCHAR(100) NOT NULL,
    skills TEXT NOT NULL,
    certifications TEXT,
    id_document VARCHAR(255) NOT NULL,
    resume VARCHAR(255) NOT NULL,
    profile_image VARCHAR(255) NOT NULL,
    province VARCHAR(50) NOT NULL,
    district VARCHAR(50) NOT NULL,
    sector VARCHAR(50) NOT NULL,
    cell VARCHAR(50) NOT NULL,
    village VARCHAR(50) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Job Seeker Availability table
CREATE TABLE IF NOT EXISTS seeker_availability (
    availability_id INT AUTO_INCREMENT PRIMARY KEY,
    seeker_id INT NOT NULL,
    availability_type ENUM('full_time', 'part_time', 'evenings', 'weekends', 'flexible') NOT NULL,
    FOREIGN KEY (seeker_id) REFERENCES job_seekers(seeker_id) ON DELETE CASCADE
);

-- Employers table
CREATE TABLE IF NOT EXISTS employers (
    employer_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    company_name VARCHAR(100) NOT NULL,
    industry VARCHAR(50) NOT NULL,
    company_size VARCHAR(20) NOT NULL,
    company_description TEXT NOT NULL,
    contact_person VARCHAR(100) NOT NULL,
    contact_phone VARCHAR(20) NOT NULL,
    company_website VARCHAR(255),
    company_registration VARCHAR(255) NOT NULL,
    company_logo VARCHAR(255) NOT NULL,
    province VARCHAR(50) NOT NULL,
    district VARCHAR(50) NOT NULL,
    sector VARCHAR(50) NOT NULL,
    cell VARCHAR(50) NOT NULL,
    village VARCHAR(50) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Job Listings table
CREATE TABLE IF NOT EXISTS job_listings (
    job_id INT AUTO_INCREMENT PRIMARY KEY,
    employer_id INT NOT NULL,
    job_title VARCHAR(100) NOT NULL,
    job_description TEXT NOT NULL,
    job_type ENUM('full_time', 'part_time', 'contract', 'temporary', 'internship') NOT NULL,
    salary_range VARCHAR(50),
    required_skills TEXT NOT NULL,
    qualifications TEXT NOT NULL,
    experience_level VARCHAR(50) NOT NULL,
    application_deadline DATE NOT NULL,
    province VARCHAR(50) NOT NULL,
    district VARCHAR(50) NOT NULL,
    sector VARCHAR(50),
    status ENUM('open', 'closed', 'draft') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employer_id) REFERENCES employers(employer_id) ON DELETE CASCADE
);

-- Job Applications table
CREATE TABLE IF NOT EXISTS job_applications (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    seeker_id INT NOT NULL,
    cover_letter TEXT,
    application_status ENUM('pending', 'reviewed', 'shortlisted', 'interviewed', 'offered', 'hired', 'rejected') DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES job_listings(job_id) ON DELETE CASCADE,
    FOREIGN KEY (seeker_id) REFERENCES job_seekers(seeker_id) ON DELETE CASCADE,
    UNIQUE(job_id, seeker_id)
);

-- Seeker Education table
CREATE TABLE IF NOT EXISTS seeker_education (
    education_id INT AUTO_INCREMENT PRIMARY KEY,
    seeker_id INT NOT NULL,
    institution_name VARCHAR(100) NOT NULL,
    degree VARCHAR(100) NOT NULL,
    field_of_study VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    is_current BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (seeker_id) REFERENCES job_seekers(seeker_id) ON DELETE CASCADE
);

-- Seeker Work Experience table
CREATE TABLE IF NOT EXISTS seeker_experience (
    experience_id INT AUTO_INCREMENT PRIMARY KEY,
    seeker_id INT NOT NULL,
    company_name VARCHAR(100) NOT NULL,
    job_title VARCHAR(100) NOT NULL,
    job_description TEXT,
    start_date DATE NOT NULL,
    end_date DATE,
    is_current BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (seeker_id) REFERENCES job_seekers(seeker_id) ON DELETE CASCADE
);

-- Saved Jobs table
CREATE TABLE IF NOT EXISTS saved_jobs (
    saved_id INT AUTO_INCREMENT PRIMARY KEY,
    seeker_id INT NOT NULL,
    job_id INT NOT NULL,
    saved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seeker_id) REFERENCES job_seekers(seeker_id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES job_listings(job_id) ON DELETE CASCADE,
    UNIQUE(seeker_id, job_id)
);

-- Messages table for internal communication
CREATE TABLE IF NOT EXISTS messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message_content TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    related_job_id INT,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (related_job_id) REFERENCES job_listings(job_id) ON DELETE SET NULL
);

-- Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    notification_type VARCHAR(50) NOT NULL,
    notification_content TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    related_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Job Skills table to allow for better skill searching
CREATE TABLE IF NOT EXISTS job_skills (
    skill_id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    skill_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (job_id) REFERENCES job_listings(job_id) ON DELETE CASCADE,
    UNIQUE(job_id, skill_name)
);

-- Seeker Skills table to normalize skills data
CREATE TABLE IF NOT EXISTS seeker_skills (
    seeker_skill_id INT AUTO_INCREMENT PRIMARY KEY,
    seeker_id INT NOT NULL,
    skill_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (seeker_id) REFERENCES job_seekers(seeker_id) ON DELETE CASCADE,
    UNIQUE(seeker_id, skill_name)
);

-- Reviews table for employers and job seekers
CREATE TABLE IF NOT EXISTS reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    reviewer_id INT NOT NULL,
    reviewed_id INT NOT NULL,
    rating INT NOT NULL CHECK(rating BETWEEN 1 AND 5),
    review_content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reviewer_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE(reviewer_id, reviewed_id)
);

-- Create indexes for better performance
CREATE INDEX idx_user_email ON users(email);
CREATE INDEX idx_job_seekers_user_id ON job_seekers(user_id);
CREATE INDEX idx_employers_user_id ON employers(user_id);
CREATE INDEX idx_job_listings_employer_id ON job_listings(employer_id);
CREATE INDEX idx_job_applications_job_id ON job_applications(job_id);
CREATE INDEX idx_job_applications_seeker_id ON job_applications(seeker_id);
CREATE INDEX idx_seeker_skills_seeker_id ON seeker_skills(seeker_id);
CREATE INDEX idx_seeker_skills_skill_name ON seeker_skills(skill_name);
CREATE INDEX idx_job_skills_job_id ON job_skills(job_id);
CREATE INDEX idx_job_skills_skill_name ON job_skills(skill_name);