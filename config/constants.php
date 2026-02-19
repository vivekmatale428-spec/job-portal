<?php
/**
 * Application constants - Job Portal Finder
 */
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/job-portal/');
}
define('SITE_NAME', 'Job Portal Finder');
define('UPLOAD_PATH', dirname(__DIR__) . '/uploads/');
define('RESUME_PATH', UPLOAD_PATH . 'resumes/');
define('IMAGE_PATH', UPLOAD_PATH . 'images/');
define('LOGO_PATH', IMAGE_PATH . 'logos/');
define('PROFILE_PATH', IMAGE_PATH . 'profiles/');
define('MAX_RESUME_SIZE', 2 * 1024 * 1024); // 2MB
define('MAX_IMAGE_SIZE', 1 * 1024 * 1024); // 1MB
define('ALLOWED_RESUME_TYPES', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ITEMS_PER_PAGE', 10);

// User roles
define('ROLE_JOBSEEKER', 'jobseeker');
define('ROLE_EMPLOYER', 'employer');
define('ROLE_ADMIN', 'admin');
