<?php
require_once __DIR__ . '/config/db.php';
$page_title = 'About Us';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<section class="py-5 bg-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="section-title">About Job Portal Finder</h1>
                <p class="lead text-muted">A web-based platform connecting job seekers and employers for efficient recruitment and job searching.</p>
                <p>Job Portal Finder is developed as a final year academic project to simplify the job search process and make recruitment faster. The system allows users to search for jobs by skills, location, qualification, and experience. Employers can post vacancies and manage applications; job seekers can create profiles, upload resumes, and apply online.</p>
            </div>
            <div class="col-lg-6 text-center">
                <i class="bi bi-briefcase-fill display-1 text-primary opacity-50"></i>
            </div>
        </div>
    </div>
</section>
<section class="py-5">
    <div class="container">
        <h2 class="section-title text-center">Why Choose Us</h2>
        <p class="section-subtitle text-center">Features that make a difference</p>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <i class="bi bi-search display-4 text-primary mb-2"></i>
                    <div class="stat-value">Smart Search</div>
                    <div class="stat-label">Filter by skills, location, job type & salary</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <i class="bi bi-shield-check display-4 text-primary mb-2"></i>
                    <div class="stat-value">Secure</div>
                    <div class="stat-label">Secure login & data protection</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <i class="bi bi-lightning display-4 text-primary mb-2"></i>
                    <div class="stat-value">Fast</div>
                    <div class="stat-label">Quick apply & instant updates</div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
