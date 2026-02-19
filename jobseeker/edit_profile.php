<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$uid = (int)$_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $uid"));
if (!$user) {
    header('Location: ' . BASE_URL . 'jobseeker/dashboard.php');
    exit;
}

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $qualification = trim($_POST['qualification'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    $skills = trim($_POST['skills'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    if (empty($full_name)) {
        $err = 'Full name is required.';
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE users SET full_name=?, phone=?, location=?, qualification=?, experience=?, skills=?, bio=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'sssssssi', $full_name, $phone, $location, $qualification, $experience, $skills, $bio, $uid);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['user_name'] = $full_name;
            $msg = 'Profile updated successfully.';
            $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $uid"));
        } else {
            $err = 'Update failed.';
        }
    }

    // Resume upload
    if (!empty($_FILES['resume']['name']) && empty($err)) {
        if (!is_dir(RESUME_PATH)) mkdir(RESUME_PATH, 0755, true);
        $ext = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['pdf', 'doc', 'docx'])) {
            $err = 'Only PDF, DOC, DOCX allowed.';
        } elseif ($_FILES['resume']['size'] > MAX_RESUME_SIZE) {
            $err = 'File too large (max 2MB).';
        } else {
            $fname = 'resume_' . $uid . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['resume']['tmp_name'], RESUME_PATH . $fname)) {
                if (!empty($user['resume_file']) && file_exists(RESUME_PATH . $user['resume_file'])) unlink(RESUME_PATH . $user['resume_file']);
                mysqli_query($conn, "UPDATE users SET resume_file = '" . mysqli_real_escape_string($conn, $fname) . "' WHERE id = $uid");
                $msg = ($msg ? $msg . ' ' : '') . 'Resume uploaded.';
            }
        }
    }
}

$page_title = 'Edit Profile';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container py-5">
    <h1 class="section-title">Edit Profile</h1>
    <?php if ($msg): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
    <?php if ($err): ?><div class="alert alert-danger"><?php echo $err; ?></div><?php endif; ?>

    <div class="card">
        <div class="card-body p-4">
            <form method="post" action="" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Qualification</label>
                        <input type="text" name="qualification" class="form-control" value="<?php echo htmlspecialchars($user['qualification'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Experience</label>
                        <input type="text" name="experience" class="form-control" placeholder="e.g. 2 years" value="<?php echo htmlspecialchars($user['experience'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Skills</label>
                        <textarea name="skills" class="form-control" rows="3" placeholder="Comma separated or one per line"><?php echo htmlspecialchars($user['skills'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Bio</label>
                        <textarea name="bio" class="form-control" rows="4"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Resume (PDF/DOC/DOCX, max 2MB)</label>
                        <input type="file" name="resume" class="form-control" accept=".pdf,.doc,.docx">
                        <?php if (!empty($user['resume_file'])): ?>
                            <small class="text-muted">Current: <?php echo htmlspecialchars($user['resume_file']); ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Save Profile</button>
                        <a href="profile.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
