<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check_admin.php';
require_once __DIR__ . '/../includes/upload_helper.php';

$user_id = (int)($_GET['id'] ?? 0);
if (!$user_id) {
    header('Location: ' . BASE_URL . 'admin/manage_users.php');
    exit;
}

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id"));
if (!$user) {
    header('Location: ' . BASE_URL . 'admin/manage_users.php');
    exit;
}

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf_post();
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $qualification = trim($_POST['qualification'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    $skills = trim($_POST['skills'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $photo_file = $user['profile_photo'] ?? null;

    // Handle profile photo upload
    if (!empty($_FILES['photo']['name'])) {
        $upload = upload_image($_FILES['photo'], 'profile');
        if ($upload['success']) {
            if ($photo_file) delete_image($photo_file, 'profile');
            $photo_file = $upload['filename'];
        } else {
            $err = $upload['error'];
        }
    }

    if (empty($full_name) || empty($email)) {
        $err = 'Name and email are required.';
    } else {
        $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM users WHERE email = '" . mysqli_real_escape_string($conn, $email) . "' AND id != $user_id"));
        if ($check) {
            $err = 'Email already exists.';
        } else {
            if (!empty($password)) {
                if (strlen($password) < 6) {
                    $err = 'Password must be at least 6 characters.';
                } else {
                    $photo_sql = $photo_file ? ", profile_photo='" . mysqli_real_escape_string($conn, $photo_file) . "'" : "";
                    if (!empty($password)) {
                        $hash = password_hash($password, PASSWORD_DEFAULT);
                        mysqli_query($conn, "UPDATE users SET full_name='" . mysqli_real_escape_string($conn, $full_name) . "', email='" . mysqli_real_escape_string($conn, $email) . "', password='" . mysqli_real_escape_string($conn, $hash) . "', phone='" . mysqli_real_escape_string($conn, $phone) . "', location='" . mysqli_real_escape_string($conn, $location) . "', qualification='" . mysqli_real_escape_string($conn, $qualification) . "', experience='" . mysqli_real_escape_string($conn, $experience) . "', skills='" . mysqli_real_escape_string($conn, $skills) . "', bio='" . mysqli_real_escape_string($conn, $bio) . "'$photo_sql WHERE id = $user_id");
                    } else {
                        mysqli_query($conn, "UPDATE users SET full_name='" . mysqli_real_escape_string($conn, $full_name) . "', email='" . mysqli_real_escape_string($conn, $email) . "', phone='" . mysqli_real_escape_string($conn, $phone) . "', location='" . mysqli_real_escape_string($conn, $location) . "', qualification='" . mysqli_real_escape_string($conn, $qualification) . "', experience='" . mysqli_real_escape_string($conn, $experience) . "', skills='" . mysqli_real_escape_string($conn, $skills) . "', bio='" . mysqli_real_escape_string($conn, $bio) . "'$photo_sql WHERE id = $user_id");
                    }
                    set_flash('success', 'User updated successfully.');
                    header('Location: ' . BASE_URL . 'admin/manage_users.php');
                    exit;
                }
            } else {
                mysqli_query($conn, "UPDATE users SET full_name='" . mysqli_real_escape_string($conn, $full_name) . "', email='" . mysqli_real_escape_string($conn, $email) . "', phone='" . mysqli_real_escape_string($conn, $phone) . "', location='" . mysqli_real_escape_string($conn, $location) . "', qualification='" . mysqli_real_escape_string($conn, $qualification) . "', experience='" . mysqli_real_escape_string($conn, $experience) . "', skills='" . mysqli_real_escape_string($conn, $skills) . "', bio='" . mysqli_real_escape_string($conn, $bio) . "' WHERE id = $user_id");
                set_flash('success', 'User updated successfully.');
                header('Location: ' . BASE_URL . 'admin/manage_users.php');
                exit;
            }
        }
    }
}

$page_title = 'Edit User';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container py-5">
    <h1 class="section-title">Edit User</h1>
    <p class="section-subtitle"><?php echo htmlspecialchars($user['full_name']); ?></p>

    <div class="mb-3">
        <a href="manage_users.php" class="btn btn-outline-secondary">Back to Users</a>
    </div>

    <?php if ($err): ?><div class="alert alert-danger"><?php echo $err; ?></div><?php endif; ?>

    <div class="card">
        <div class="card-body p-4">
            <form method="post" action="" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">New Password (leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control" minlength="6">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Profile Photo</label>
                        <?php if (!empty($user['profile_photo'])): ?>
                            <div class="mb-2">
                                <img src="<?php echo BASE_URL; ?>uploads/images/profiles/<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Photo" style="max-height:80px;" class="img-thumbnail">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                        <small class="text-muted">JPEG, PNG, GIF, WebP. Max 1MB.</small>
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
                        <input type="text" name="experience" class="form-control" value="<?php echo htmlspecialchars($user['experience'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Skills</label>
                        <textarea name="skills" class="form-control" rows="2"><?php echo htmlspecialchars($user['skills'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Bio</label>
                        <textarea name="bio" class="form-control" rows="3"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Update User</button>
                        <a href="manage_users.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
