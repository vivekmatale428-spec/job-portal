<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$uid = (int)$_SESSION['user_id'];

$applications_count = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM applications WHERE user_id = $uid"));
$saved_count = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM saved_jobs WHERE user_id = $uid"));
$shortlisted = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM applications WHERE user_id = $uid AND status = 'shortlisted'"));

$recent_apps = mysqli_query($conn, "SELECT a.*, j.title, j.location, e.company_name FROM applications a 
    JOIN jobs j ON a.job_id = j.id 
    JOIN employers e ON j.employer_id = e.id 
    WHERE a.user_id = $uid ORDER BY a.applied_at DESC LIMIT 5");

$page_title = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container py-5">
    <h1 class="section-title">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
    <p class="section-subtitle">Manage your job search from here</p>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-value"><?php echo $applications_count; ?></div>
                <div class="stat-label">Applications Sent</div>
                <a href="applied_jobs.php" class="btn btn-sm btn-outline-primary mt-2">View All</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-value"><?php echo $shortlisted; ?></div>
                <div class="stat-label">Shortlisted</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-value"><?php echo $saved_count; ?></div>
                <div class="stat-label">Saved Jobs</div>
                <a href="saved_jobs.php" class="btn btn-sm btn-outline-primary mt-2">View Saved</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card">
                <h5><i class="bi bi-clock-history me-2"></i>Recent Applications</h5>
                <?php if ($recent_apps && mysqli_num_rows($recent_apps) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-modern table-hover">
                            <thead>
                                <tr>
                                    <th>Job</th>
                                    <th>Company</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($recent_apps)): ?>
                                    <tr>
                                        <td><a href="<?php echo BASE_URL; ?>job_detail.php?id=<?php echo (int)$row['job_id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></td>
                                        <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                                        <td><span class="badge-status badge-<?php echo $row['status']; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                        <td><?php echo date('M d, Y', strtotime($row['applied_at'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No applications yet. <a href="search_jobs.php">Search jobs</a> and apply.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="dashboard-card">
                <h5><i class="bi bi-lightning me-2"></i>Quick Actions</h5>
                <a href="search_jobs.php" class="btn btn-primary w-100 mb-2"><i class="bi bi-search me-2"></i>Search Jobs</a>
                <a href="job_alerts.php" class="btn btn-outline-primary w-100 mb-2"><i class="bi bi-bell me-2"></i>Job Alerts</a>
                <a href="profile.php" class="btn btn-outline-primary w-100 mb-2"><i class="bi bi-person me-2"></i>Edit Profile</a>
                <a href="applied_jobs.php" class="btn btn-outline-secondary w-100"><i class="bi bi-file-earmark me-2"></i>Applied Jobs</a>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
