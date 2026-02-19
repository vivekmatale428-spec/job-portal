<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check_admin.php';

$users_count = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM users"));
$employers_count = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM employers"));
$jobs_count = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM jobs"));
$pending_jobs = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM jobs WHERE status = 'pending'"));
$applications_count = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM applications"));

$recent_jobs = mysqli_query($conn, "SELECT j.*, e.company_name FROM jobs j JOIN employers e ON j.employer_id = e.id ORDER BY j.created_at DESC LIMIT 10");

$page_title = 'Admin Dashboard';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container py-5">
    <h1 class="section-title">Admin Dashboard</h1>
    <p class="section-subtitle">Overview of the job portal</p>

    <div class="mb-3">
        <a href="manage_jobs.php" class="btn btn-outline-primary me-2">Manage Jobs</a>
        <a href="manage_users.php" class="btn btn-outline-primary me-2">Manage Users</a>
        <a href="manage_employers.php" class="btn btn-outline-primary me-2">Manage Employers</a>
        <a href="manage_categories.php" class="btn btn-outline-primary me-2">Manage Categories</a>
        <a href="analytics.php" class="btn btn-outline-info">Analytics</a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4 col-lg-2">
            <div class="stat-card">
                <div class="stat-value"><?php echo $users_count; ?></div>
                <div class="stat-label">Job Seekers</div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="stat-card">
                <div class="stat-value"><?php echo $employers_count; ?></div>
                <div class="stat-label">Employers</div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="stat-card">
                <div class="stat-value"><?php echo $jobs_count; ?></div>
                <div class="stat-label">Total Jobs</div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="stat-card">
                <div class="stat-value"><?php echo $pending_jobs; ?></div>
                <div class="stat-label">Pending Jobs</div>
                <a href="manage_jobs.php?status=pending" class="btn btn-sm btn-outline-primary mt-2">Review</a>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="stat-card">
                <div class="stat-value"><?php echo $applications_count; ?></div>
                <div class="stat-label">Applications</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="dashboard-card">
                <h5><i class="bi bi-briefcase me-2"></i>Recent Job Postings</h5>
                <?php if ($recent_jobs && mysqli_num_rows($recent_jobs) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-modern table-hover">
                            <thead>
                                <tr>
                                    <th>Job</th>
                                    <th>Company</th>
                                    <th>Status</th>
                                    <th>Posted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($recent_jobs)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                                        <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                                        <td><span class="badge-status badge-<?php echo $row['status']; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <?php if ($row['status'] === 'pending'): ?>
                                                <a href="manage_jobs.php?approve=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-success me-1">Approve</a>
                                                <a href="manage_jobs.php?reject=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-danger me-1" onclick="return confirm('Reject this job?');">Reject</a>
                                            <?php endif; ?>
                                            <a href="<?php echo BASE_URL; ?>job_detail.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-outline-primary" target="_blank">View</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No jobs yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
