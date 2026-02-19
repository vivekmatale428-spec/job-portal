<?php
require_once __DIR__ . '/config/db.php';

// Featured & recent approved jobs for homepage
$featured = mysqli_query($conn, "SELECT j.*, e.company_name, c.name AS category_name FROM jobs j 
    LEFT JOIN employers e ON j.employer_id = e.id 
    LEFT JOIN categories c ON j.category_id = c.id 
    WHERE j.status = 'approved' AND j.featured = 1 ORDER BY j.created_at DESC LIMIT 6");
$recent = mysqli_query($conn, "SELECT j.*, e.company_name, c.name AS category_name FROM jobs j 
    LEFT JOIN employers e ON j.employer_id = e.id 
    LEFT JOIN categories c ON j.category_id = c.id 
    WHERE j.status = 'approved' ORDER BY j.created_at DESC LIMIT 8");
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC LIMIT 8");

$page_title = 'Home';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<section class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1>Find Your Dream <span class="text-warning">Job</span> Today</h1>
                <p class="lead">Connect with top employers. Search by skills, location, and experience. Your next opportunity is one click away.</p>
                <form class="hero-search d-flex flex-wrap gap-2" action="jobs.php" method="get">
                    <input type="text" name="q" class="form-control flex-grow-1" style="min-width:180px" placeholder="Job title or keyword">
                    <input type="text" name="location" class="form-control" style="min-width:150px" placeholder="Location">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i> Search</button>
                </form>
            </div>
            <div class="col-lg-5 d-none d-lg-block text-center">
                <i class="bi bi-briefcase-fill display-1 opacity-25"></i>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <h2 class="section-title">Browse by Category</h2>
        <p class="section-subtitle">Explore jobs by industry</p>
        <div class="row g-3">
            <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="jobs.php?category=<?php echo urlencode($cat['slug']); ?>" class="category-pill text-decoration-none w-100">
                        <i class="bi bi-<?php echo htmlspecialchars($cat['icon']); ?>"></i>
                        <span><?php echo htmlspecialchars($cat['name']); ?></span>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<?php if ($featured && mysqli_num_rows($featured) > 0): ?>
<section class="py-5">
    <div class="container">
        <h2 class="section-title">Featured Jobs</h2>
        <p class="section-subtitle">Hand-picked opportunities for you</p>
        <div class="row g-4">
            <?php while ($job = mysqli_fetch_assoc($featured)): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card card-job card-featured h-100">
                        <div class="card-body">
                            <span class="job-type"><?php echo htmlspecialchars(str_replace('_', ' ', $job['job_type'])); ?></span>
                            <h5 class="card-title mt-2 mb-1"><a href="job_detail.php?id=<?php echo (int)$job['id']; ?>" class="text-dark text-decoration-none"><?php echo htmlspecialchars($job['title']); ?></a></h5>
                            <p class="company-name mb-2"><?php echo htmlspecialchars($job['company_name']); ?></p>
                            <p class="text-muted small mb-2"><?php echo htmlspecialchars($job['location'] ?? '—'); ?> <?php if ($job['category_name']) echo ' • ' . htmlspecialchars($job['category_name']); ?></p>
                            <?php if ($job['salary_show'] && ($job['salary_min'] || $job['salary_max'])): ?>
                                <p class="salary small mb-0">₹<?php echo number_format($job['salary_min'] ?? 0); ?> - ₹<?php echo number_format($job['salary_max'] ?? 0); ?> / mo</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="py-5 bg-white">
    <div class="container">
        <h2 class="section-title">Recent Jobs</h2>
        <p class="section-subtitle">Latest openings from employers</p>
        <div class="row g-4">
            <?php 
            if ($recent) while ($job = mysqli_fetch_assoc($recent)): 
                $is_featured = !empty($job['featured']);
            ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card card-job h-100 <?php echo $is_featured ? 'card-featured' : ''; ?>">
                        <div class="card-body">
                            <span class="job-type"><?php echo htmlspecialchars(str_replace('_', ' ', $job['job_type'])); ?></span>
                            <h5 class="card-title mt-2 mb-1"><a href="job_detail.php?id=<?php echo (int)$job['id']; ?>" class="text-dark text-decoration-none"><?php echo htmlspecialchars($job['title']); ?></a></h5>
                            <p class="company-name mb-2"><?php echo htmlspecialchars($job['company_name']); ?></p>
                            <p class="text-muted small mb-2"><?php echo htmlspecialchars($job['location'] ?? '—'); ?></p>
                            <?php if ($job['salary_show'] && ($job['salary_min'] || $job['salary_max'])): ?>
                                <p class="salary small mb-0">₹<?php echo number_format($job['salary_min'] ?? 0); ?> - ₹<?php echo number_format($job['salary_max'] ?? 0); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <div class="text-center mt-4">
            <a href="jobs.php" class="btn btn-primary">View All Jobs</a>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="section-title">For Employers</h2>
                <p class="text-muted">Post jobs, reach qualified candidates, and hire faster. Get started in minutes.</p>
                <a href="auth/register.php?role=employer" class="btn btn-accent me-2">Post a Job</a>
                <a href="auth/login.php" class="btn btn-outline-primary">Employer Login</a>
            </div>
            <div class="col-lg-6">
                <h2 class="section-title">For Job Seekers</h2>
                <p class="text-muted">Create your profile, upload resume, and apply to thousands of jobs with one click.</p>
                <a href="auth/register.php?role=jobseeker" class="btn btn-primary me-2">Create Account</a>
                <a href="jobs.php" class="btn btn-outline-primary">Browse Jobs</a>
            </div>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
