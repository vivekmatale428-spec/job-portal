<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check_employer.php';

$eid = (int)$_SESSION['employer_id'];
$job_id = (int)($_GET['id'] ?? 0);

if (!$job_id) {
    header('Location: ' . BASE_URL . 'employer/manage_jobs.php');
    exit;
}

$job = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM jobs WHERE id = $job_id AND employer_id = $eid"));
if (!$job) {
    header('Location: ' . BASE_URL . 'employer/manage_jobs.php');
    exit;
}

// Update application status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['app_id'], $_POST['status'])) {
    $app_id = (int)$_POST['app_id'];
    $status = in_array($_POST['status'], ['pending','shortlisted','rejected','hired']) ? $_POST['status'] : 'pending';
    mysqli_query($conn, "UPDATE applications SET status = '$status' WHERE id = $app_id AND job_id = $job_id");
}

$applicants = mysqli_query($conn, "SELECT a.*, u.full_name, u.email, u.phone, u.location, u.qualification, u.experience, u.skills, u.resume_file AS user_resume FROM applications a JOIN users u ON a.user_id = u.id WHERE a.job_id = $job_id ORDER BY a.applied_at DESC");

// Export to CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="applicants_' . $job['title'] . '_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Name', 'Email', 'Phone', 'Location', 'Qualification', 'Experience', 'Skills', 'Status', 'Applied Date']);
    if ($applicants) {
        mysqli_data_seek($applicants, 0);
        while ($row = mysqli_fetch_assoc($applicants)) {
            $resume = $row['resume_file'] ?? $row['user_resume'] ?? '';
            fputcsv($out, [$row['full_name'], $row['email'], $row['phone'] ?? '', $row['location'] ?? '', $row['qualification'] ?? '', $row['experience'] ?? '', substr($row['skills'] ?? '', 0, 200), $row['status'], date('Y-m-d H:i', strtotime($row['applied_at']))]);
        }
    }
    fclose($out);
    exit;
}

$page_title = 'Applicants - ' . $job['title'];
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container py-5">
    <h1 class="section-title">Applicants</h1>
    <p class="section-subtitle"><?php echo htmlspecialchars($job['title']); ?></p>

    <div class="mb-3 d-flex gap-2">
        <a href="manage_jobs.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back to Jobs</a>
        <?php if ($applicants && mysqli_num_rows($applicants) > 0): ?>
            <a href="view_applicants.php?id=<?php echo $job_id; ?>&export=csv" class="btn btn-outline-primary"><i class="bi bi-download me-1"></i>Export CSV</a>
        <?php endif; ?>
    </div>

    <?php if ($applicants && mysqli_num_rows($applicants) > 0): ?>
        <div class="row g-4">
            <?php while ($app = mysqli_fetch_assoc($applicants)): ?>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="mb-1"><?php echo htmlspecialchars($app['full_name']); ?></h5>
                                    <p class="text-muted small mb-1"><i class="bi bi-envelope me-1"></i><?php echo htmlspecialchars($app['email']); ?></p>
                                    <?php if ($app['phone']): ?>
                                        <p class="text-muted small mb-1"><i class="bi bi-telephone me-1"></i><?php echo htmlspecialchars($app['phone']); ?></p>
                                    <?php endif; ?>
                                    <?php if ($app['location']): ?>
                                        <p class="text-muted small mb-1"><i class="bi bi-geo me-1"></i><?php echo htmlspecialchars($app['location']); ?></p>
                                    <?php endif; ?>
                                    <?php if ($app['qualification']): ?>
                                        <p class="small mb-1"><strong>Qualification:</strong> <?php echo htmlspecialchars($app['qualification']); ?></p>
                                    <?php endif; ?>
                                    <?php if ($app['experience']): ?>
                                        <p class="small mb-1"><strong>Experience:</strong> <?php echo htmlspecialchars($app['experience']); ?></p>
                                    <?php endif; ?>
                                    <?php if ($app['skills']): ?>
                                        <p class="small mb-1"><strong>Skills:</strong> <?php echo htmlspecialchars(substr($app['skills'], 0, 150)); ?><?php echo strlen($app['skills']) > 150 ? '...' : ''; ?></p>
                                    <?php endif; ?>
                                    <?php if ($app['cover_letter']): ?>
                                        <p class="small mb-0"><strong>Cover letter:</strong><br><?php echo nl2br(htmlspecialchars(substr($app['cover_letter'], 0, 300))); ?><?php echo strlen($app['cover_letter']) > 300 ? '...' : ''; ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <?php 
                                    $resume = $app['resume_file'] ?? $app['user_resume'] ?? null;
                                    if ($resume): ?>
                                        <a href="<?php echo BASE_URL; ?>uploads/resumes/<?php echo htmlspecialchars($resume); ?>" target="_blank" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-download me-1"></i>Resume</a>
                                    <?php endif; ?>
                                    <form method="post" action="" class="d-inline">
                                        <input type="hidden" name="app_id" value="<?php echo (int)$app['id']; ?>">
                                        <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                            <option value="pending" <?php echo $app['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="shortlisted" <?php echo $app['status'] === 'shortlisted' ? 'selected' : ''; ?>>Shortlisted</option>
                                            <option value="rejected" <?php echo $app['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                            <option value="hired" <?php echo $app['status'] === 'hired' ? 'selected' : ''; ?>>Hired</option>
                                        </select>
                                    </form>
                                    <span class="badge-status badge-<?php echo $app['status']; ?> ms-1"><?php echo htmlspecialchars($app['status']); ?></span>
                                    <p class="small text-muted mb-0 mt-1">Applied <?php echo date('M d, Y', strtotime($app['applied_at'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="card p-5 text-center">
            <i class="bi bi-people display-4 text-muted"></i>
            <p class="mb-0 text-muted mt-2">No applications yet for this job.</p>
        </div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
