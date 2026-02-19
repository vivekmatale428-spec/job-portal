-- Job Portal Finder - Sample Data
-- Run AFTER job_portal.sql (schema must exist first)
-- Default password for all sample users: password

USE job_portal;

-- ========== USERS (Job Seekers) ==========
-- Password: password
INSERT INTO users (full_name, email, password, phone, location, qualification, experience, skills, bio) VALUES
('Rahul Sharma', 'rahul.sharma@email.com', '$2y$10$bWg83VNK1n09wyTnS8socO8njA4Bif9tNfQm26DHGTSEfYp5rySWu', '9876543210', 'Mumbai, Maharashtra', 'B.Tech Computer Science', '2 years', 'PHP, MySQL, JavaScript, Laravel', 'Full-stack developer with 2 years of experience building web applications.'),
('Priya Patel', 'priya.patel@email.com', '$2y$10$bWg83VNK1n09wyTnS8socO8njA4Bif9tNfQm26DHGTSEfYp5rySWu', '9876543211', 'Ahmedabad, Gujarat', 'MBA Marketing', '1 year', 'Digital Marketing, SEO, Content Writing', 'Marketing professional seeking growth opportunities.'),
('Amit Kumar', 'amit.kumar@email.com', '$2y$10$bWg83VNK1n09wyTnS8socO8njA4Bif9tNfQm26DHGTSEfYp5rySWu', '9876543212', 'Delhi NCR', 'B.Tech IT', 'Fresher', 'Python, Java, React, SQL', 'Fresher looking for software development roles.'),
('Sneha Reddy', 'sneha.reddy@email.com', '$2y$10$bWg83VNK1n09wyTnS8socO8njA4Bif9tNfQm26DHGTSEfYp5rySWu', '9876543213', 'Hyderabad, Telangana', 'B.Des', '3 years', 'UI/UX Design, Figma, Adobe XD', 'Creative UI/UX designer with strong portfolio.'),
('Vikram Singh', 'vikram.singh@email.com', '$2y$10$bWg83VNK1n09wyTnS8socO8njA4Bif9tNfQm26DHGTSEfYp5rySWu', '9876543214', 'Bangalore, Karnataka', 'BCA', '5+ years', 'PHP, MySQL, Node.js, AWS', 'Senior developer with 5+ years of experience.');

-- ========== EMPLOYERS ==========
-- Password: password
INSERT INTO employers (company_name, email, password, phone, website, location, industry, description) VALUES
('Tech Solutions India', 'hr@techsolutions.com', '$2y$10$bWg83VNK1n09wyTnS8socO8njA4Bif9tNfQm26DHGTSEfYp5rySWu', '022-12345678', 'https://techsolutions.com', 'Mumbai, Maharashtra', 'IT & Software', 'Leading software development company specializing in web and mobile solutions.'),
('Digital Marketing Pro', 'careers@digitalmarketingpro.com', '$2y$10$bWg83VNK1n09wyTnS8socO8njA4Bif9tNfQm26DHGTSEfYp5rySWu', '079-23456789', 'https://digitalmarketingpro.com', 'Ahmedabad, Gujarat', 'Marketing', 'Full-service digital marketing agency helping brands grow online.'),
('Creative Design Studio', 'jobs@creativedesign.com', '$2y$10$bWg83VNK1n09wyTnS8socO8njA4Bif9tNfQm26DHGTSEfYp5rySWu', '040-34567890', 'https://creativedesign.com', 'Hyderabad, Telangana', 'Design', 'Boutique design studio creating stunning UI/UX for startups and enterprises.'),
('FinanceHub Pvt Ltd', 'recruit@financehub.com', '$2y$10$bWg83VNK1n09wyTnS8socO8njA4Bif9tNfQm26DHGTSEfYp5rySWu', '080-45678901', 'https://financehub.com', 'Bangalore, Karnataka', 'Finance', 'Fintech company providing innovative financial solutions.'),
('StartupXYZ', 'hello@startupxyz.com', '$2y$10$bWg83VNK1n09wyTnS8socO8njA4Bif9tNfQm26DHGTSEfYp5rySWu', '011-56789012', 'https://startupxyz.com', 'Gurgaon, Haryana', 'IT & Software', 'Fast-growing startup building the next-gen SaaS platform.');

