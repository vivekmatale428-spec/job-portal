<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$job_id = (int)($_GET['id'] ?? $_POST['job_id'] ?? 0);
if (!$job_id) {
    header('Location: ' . BASE_URL . 'jobseeker/search_jobs.php');
    exit;
}

$job = mysqli_fetch_assoc(mysqli_query($conn, "SELECT j.*, e.company_name FROM jobs j JOIN employers e ON j.employer_id = e.id WHERE j.id = $job_id AND j.status = 'approved'"));
if (!$job) {
    header('Location: ' . BASE_URL . 'jobs.php');
    exit;
}

$uid = (int)$_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT resume_file FROM users WHERE id = $uid"));
$has_profile_resume = !empty($user['resume_file']);
$already = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM applications WHERE job_id = $job_id AND user_id = $uid"));
if ($already) {
    header('Location: ' . BASE_URL . 'job_detail.php?id=' . $job_id);
    exit;
}

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf_post();
    $cover_letter = trim($_POST['cover_letter'] ?? '');
    $resume_file = null;

    // Use profile resume or handle upload
    if (!empty($_POST['use_profile_resume']) && $has_profile_resume) {
        $resume_file = $user['resume_file'];
    } elseif (!empty($_FILES['resume']['name'])) {
        if (!is_dir(RESUME_PATH)) mkdir(RESUME_PATH, 0755, true);
        $ext = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['pdf', 'doc', 'docx'])) {
            $err = 'Resume must be PDF, DOC or DOCX.';
        } elseif ($_FILES['resume']['size'] > MAX_RESUME_SIZE) {
            $err = 'Resume file too large (max 2MB).';
        } elseif ($_FILES['resume']['error'] === UPLOAD_ERR_OK) {
            $resume_file = 'app_' . $uid . '_' . $job_id . '_' . time() . '.' . $ext;
            if (!move_uploaded_file($_FILES['resume']['tmp_name'], RESUME_PATH . $resume_file)) {
                $err = 'Failed to upload resume.';
                $resume_file = null;
            }
        }
    }

    if (empty($err)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO applications (job_id, user_id, cover_letter, resume_file) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'iiss', $job_id, $uid, $cover_letter, $resume_file);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_query($conn, "UPDATE jobs SET applications_count = applications_count + 1 WHERE id = $job_id");
            header('Location: ' . BASE_URL . 'jobseeker/applied_jobs.php?success=1');
            exit;
        }
        $err = 'Failed to submit application.';
    }
}

$page_title = 'Apply for ' . $job['title'];
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container py-5">
    <h1 class="section-title">Apply for Job</h1>
    <p class="section-subtitle"><?php echo htmlspecialchars($job['title']); ?> at <?php echo htmlspecialchars($job['company_name']); ?></p>

    <?php if ($err): ?><div class="alert alert-danger"><?php echo $err; ?></div><?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                    <form method="post" action="" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
                        <div class="mb-3">
                            <label class="form-label">Resume (optional)</label>
                            <?php if ($has_profile_resume): ?>
                                <div class="form-check mb-2">
                                    <input type="checkbox" name="use_profile_resume" id="useProfileResume" class="form-check-input" value="1">
                                    <label class="form-check-label" for="useProfileResume">Use my profile resume</label>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="resume" class="form-control" accept=".pdf,.doc,.docx" id="resumeFile">
                            <small class="text-muted">PDF, DOC or DOCX. Max 2MB. Or use profile resume above.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cover Letter (optional)</label>
                            <textarea name="cover_letter" class="form-control" rows="6" placeholder="Tell the employer why you're a good fit..."><?php echo htmlspecialchars($_POST['cover_letter'] ?? ''); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Application</button>
                        <a href="<?php echo BASE_URL; ?>job_detail.php?id=<?php echo $job_id; ?>" class="btn btn-outline-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
