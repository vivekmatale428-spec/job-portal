<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$uid = (int)$_SESSION['user_id'];
$success = isset($_GET['success']);

$apps = mysqli_query($conn, "SELECT a.*, j.title, j.location, j.job_type, e.company_name FROM applications a 
    JOIN jobs j ON a.job_id = j.id 
    JOIN employers e ON j.employer_id = e.id 
    WHERE a.user_id = $uid ORDER BY a.applied_at DESC");

$page_title = 'Applied Jobs';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container py-5">
    <h1 class="section-title">Applied Jobs</h1>
    <p class="section-subtitle">Your job applications and status</p>

    <?php if ($success): ?>
        <div class="alert alert-success">Application submitted successfully!</div>
    <?php endif; ?>

    <?php if ($apps && mysqli_num_rows($apps) > 0): ?>
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Job</th>
                                <th>Company</th>
                                <th>Location</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Applied</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($apps)): ?>
                                <tr>
                                    <td><a href="<?php echo BASE_URL; ?>job_detail.php?id=<?php echo (int)$row['job_id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></td>
                                    <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['location'] ?? '—'); ?></td>
                                    <td><?php echo htmlspecialchars(str_replace('_', ' ', $row['job_type'])); ?></td>
                                    <td><span class="badge-status badge-<?php echo $row['status']; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                    <td><?php echo date('M d, Y', strtotime($row['applied_at'])); ?></td>
                                    <td><a href="<?php echo BASE_URL; ?>job_detail.php?id=<?php echo (int)$row['job_id']; ?>" class="btn btn-sm btn-outline-primary">View</a></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card p-5 text-center">
            <i class="bi bi-file-earmark display-4 text-muted"></i>
            <p class="mb-0 text-muted mt-2">You haven't applied to any jobs yet.</p>
            <a href="search_jobs.php" class="btn btn-primary mt-3">Search Jobs</a>
        </div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
