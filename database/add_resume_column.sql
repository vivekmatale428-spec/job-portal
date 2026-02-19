-- Add resume upload to application form
-- Run this if you have an existing database (before the resume_file column was added)

USE job_portal;
ALTER TABLE applications ADD COLUMN resume_file VARCHAR(255) NULL AFTER cover_letter;
