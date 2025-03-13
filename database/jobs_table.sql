CREATE TABLE IF NOT EXISTS jobs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employer_id INT NOT NULL,
    job_title VARCHAR(255) NOT NULL,
    job_description TEXT NOT NULL,
    job_type VARCHAR(50) NOT NULL,
    location VARCHAR(255) NOT NULL,
    salary_range VARCHAR(100) NOT NULL,
    requirements TEXT NOT NULL,
    application_deadline DATE,
    created_at DATETIME NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    FOREIGN KEY (employer_id) REFERENCES employers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 