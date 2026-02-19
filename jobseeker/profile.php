<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$uid = (int)$_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $uid"));
if (!$user) {
    header('Location: ' . BASE_URL . 'jobseeker/dashboard.php');
    exit;
}

$page_title = 'My Profile';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container py-5">
    <h1 class="section-title">My Profile</h1>
    <p class="section-subtitle">Your job seeker profile</p>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-medium text-muted">Full Name</div>
                        <div class="col-sm-8"><?php echo htmlspecialchars($user['full_name']); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-medium text-muted">Email</div>
                        <div class="col-sm-8"><?php echo htmlspecialchars($user['email']); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-medium text-muted">Phone</div>
                        <div class="col-sm-8"><?php echo htmlspecialchars($user['phone'] ?? '—'); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-medium text-muted">Location</div>
                        <div class="col-sm-8"><?php echo htmlspecialchars($user['location'] ?? '—'); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-medium text-muted">Qualification</div>
                        <div class="col-sm-8"><?php echo htmlspecialchars($user['qualification'] ?? '—'); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-medium text-muted">Experience</div>
                        <div class="col-sm-8"><?php echo htmlspecialchars($user['experience'] ?? '—'); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-medium text-muted">Skills</div>
                        <div class="col-sm-8"><?php echo nl2br(htmlspecialchars($user['skills'] ?? '—')); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-medium text-muted">Resume</div>
                        <div class="col-sm-8">
                            <?php if (!empty($user['resume_file'])): ?>
                                <a href="<?php echo BASE_URL; ?>uploads/resumes/<?php echo htmlspecialchars($user['resume_file']); ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-download me-1"></i>Download</a>
                            <?php else: ?>
                                <span class="text-muted">Not uploaded</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (!empty($user['bio'])): ?>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-medium text-muted">Bio</div>
                        <div class="col-sm-8"><?php echo nl2br(htmlspecialchars($user['bio'])); ?></div>
                    </div>
                    <?php endif; ?>
                    <a href="edit_profile.php" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
