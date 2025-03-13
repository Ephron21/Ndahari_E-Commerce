-- Add last_login column to employers table
ALTER TABLE employers
ADD COLUMN IF NOT EXISTS last_login DATETIME DEFAULT NULL;

-- Add last_login column to job_seekers table
ALTER TABLE job_seekers
ADD COLUMN IF NOT EXISTS last_login DATETIME DEFAULT NULL; 