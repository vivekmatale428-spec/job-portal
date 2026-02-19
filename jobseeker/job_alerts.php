<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$uid = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $id = (int)$_POST['delete'];
        mysqli_query($conn, "DELETE FROM job_alerts WHERE id = $id AND user_id = $uid");
    } else {
        $keywords = trim($_POST['keywords'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0) ?: null;
        $job_type = trim($_POST['job_type'] ?? '') ?: null;
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        if (isset($_POST['alert_id']) && $_POST['alert_id']) {
            $id = (int)$_POST['alert_id'];
            mysqli_query($conn, "UPDATE job_alerts SET keywords='" . mysqli_real_escape_string($conn, $keywords) . "', location='" . mysqli_real_escape_string($conn, $location) . "', category_id=" . ($category_id ?: 'NULL') . ", job_type=" . ($job_type ? "'" . mysqli_real_escape_string($conn, $job_type) . "'" : 'NULL') . ", is_active=$is_active WHERE id=$id AND user_id=$uid");
        } else {
            mysqli_query($conn, "INSERT INTO job_alerts (user_id, keywords, location, category_id, job_type) VALUES ($uid, '" . mysqli_real_escape_string($conn, $keywords) . "', '" . mysqli_real_escape_string($conn, $location) . "', " . ($category_id ?: 'NULL') . ", " . ($job_type ? "'" . mysqli_real_escape_string($conn, $job_type) . "'" : 'NULL') . ")");
        }
    }
    header('Location: ' . BASE_URL . 'jobseeker/job_alerts.php');
    exit;
}

$alerts = mysqli_query($conn, "SELECT ja.*, c.name AS category_name FROM job_alerts ja LEFT JOIN categories c ON ja.category_id = c.id WHERE ja.user_id = $uid ORDER BY ja.created_at DESC");
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");

$page_title = 'Job Alerts';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container py-5">
    <h1 class="section-title">Job Alerts</h1>
    <p class="section-subtitle">Get notified when new jobs match your criteria</p>

    <div class="row">
        <div class="col-lg-5">
            <div class="card mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-3">Create New Alert</h5>
                    <form method="post" action="">
                        <div class="mb-3">
                            <label class="form-label">Keywords</label>
                            <input type="text" name="keywords" class="form-control" placeholder="e.g. PHP, Laravel">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" placeholder="e.g. Mumbai">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">Any</option>
                                <?php mysqli_data_seek($categories, 0); while ($c = mysqli_fetch_assoc($categories)): ?>
                                    <option value="<?php echo (int)$c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Job Type</label>
                            <select name="job_type" class="form-select">
                                <option value="">Any</option>
                                <option value="full_time">Full Time</option>
                                <option value="part_time">Part Time</option>
                                <option value="remote">Remote</option>
                                <option value="internship">Internship</option>
                            </select>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_active" class="form-check-input" value="1" checked>
                            <label class="form-check-label">Active</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Alert</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body p-4">
                    <h5 class="mb-3">Your Alerts</h5>
                    <?php if ($alerts && mysqli_num_rows($alerts) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php while ($a = mysqli_fetch_assoc($alerts)): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo htmlspecialchars($a['keywords'] ?: 'Any'); ?></strong>
                                        <?php if ($a['location']) echo ' • ' . htmlspecialchars($a['location']); ?>
                                        <?php if ($a['category_name']) echo ' • ' . htmlspecialchars($a['category_name']); ?>
                                        <?php if ($a['job_type']) echo ' • ' . htmlspecialchars($a['job_type']); ?>
                                        <span class="badge bg-<?php echo $a['is_active'] ? 'success' : 'secondary'; ?> ms-1"><?php echo $a['is_active'] ? 'Active' : 'Inactive'; ?></span>
                                    </div>
                                    <form method="post" class="d-inline" onsubmit="return confirm('Delete this alert?');">
                                        <input type="hidden" name="delete" value="<?php echo (int)$a['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">No alerts yet. Create one to get notified about matching jobs.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
