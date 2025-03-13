-- Add reset token columns to employers table
ALTER TABLE employers
ADD COLUMN reset_token VARCHAR(64) DEFAULT NULL,
ADD COLUMN reset_token_expires DATETIME DEFAULT NULL;

-- Add reset token columns to job_seekers table
ALTER TABLE job_seekers
ADD COLUMN reset_token VARCHAR(64) DEFAULT NULL,
ADD COLUMN reset_token_expires DATETIME DEFAULT NULL; 