<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check_admin.php';

// Get statistics
$total_users = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM users"));
$total_employers = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM employers"));
$total_jobs = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM jobs"));
$total_applications = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM applications"));
$pending_jobs = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM jobs WHERE status = 'pending'"));
$approved_jobs = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM jobs WHERE status = 'approved'"));
$featured_jobs = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM jobs WHERE featured = 1"));

// Jobs by category
$jobs_by_category = mysqli_query($conn, "SELECT c.name, COUNT(j.id) as count FROM categories c LEFT JOIN jobs j ON c.id = j.category_id AND j.status = 'approved' GROUP BY c.id, c.name ORDER BY count DESC");

// Jobs by type
$jobs_by_type = mysqli_query($conn, "SELECT job_type, COUNT(*) as count FROM jobs WHERE status = 'approved' GROUP BY job_type");

// Recent registrations (last 30 days)
$recent_users = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"));
$recent_employers = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM employers WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"));

// Applications by status
$apps_pending = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM applications WHERE status = 'pending'"));
$apps_shortlisted = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM applications WHERE status = 'shortlisted'"));
$apps_hired = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM applications WHERE status = 'hired'"));

$page_title = 'Analytics & Reports';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container py-5">
    <h1 class="section-title">Analytics & Reports</h1>
    <p class="section-subtitle">System statistics and insights</p>

    <div class="mb-3">
        <a href="dashboard.php" class="btn btn-outline-secondary">Dashboard</a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value"><?php echo $total_users; ?></div>
                <div class="stat-label">Total Users</div>
                <small class="text-muted">+<?php echo $recent_users; ?> this month</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value"><?php echo $total_employers; ?></div>
                <div class="stat-label">Total Employers</div>
                <small class="text-muted">+<?php echo $recent_employers; ?> this month</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value"><?php echo $total_jobs; ?></div>
                <div class="stat-label">Total Jobs</div>
                <small class="text-muted"><?php echo $approved_jobs; ?> approved</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value"><?php echo $total_applications; ?></div>
                <div class="stat-label">Total Applications</div>
                <small class="text-muted"><?php echo $apps_hired; ?> hired</small>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Jobs by Category</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Jobs</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($jobs_by_category)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><strong><?php echo (int)$row['count']; ?></strong></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Jobs by Type</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($jobs_by_type)): ?>
                                    <tr>
                                        <td><?php echo ucfirst(str_replace('_', ' ', $row['job_type'])); ?></td>
                                        <td><strong><?php echo (int)$row['count']; ?></strong></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Application Status</h5>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Pending</span>
                            <span class="badge bg-warning"><?php echo $apps_pending; ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Shortlisted</span>
                            <span class="badge bg-info"><?php echo $apps_shortlisted; ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Hired</span>
                            <span class="badge bg-success"><?php echo $apps_hired; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Job Status</h5>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Pending Approval</span>
                            <span class="badge bg-warning"><?php echo $pending_jobs; ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Approved</span>
                            <span class="badge bg-success"><?php echo $approved_jobs; ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Featured</span>
                            <span class="badge bg-primary"><?php echo $featured_jobs; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
