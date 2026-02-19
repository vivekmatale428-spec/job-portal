<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check_employer.php';

$eid = (int)$_SESSION['employer_id'];

$jobs = mysqli_query($conn, "SELECT j.*, (SELECT COUNT(*) FROM applications WHERE job_id = j.id) AS app_count FROM jobs j WHERE j.employer_id = $eid ORDER BY j.created_at DESC");

$page_title = 'Manage Jobs';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container py-5">
    <h1 class="section-title">Manage Jobs</h1>
    <p class="section-subtitle">Edit, close, or view applicants for your jobs</p>

    <div class="mb-3">
        <a href="post_job.php" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Post New Job</a>
    </div>

    <?php if ($jobs && mysqli_num_rows($jobs) > 0): ?>
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Job</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Applications</th>
                                <th>Posted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($jobs)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td><?php echo htmlspecialchars(str_replace('_', ' ', $row['job_type'])); ?></td>
                                    <td><span class="badge-status badge-<?php echo $row['status']; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                    <td><?php echo (int)$row['app_count']; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <a href="view_applicants.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-outline-primary me-1">Applicants</a>
                                        <a href="edit_job.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-outline-secondary me-1">Edit</a>
                                        <?php if ($row['status'] === 'approved'): ?>
                                            <a href="edit_job.php?id=<?php echo (int)$row['id']; ?>&close=1" class="btn btn-sm btn-outline-danger" onclick="return confirm('Close this job?');">Close</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card p-5 text-center">
            <i class="bi bi-briefcase display-4 text-muted"></i>
            <p class="mb-0 text-muted mt-2">No jobs posted yet.</p>
            <a href="post_job.php" class="btn btn-primary mt-3">Post a Job</a>
        </div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
