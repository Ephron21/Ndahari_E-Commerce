-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    user_type ENUM('job_seeker', 'employer') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME DEFAULT NULL,
    remember_token VARCHAR(64) DEFAULT NULL
);

-- Create job_seekers table
CREATE TABLE IF NOT EXISTS job_seekers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    skills TEXT,
    experience_level ENUM('entry', 'intermediate', 'experienced', 'senior'),
    education ENUM('high_school', 'diploma', 'bachelors', 'masters', 'phd'),
    preferred_job_type ENUM('full_time', 'part_time', 'contract', 'internship'),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create employers table
CREATE TABLE IF NOT EXISTS employers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    company_name VARCHAR(255) NOT NULL,
    company_size VARCHAR(20),
    industry VARCHAR(100),
    website VARCHAR(255),
    company_description TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
); 