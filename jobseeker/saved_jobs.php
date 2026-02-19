<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$uid = (int)$_SESSION['user_id'];

if (isset($_GET['unsave'])) {
    $job_id = (int)$_GET['unsave'];
    mysqli_query($conn, "DELETE FROM saved_jobs WHERE user_id = $uid AND job_id = $job_id");
    header('Location: ' . BASE_URL . 'jobseeker/saved_jobs.php');
    exit;
}

$saved = mysqli_query($conn, "SELECT s.*, j.title, j.location, j.job_type, j.salary_min, j.salary_max, j.salary_show, e.company_name 
    FROM saved_jobs s 
    JOIN jobs j ON s.job_id = j.id 
    JOIN employers e ON j.employer_id = e.id 
    WHERE s.user_id = $uid AND j.status = 'approved' ORDER BY s.saved_at DESC");

$page_title = 'Saved Jobs';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container py-5">
    <h1 class="section-title">Saved Jobs</h1>
    <p class="section-subtitle">Jobs you saved for later</p>

    <?php if ($saved && mysqli_num_rows($saved) > 0): ?>
        <div class="row g-4">
            <?php while ($job = mysqli_fetch_assoc($saved)): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card card-job h-100">
                        <div class="card-body">
                            <span class="job-type"><?php echo htmlspecialchars(str_replace('_', ' ', $job['job_type'])); ?></span>
                            <h5 class="card-title mt-2 mb-1"><a href="<?php echo BASE_URL; ?>job_detail.php?id=<?php echo (int)$job['job_id']; ?>" class="text-dark text-decoration-none"><?php echo htmlspecialchars($job['title']); ?></a></h5>
                            <p class="company-name mb-2"><?php echo htmlspecialchars($job['company_name']); ?></p>
                            <p class="text-muted small mb-2"><?php echo htmlspecialchars($job['location'] ?? '—'); ?></p>
                            <?php if ($job['salary_show'] && ($job['salary_min'] || $job['salary_max'])): ?>
                                <p class="salary small mb-2">₹<?php echo number_format($job['salary_min'] ?? 0); ?> - ₹<?php echo number_format($job['salary_max'] ?? 0); ?></p>
                            <?php endif; ?>
                            <div class="d-flex gap-2">
                                <a href="<?php echo BASE_URL; ?>job_detail.php?id=<?php echo (int)$job['job_id']; ?>" class="btn btn-sm btn-primary">View & Apply</a>
                                <a href="saved_jobs.php?unsave=<?php echo (int)$job['job_id']; ?>" class="btn btn-sm btn-outline-danger" title="Remove from saved"><i class="bi bi-bookmark-dash"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="card p-5 text-center">
            <i class="bi bi-bookmark display-4 text-muted"></i>
            <p class="mb-0 text-muted mt-2">No saved jobs. Save jobs from search or job detail page.</p>
            <a href="search_jobs.php" class="btn btn-primary mt-3">Search Jobs</a>
        </div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
