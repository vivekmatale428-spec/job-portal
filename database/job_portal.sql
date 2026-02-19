    -- Job Portal Finder - Database Schema
    -- Run this in phpMyAdmin or MySQL

    CREATE DATABASE IF NOT EXISTS job_portal;
    USE job_portal;

    -- Categories for jobs
    CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        slug VARCHAR(100) NOT NULL UNIQUE,
        icon VARCHAR(50) DEFAULT 'briefcase',
        job_count INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- Users (Job Seekers)
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(150) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        location VARCHAR(150),
        qualification VARCHAR(100),
        experience VARCHAR(50),
        skills TEXT,
        resume_file VARCHAR(255),
        profile_photo VARCHAR(255),
        bio TEXT,
        is_verified TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    -- Employers
    CREATE TABLE IF NOT EXISTS employers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        company_name VARCHAR(200) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        website VARCHAR(255),
        location VARCHAR(200),
        industry VARCHAR(100),
        company_logo VARCHAR(255),
        description TEXT,
        is_verified TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    -- Jobs
    CREATE TABLE IF NOT EXISTS jobs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        employer_id INT NOT NULL,
        category_id INT,
        title VARCHAR(200) NOT NULL,
        slug VARCHAR(250),
        description TEXT NOT NULL,
        requirements TEXT,
        location VARCHAR(150),
        job_type ENUM('full_time', 'part_time', 'contract', 'internship', 'remote') DEFAULT 'full_time',
        experience_level ENUM('fresher', '1-2', '3-5', '5+') DEFAULT 'fresher',
        salary_min INT,
        salary_max INT,
        salary_show TINYINT(1) DEFAULT 1,
        vacancies INT DEFAULT 1,
        status ENUM('pending', 'approved', 'rejected', 'closed') DEFAULT 'pending',
        featured TINYINT(1) DEFAULT 0,
        views INT DEFAULT 0,
        applications_count INT DEFAULT 0,
        deadline DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (employer_id) REFERENCES employers(id) ON DELETE CASCADE,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
        INDEX idx_status (status),
        INDEX idx_category (category_id),
        INDEX idx_location (location),
        INDEX idx_job_type (job_type),
        FULLTEXT idx_search (title, description, requirements)
    );

-- Applications
CREATE TABLE IF NOT EXISTS applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    user_id INT NOT NULL,
    cover_letter TEXT,
    resume_file VARCHAR(255),
    status ENUM('pending', 'shortlisted', 'rejected', 'hired') DEFAULT 'pending',
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_application (job_id, user_id),
        FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_status (status)
    );

    -- Saved Jobs (extra feature)
    CREATE TABLE IF NOT EXISTS saved_jobs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        job_id INT NOT NULL,
        saved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_saved (user_id, job_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE
    );

    -- Job Alerts (extra feature)
    CREATE TABLE IF NOT EXISTS job_alerts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        keywords VARCHAR(255),
        location VARCHAR(150),
        category_id INT,
        job_type VARCHAR(50),
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    );

    -- Admin
    CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- Insert default admin (password: admin123)
    INSERT INTO admin (username, password, full_name) VALUES
('admin', '$2y$10$2Ic70CnuY0o9TuTgNh4DUus/3K/xTQD885jHXvEYNWAGdDRoIG2OC', 'System Admin')
    ON DUPLICATE KEY UPDATE username=username;

    -- Insert sample categories
    INSERT INTO categories (name, slug, icon) VALUES
    ('IT & Software', 'it-software', 'code'),
    ('Marketing', 'marketing', 'megaphone'),
    ('Sales', 'sales', 'trending-up'),
    ('Design', 'design', 'palette'),
    ('Finance', 'finance', 'dollar-sign'),
    ('HR', 'hr', 'users'),
    ('Engineering', 'engineering', 'settings'),
    ('Customer Service', 'customer-service', 'headphones')
    ON DUPLICATE KEY UPDATE name=VALUES(name);
