<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check_admin.php';

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf_post();
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $qualification = trim($_POST['qualification'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    $skills = trim($_POST['skills'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    if (empty($full_name) || empty($email) || empty($password)) {
        $err = 'Name, email and password are required.';
    } elseif (strlen($password) < 6) {
        $err = 'Password must be at least 6 characters.';
    } else {
        $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM users WHERE email = '" . mysqli_real_escape_string($conn, $email) . "'"));
        if ($check) {
            $err = 'Email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO users (full_name, email, password, phone, location, qualification, experience, skills, bio) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'sssssssss', $full_name, $email, $hash, $phone, $location, $qualification, $experience, $skills, $bio);
            if (mysqli_stmt_execute($stmt)) {
                set_flash('success', 'User added successfully.');
                header('Location: ' . BASE_URL . 'admin/manage_users.php');
                exit;
            } else {
                $err = 'Failed to add user.';
            }
        }
    }
}

$page_title = 'Add User';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container py-5">
    <h1 class="section-title">Add New User</h1>
    <p class="section-subtitle">Create a new job seeker account</p>

    <div class="mb-3">
        <a href="manage_users.php" class="btn btn-outline-secondary">Back to Users</a>
    </div>

    <?php if ($err): ?><div class="alert alert-danger"><?php echo $err; ?></div><?php endif; ?>

    <div class="card">
        <div class="card-body p-4">
            <form method="post" action="">
                <?php echo csrf_field(); ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Qualification</label>
                        <input type="text" name="qualification" class="form-control" value="<?php echo htmlspecialchars($_POST['qualification'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Experience</label>
                        <input type="text" name="experience" class="form-control" placeholder="e.g. 2 years" value="<?php echo htmlspecialchars($_POST['experience'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Skills</label>
                        <textarea name="skills" class="form-control" rows="2"><?php echo htmlspecialchars($_POST['skills'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Bio</label>
                        <textarea name="bio" class="form-control" rows="3"><?php echo htmlspecialchars($_POST['bio'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Add User</button>
                        <a href="manage_users.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
