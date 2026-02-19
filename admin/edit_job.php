<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check_admin.php';

$job_id = (int)($_GET['id'] ?? 0);
if (!$job_id) {
    header('Location: ' . BASE_URL . 'admin/manage_jobs.php');
    exit;
}

$job = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM jobs WHERE id = $job_id"));
if (!$job) {
    header('Location: ' . BASE_URL . 'admin/manage_jobs.php');
    exit;
}

$msg = '';
$err = '';
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
$employers = mysqli_query($conn, "SELECT id, company_name FROM employers ORDER BY company_name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf_post();
    $employer_id = (int)($_POST['employer_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $requirements = trim($_POST['requirements'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $job_type = $_POST['job_type'] ?? 'full_time';
    $experience_level = $_POST['experience_level'] ?? 'fresher';
    $salary_min = (int)($_POST['salary_min'] ?? 0);
    $salary_max = (int)($_POST['salary_max'] ?? 0);
    $salary_show = isset($_POST['salary_show']) ? 1 : 0;
    $vacancies = max(1, (int)($_POST['vacancies'] ?? 1));
    $category_id = (int)($_POST['category_id'] ?? 0) ?: null;
    $deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;
    $featured = isset($_POST['featured']) ? 1 : 0;
    $status = in_array($_POST['status'] ?? '', ['pending','approved','rejected','closed']) ? $_POST['status'] : 'pending';

    if (empty($title) || empty($description) || !$employer_id) {
        $err = 'Title, description and employer are required.';
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE jobs SET employer_id=?, category_id = NULLIF(?, 0), title=?, description=?, requirements=?, location=?, job_type=?, experience_level=?, salary_min=?, salary_max=?, salary_show=?, vacancies=?, deadline=?, featured=?, status=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'iissssssiiississi', $employer_id, $category_id, $title, $description, $requirements, $location, $job_type, $experience_level, $salary_min, $salary_max, $salary_show, $vacancies, $deadline, $featured, $status, $job_id);
        if (mysqli_stmt_execute($stmt)) {
            set_flash('success', 'Job updated successfully.');
            header('Location: ' . BASE_URL . 'admin/manage_jobs.php');
            exit;
        } else {
            $err = 'Update failed.';
        }
    }
}

$page_title = 'Edit Job';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container py-5">
    <h1 class="section-title">Edit Job</h1>
    <p class="section-subtitle"><?php echo htmlspecialchars($job['title']); ?></p>

    <?php if ($err): ?><div class="alert alert-danger"><?php echo $err; ?></div><?php endif; ?>

    <div class="mb-3">
        <a href="manage_jobs.php" class="btn btn-outline-secondary">Back to Jobs</a>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <form method="post" action="">
                <?php echo csrf_field(); ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Employer *</label>
                        <select name="employer_id" class="form-select" required>
                            <?php mysqli_data_seek($employers, 0); while ($e = mysqli_fetch_assoc($employers)): ?>
                                <option value="<?php echo (int)$e['id']; ?>" <?php echo $job['employer_id'] == $e['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($e['company_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">Select</option>
                            <?php mysqli_data_seek($categories, 0); while ($c = mysqli_fetch_assoc($categories)): ?>
                                <option value="<?php echo (int)$c['id']; ?>" <?php echo $job['category_id'] == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Job Title *</label>
                        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($job['title']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($job['location'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Job Type</label>
                        <select name="job_type" class="form-select">
                            <?php foreach (['full_time','part_time','contract','internship','remote'] as $t): ?>
                                <option value="<?php echo $t; ?>" <?php echo $job['job_type'] === $t ? 'selected' : ''; ?>><?php echo ucfirst(str_replace('_',' ',$t)); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="pending" <?php echo $job['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?php echo $job['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                            <option value="rejected" <?php echo $job['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                            <option value="closed" <?php echo $job['status'] === 'closed' ? 'selected' : ''; ?>>Closed</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Experience Level</label>
                        <select name="experience_level" class="form-select">
                            <?php foreach (['fresher','1-2','3-5','5+'] as $e): ?>
                                <option value="<?php echo $e; ?>" <?php echo ($job['experience_level'] ?? '') === $e ? 'selected' : ''; ?>><?php echo $e; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Vacancies</label>
                        <input type="number" name="vacancies" class="form-control" min="1" value="<?php echo (int)$job['vacancies']; ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Deadline</label>
                        <input type="date" name="deadline" class="form-control" value="<?php echo $job['deadline'] ? date('Y-m-d', strtotime($job['deadline'])) : ''; ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Salary Min (₹/month)</label>
                        <input type="number" name="salary_min" class="form-control" min="0" value="<?php echo (int)$job['salary_min']; ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Salary Max (₹/month)</label>
                        <input type="number" name="salary_max" class="form-control" min="0" value="<?php echo (int)$job['salary_max']; ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label d-block">&nbsp;</label>
                        <div class="form-check">
                            <input type="checkbox" name="salary_show" class="form-check-input" value="1" <?php echo $job['salary_show'] ? 'checked' : ''; ?>>
                            <label class="form-check-label">Show salary</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="featured" class="form-check-input" value="1" <?php echo $job['featured'] ? 'checked' : ''; ?>>
                            <label class="form-check-label">Featured</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Job Description *</label>
                        <textarea name="description" class="form-control" rows="5" required><?php echo htmlspecialchars($job['description']); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Requirements</label>
                        <textarea name="requirements" class="form-control" rows="4"><?php echo htmlspecialchars($job['requirements'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Update Job</button>
                        <a href="manage_jobs.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