-- ========== JOBS ==========
INSERT INTO jobs (employer_id, category_id, title, slug, description, requirements, location, job_type, experience_level, salary_min, salary_max, salary_show, vacancies, status, featured) VALUES
(1, 1, 'PHP Developer', 'php-developer-1', 'We are looking for a skilled PHP developer to join our backend team. You will work on building scalable web applications.', 'B.Tech/B.E in CS/IT. Strong knowledge of PHP, MySQL, JavaScript. Experience with Laravel or CodeIgniter preferred.', 'Mumbai, Maharashtra', 'full_time', '1-2', 40000, 70000, 1, 2, 'approved', 1),
(1, 1, 'React Developer', 'react-developer-1', 'Join our frontend team to build modern React applications. Work with cutting-edge technologies.', 'Proficiency in React, Redux, JavaScript ES6+. Experience with REST APIs.', 'Mumbai, Maharashtra', 'full_time', 'fresher', 30000, 50000, 1, 1, 'approved', 1),
(1, 1, 'Intern - Web Development', 'intern-web-dev-1', '6-month internship for aspiring web developers. Hands-on experience with PHP, MySQL, JavaScript.', 'Pursuing or completed B.Tech/BCA. Basic knowledge of HTML, CSS, JavaScript.', 'Mumbai, Maharashtra', 'internship', 'fresher', 15000, 20000, 1, 3, 'approved', 0),
(2, 2, 'Digital Marketing Executive', 'digital-marketing-exec-1', 'Drive digital marketing campaigns across SEO, PPC, and social media.', 'MBA/BBA. 1-2 years experience in digital marketing. Knowledge of Google Analytics, Ads.', 'Ahmedabad, Gujarat', 'full_time', '1-2', 25000, 40000, 1, 1, 'approved', 0),
(2, 2, 'Content Writer', 'content-writer-1', 'Create engaging content for blogs, social media, and marketing collateral.', 'Excellent writing skills. Knowledge of SEO basics. Portfolio required.', 'Ahmedabad, Gujarat', 'part_time', 'fresher', 15000, 25000, 1, 2, 'approved', 0),
(3, 4, 'UI/UX Designer', 'ui-ux-designer-1', 'Design beautiful and intuitive user interfaces for web and mobile apps.', 'Proficiency in Figma, Adobe XD. 2+ years of UI/UX experience. Strong portfolio.', 'Hyderabad, Telangana', 'full_time', '3-5', 45000, 80000, 1, 1, 'approved', 1),
(3, 4, 'Graphic Designer', 'graphic-designer-1', 'Create visual content for brands - logos, social media, advertisements.', 'Proficiency in Photoshop, Illustrator. Creative mindset. 1+ year experience.', 'Hyderabad, Telangana', 'full_time', '1-2', 20000, 35000, 1, 1, 'pending', 0),
(4, 5, 'Finance Analyst', 'finance-analyst-1', 'Analyze financial data, prepare reports, and support investment decisions.', 'CA/MBA Finance. 2+ years in finance. Strong Excel and analytical skills.', 'Bangalore, Karnataka', 'full_time', '3-5', 50000, 90000, 1, 1, 'approved', 0),
(4, 6, 'HR Executive', 'hr-executive-1', 'Handle recruitment, onboarding, and employee relations.', 'MBA HR or equivalent. 1-2 years HR experience. Good communication skills.', 'Bangalore, Karnataka', 'full_time', '1-2', 30000, 45000, 1, 1, 'approved', 0),
(5, 1, 'Full Stack Developer', 'full-stack-developer-1', 'Build end-to-end features for our SaaS product. Node.js + React stack.', 'Strong JavaScript. Experience with Node.js, React, MongoDB or PostgreSQL.', 'Gurgaon, Haryana', 'full_time', '3-5', 60000, 120000, 1, 1, 'approved', 1),
(5, 1, 'Remote - Python Developer', 'remote-python-developer-1', 'Work from anywhere! Build backend services and APIs in Python.', 'Strong Python. Experience with Django/Flask. Familiar with AWS or GCP.', 'Remote', 'remote', '1-2', 40000, 80000, 1, 2, 'approved', 1);

-- ========== APPLICATIONS ==========
INSERT INTO applications (job_id, user_id, cover_letter, status) VALUES
(1, 1, 'I am a PHP developer with 2 years of experience. I have worked on Laravel projects and would love to contribute to your team.', 'shortlisted'),
(1, 3, 'I am a fresher with strong fundamentals in PHP and MySQL. Eager to learn and grow.', 'pending'),
(2, 3, 'I have completed several React projects during my college. Ready to join as a fresher.', 'pending'),
(3, 3, 'Looking for an internship to gain practical experience. I know HTML, CSS, and basic JavaScript.', 'pending'),
(4, 2, 'I have 1 year of experience in digital marketing. I have managed SEO and social media campaigns.', 'shortlisted'),
(6, 4, 'UI/UX designer with 3 years of experience. I have designed 20+ mobile and web apps. Portfolio attached.', 'hired'),
(8, 1, 'Interested in the finance analyst role. I have analytical skills from my tech background.', 'rejected'),
(10, 1, 'Full-stack developer with PHP and Node.js experience. I can contribute immediately.', 'pending'),
(11, 5, 'Senior Python developer. 5+ years of experience. Comfortable with remote work.', 'shortlisted');

-- ========== SAVED JOBS ==========
INSERT INTO saved_jobs (user_id, job_id) VALUES
(1, 2),
(1, 10),
(2, 4),
(2, 5),
(3, 1),
(3, 2),
(3, 3),
(4, 6),
(5, 10),
(5, 11);

-- ========== JOB ALERTS ==========
INSERT INTO job_alerts (user_id, keywords, location, category_id, job_type, is_active) VALUES
(1, 'PHP, Laravel', 'Mumbai', 1, 'full_time', 1),
(2, 'marketing, SEO', NULL, 2, NULL, 1),
(3, 'React, JavaScript', NULL, 1, 'internship', 1),
(4, 'UI, UX, design', 'Hyderabad', 4, 'full_time', 1),
(5, 'Python, remote', NULL, 1, 'remote', 1);
