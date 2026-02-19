<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check_employer.php';

$eid = (int)$_SESSION['employer_id'];
$msg = '';
$err = '';

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    if (empty($title) || empty($description)) {
        $err = 'Title and description are required.';
    } else {
        $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($title)) . '-' . time();
        $stmt = mysqli_prepare($conn, "INSERT INTO jobs (employer_id, category_id, title, slug, description, requirements, location, job_type, experience_level, salary_min, salary_max, salary_show, vacancies, deadline, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        mysqli_stmt_bind_param($stmt, 'iisssssssiiiss', $eid, $category_id, $title, $slug, $description, $requirements, $location, $job_type, $experience_level, $salary_min, $salary_max, $salary_show, $vacancies, $deadline);
        if (mysqli_stmt_execute($stmt)) {
            $msg = 'Job posted successfully! It will be visible after admin approval.';
            $_POST = [];
        } else {
            $err = 'Failed to post job.';
        }
    }
}

$page_title = 'Post a Job';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container py-5">
    <h1 class="section-title">Post a Job</h1>
    <p class="section-subtitle">Create a new job posting</p>

    <?php if ($msg): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
    <?php if ($err): ?><div class="alert alert-danger"><?php echo $err; ?></div><?php endif; ?>

    <div class="card">
        <div class="card-body p-4">
            <form method="post" action="">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Job Title *</label>
                        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">Select</option>
                            <?php while ($c = mysqli_fetch_assoc($categories)): ?>
                                <option value="<?php echo (int)$c['id']; ?>" <?php echo ($_POST['category_id'] ?? '') == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Job Type</label>
                        <select name="job_type" class="form-select">
                            <option value="full_time" <?php echo ($_POST['job_type'] ?? '') === 'full_time' ? 'selected' : ''; ?>>Full Time</option>
                            <option value="part_time" <?php echo ($_POST['job_type'] ?? '') === 'part_time' ? 'selected' : ''; ?>>Part Time</option>
                            <option value="contract" <?php echo ($_POST['job_type'] ?? '') === 'contract' ? 'selected' : ''; ?>>Contract</option>
                            <option value="internship" <?php echo ($_POST['job_type'] ?? '') === 'internship' ? 'selected' : ''; ?>>Internship</option>
                            <option value="remote" <?php echo ($_POST['job_type'] ?? '') === 'remote' ? 'selected' : ''; ?>>Remote</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Experience Level</label>
                        <select name="experience_level" class="form-select">
                            <option value="fresher" <?php echo ($_POST['experience_level'] ?? '') === 'fresher' ? 'selected' : ''; ?>>Fresher</option>
                            <option value="1-2" <?php echo ($_POST['experience_level'] ?? '') === '1-2' ? 'selected' : ''; ?>>1-2 years</option>
                            <option value="3-5" <?php echo ($_POST['experience_level'] ?? '') === '3-5' ? 'selected' : ''; ?>>3-5 years</option>
                            <option value="5+" <?php echo ($_POST['experience_level'] ?? '') === '5+' ? 'selected' : ''; ?>>5+ years</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Vacancies</label>
                        <input type="number" name="vacancies" class="form-control" min="1" value="<?php echo (int)($_POST['vacancies'] ?? 1); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Salary Min (₹/month)</label>
                        <input type="number" name="salary_min" class="form-control" min="0" value="<?php echo (int)($_POST['salary_min'] ?? 0); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Salary Max (₹/month)</label>
                        <input type="number" name="salary_max" class="form-control" min="0" value="<?php echo (int)($_POST['salary_max'] ?? 0); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label d-block">&nbsp;</label>
                        <div class="form-check">
                            <input type="checkbox" name="salary_show" class="form-check-input" value="1" <?php echo !isset($_POST['salary_show']) || $_POST['salary_show'] ? 'checked' : ''; ?>>
                            <label class="form-check-label">Show salary on listing</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Application Deadline</label>
                        <input type="date" name="deadline" class="form-control" value="<?php echo htmlspecialchars($_POST['deadline'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Job Description *</label>
                        <textarea name="description" class="form-control" rows="5" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Requirements</label>
                        <textarea name="requirements" class="form-control" rows="4"><?php echo htmlspecialchars($_POST['requirements'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Post Job</button>
                        <a href="dashboard.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
