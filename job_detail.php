<?php
require_once __DIR__ . '/config/db.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: ' . BASE_URL . 'jobs.php');
    exit;
}

mysqli_query($conn, "UPDATE jobs SET views = views + 1 WHERE id = $id");

$job = mysqli_fetch_assoc(mysqli_query($conn, "SELECT j.*, e.company_name, e.location AS company_location, e.website, e.description AS company_desc, c.name AS category_name 
    FROM jobs j 
    LEFT JOIN employers e ON j.employer_id = e.id 
    LEFT JOIN categories c ON j.category_id = c.id 
    WHERE j.id = $id AND j.status = 'approved'"));

if (!$job) {
    header('Location: ' . BASE_URL . 'jobs.php');
    exit;
}

$applied = false;
$saved = false;
if (isset($_SESSION['user_id']) && $_SESSION['role'] === ROLE_JOBSEEKER) {
    $uid = (int)$_SESSION['user_id'];
    $applied = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM applications WHERE job_id = $id AND user_id = $uid"));
    $saved = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM saved_jobs WHERE job_id = $id AND user_id = $uid"));
} else {
    $uid = 0;
}

$page_title = $job['title'];
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="job-type"><?php echo htmlspecialchars(str_replace('_', ' ', $job['job_type'])); ?></span>
                        <?php if (!empty($job['category_name'])): ?>
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($job['category_name']); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($job['experience_level'])): ?>
                            <span class="badge bg-light text-dark"><?php echo htmlspecialchars($job['experience_level']); ?></span>
                        <?php endif; ?>
                    </div>
                    <h1 class="h3 mb-2"><?php echo htmlspecialchars($job['title']); ?></h1>
                    <p class="text-muted mb-3"><a href="<?php echo BASE_URL; ?>employer_profile.php?id=<?php echo (int)$job['employer_id']; ?>" class="text-decoration-none"><?php echo htmlspecialchars($job['company_name']); ?></a> • <?php echo htmlspecialchars($job['location'] ?? '—'); ?></p>
                    <div class="d-flex flex-wrap gap-2 small text-muted mb-3">
                        <span><i class="bi bi-eye me-1"></i><?php echo (int)$job['views']; ?> views</span>
                        <span><i class="bi bi-people me-1"></i><?php echo (int)$job['applications_count']; ?> applications</span>
                        <?php if ($job['deadline']): ?>
                            <span><i class="bi bi-calendar me-1"></i>Apply before <?php echo date('M d, Y', strtotime($job['deadline'])); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === ROLE_JOBSEEKER): ?>
                        <div class="d-flex gap-2">
                            <?php if ($applied): ?>
                                <span class="btn btn-success disabled"><i class="bi bi-check-circle me-1"></i>Applied</span>
                            <?php else: ?>
                                <a href="<?php echo BASE_URL; ?>jobseeker/apply_job.php?id=<?php echo $id; ?>" class="btn btn-primary">Apply Now</a>
                            <?php endif; ?>
                            <?php if ($saved): ?>
                                <a href="<?php echo BASE_URL; ?>jobseeker/saved_jobs.php?unsave=<?php echo $id; ?>" class="btn btn-outline-secondary"><i class="bi bi-bookmark-fill me-1"></i>Saved</a>
                            <?php else: ?>
                                <a href="<?php echo BASE_URL; ?>jobseeker/save_job.php?id=<?php echo $id; ?>" class="btn btn-outline-secondary"><i class="bi bi-bookmark me-1"></i>Save Job</a>
                            <?php endif; ?>
                        </div>
                    <?php elseif (!isset($_SESSION['user_id'])): ?>
                        <a href="<?php echo BASE_URL; ?>auth/login.php?redirect=<?php echo urlencode(BASE_URL . 'job_detail.php?id=' . $id); ?>" class="btn btn-primary">Login to Apply</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-3">Job Description</h5>
                    <div class="job-desc"><?php echo nl2br(htmlspecialchars($job['description'])); ?></div>
                    <?php if (!empty($job['requirements'])): ?>
                        <h5 class="mt-4 mb-3">Requirements</h5>
                        <div class="job-desc"><?php echo nl2br(htmlspecialchars($job['requirements'])); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-3">Job Overview</h5>
                    <ul class="list-unstyled mb-0">
                        <?php if ($job['salary_show'] && ($job['salary_min'] || $job['salary_max'])): ?>
                            <li class="mb-2"><strong>Salary:</strong> ₹<?php echo number_format($job['salary_min'] ?? 0); ?> - ₹<?php echo number_format($job['salary_max'] ?? 0); ?> / month</li>
                        <?php endif; ?>
                        <li class="mb-2"><strong>Job Type:</strong> <?php echo htmlspecialchars(str_replace('_', ' ', $job['job_type'])); ?></li>
                        <li class="mb-2"><strong>Location:</strong> <?php echo htmlspecialchars($job['location'] ?? '—'); ?></li>
                        <li class="mb-2"><strong>Experience:</strong> <?php echo htmlspecialchars($job['experience_level'] ?? '—'); ?></li>
                        <li class="mb-2"><strong>Vacancies:</strong> <?php echo (int)$job['vacancies']; ?></li>
                        <?php if ($job['deadline']): ?>
                            <li class="mb-0"><strong>Deadline:</strong> <?php echo date('M d, Y', strtotime($job['deadline'])); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="card">
                <div class="card-body p-4">
                    <h5 class="mb-3">About Company</h5>
                    <p class="small text-muted mb-2"><?php echo htmlspecialchars($job['company_name']); ?></p>
                    <?php if (!empty($job['company_desc'])): ?>
                        <p class="small"><?php echo nl2br(htmlspecialchars(substr($job['company_desc'], 0, 300))); ?><?php echo strlen($job['company_desc']) > 300 ? '...' : ''; ?></p>
                    <?php endif; ?>
                    <?php if (!empty($job['website'])): ?>
                        <a href="<?php echo htmlspecialchars($job['website']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">Visit Website</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
