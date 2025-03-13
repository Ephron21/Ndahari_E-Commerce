-- Alter the jobs table to update job_type ENUM values
ALTER TABLE jobs 
MODIFY COLUMN job_type ENUM('full_time', 'part_time', 'temporary', 'contract', 'weekend', 'evening', 'seasonal', 'internship') NOT NULL; 