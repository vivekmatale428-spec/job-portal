<?php
require_once __DIR__ . '/config/db.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: ' . BASE_URL . 'jobs.php');
    exit;
}

$emp = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM employers WHERE id = $id"));
if (!$emp) {
    header('Location: ' . BASE_URL . 'jobs.php');
    exit;
}

$jobs = mysqli_query($conn, "SELECT j.*, c.name AS category_name FROM jobs j LEFT JOIN categories c ON j.category_id = c.id WHERE j.employer_id = $id AND j.status = 'approved' ORDER BY j.created_at DESC");

$page_title = $emp['company_name'];
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<div class="container py-5">
    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body p-4 text-center">
                    <h4 class="mb-2"><?php echo htmlspecialchars($emp['company_name']); ?></h4>
                    <?php if ($emp['industry']): ?>
                        <p class="text-muted small mb-2"><?php echo htmlspecialchars($emp['industry']); ?></p>
                    <?php endif; ?>
                    <?php if ($emp['location']): ?>
                        <p class="small mb-1"><i class="bi bi-geo me-1"></i><?php echo htmlspecialchars($emp['location']); ?></p>
                    <?php endif; ?>
                    <?php if ($emp['website']): ?>
                        <a href="<?php echo htmlspecialchars($emp['website']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">Visit Website</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (!empty($emp['description'])): ?>
            <div class="card mb-4">
                <div class="card-body p-4">
                    <h6 class="mb-2">About Company</h6>
                    <p class="small text-muted mb-0"><?php echo nl2br(htmlspecialchars($emp['description'])); ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="col-lg-8">
            <h5 class="mb-3">Open Positions (<?php echo mysqli_num_rows($jobs); ?>)</h5>
            <?php if ($jobs && mysqli_num_rows($jobs) > 0): ?>
                <div class="row g-3">
                    <?php while ($job = mysqli_fetch_assoc($jobs)): ?>
                        <div class="col-12">
                            <div class="card card-job">
                                <div class="card-body">
                                    <span class="job-type"><?php echo htmlspecialchars(str_replace('_', ' ', $job['job_type'])); ?></span>
                                    <h5 class="card-title mt-2 mb-1"><a href="<?php echo BASE_URL; ?>job_detail.php?id=<?php echo (int)$job['id']; ?>" class="text-dark text-decoration-none"><?php echo htmlspecialchars($job['title']); ?></a></h5>
                                    <p class="text-muted small mb-2"><?php echo htmlspecialchars($job['location'] ?? '—'); ?> <?php if ($job['category_name']) echo ' • ' . htmlspecialchars($job['category_name']); ?></p>
                                    <?php if ($job['salary_show'] && ($job['salary_min'] || $job['salary_max'])): ?>
                                        <p class="salary small mb-0">₹<?php echo number_format($job['salary_min'] ?? 0); ?> - ₹<?php echo number_format($job['salary_max'] ?? 0); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-muted">No open positions at the moment.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
