<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check_employer.php';

$eid = (int)$_SESSION['employer_id'];

$jobs_count = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM jobs WHERE employer_id = $eid"));
$active_jobs = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM jobs WHERE employer_id = $eid AND status = 'approved'"));
$pending_jobs = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM jobs WHERE employer_id = $eid AND status = 'pending'"));
$total_apps = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM applications a JOIN jobs j ON a.job_id = j.id WHERE j.employer_id = $eid"));

$recent_jobs = mysqli_query($conn, "SELECT j.*, (SELECT COUNT(*) FROM applications WHERE job_id = j.id) AS app_count FROM jobs j WHERE j.employer_id = $eid ORDER BY j.created_at DESC LIMIT 5");

$page_title = 'Employer Dashboard';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container py-5">
    <h1 class="section-title">Welcome, <?php echo htmlspecialchars($_SESSION['company_name']); ?></h1>
    <p class="section-subtitle">Manage your job postings and applicants</p>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value"><?php echo $jobs_count; ?></div>
                <div class="stat-label">Total Jobs</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value"><?php echo $active_jobs; ?></div>
                <div class="stat-label">Active Jobs</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value"><?php echo $pending_jobs; ?></div>
                <div class="stat-label">Pending Approval</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value"><?php echo $total_apps; ?></div>
                <div class="stat-label">Total Applications</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card">
                <h5><i class="bi bi-briefcase me-2"></i>Recent Job Postings</h5>
                <?php if ($recent_jobs && mysqli_num_rows($recent_jobs) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-modern table-hover">
                            <thead>
                                <tr>
                                    <th>Job</th>
                                    <th>Status</th>
                                    <th>Applications</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($recent_jobs)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                                        <td><span class="badge-status badge-<?php echo $row['status']; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                        <td><?php echo (int)$row['app_count']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <a href="view_applicants.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-outline-primary me-1">Applicants</a>
                                            <a href="edit_job.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No jobs posted yet. <a href="post_job.php">Post your first job</a>.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="dashboard-card">
                <h5><i class="bi bi-lightning me-2"></i>Quick Actions</h5>
                <a href="post_job.php" class="btn btn-primary w-100 mb-2"><i class="bi bi-plus-circle me-2"></i>Post a Job</a>
                <a href="manage_jobs.php" class="btn btn-outline-primary w-100 mb-2"><i class="bi bi-list-ul me-2"></i>Manage Jobs</a>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
