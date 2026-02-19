<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check_admin.php';
require_once __DIR__ . '/../includes/upload_helper.php';

$employer_id = (int)($_GET['id'] ?? 0);
if (!$employer_id) {
    header('Location: ' . BASE_URL . 'admin/manage_employers.php');
    exit;
}

$employer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM employers WHERE id = $employer_id"));
if (!$employer) {
    header('Location: ' . BASE_URL . 'admin/manage_employers.php');
    exit;
}

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf_post();
    $company_name = trim($_POST['company_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $industry = trim($_POST['industry'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Handle logo upload
    $logo_file = $employer['company_logo'] ?? null;
    if (!empty($_FILES['logo']['name'])) {
        $upload = upload_image($_FILES['logo'], 'logo');
        if ($upload['success']) {
            if ($logo_file) delete_image($logo_file, 'logo');
            $logo_file = $upload['filename'];
        } else {
            $err = $upload['error'];
        }
    }

    if (empty($company_name) || empty($email)) {
        $err = 'Company name and email are required.';
    } else {
        $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM employers WHERE email = '" . mysqli_real_escape_string($conn, $email) . "' AND id != $employer_id"));
        if ($check) {
            $err = 'Email already exists.';
        } else {
            if (!empty($password)) {
                if (strlen($password) < 6) {
                    $err = 'Password must be at least 6 characters.';
                } else {
                    $logo_sql = $logo_file ? ", company_logo='" . mysqli_real_escape_string($conn, $logo_file) . "'" : "";
                    if (!empty($password)) {
                        $hash = password_hash($password, PASSWORD_DEFAULT);
                        mysqli_query($conn, "UPDATE employers SET company_name='" . mysqli_real_escape_string($conn, $company_name) . "', email='" . mysqli_real_escape_string($conn, $email) . "', password='" . mysqli_real_escape_string($conn, $hash) . "', phone='" . mysqli_real_escape_string($conn, $phone) . "', website='" . mysqli_real_escape_string($conn, $website) . "', location='" . mysqli_real_escape_string($conn, $location) . "', industry='" . mysqli_real_escape_string($conn, $industry) . "', description='" . mysqli_real_escape_string($conn, $description) . "'$logo_sql WHERE id = $employer_id");
                    } else {
                        mysqli_query($conn, "UPDATE employers SET company_name='" . mysqli_real_escape_string($conn, $company_name) . "', email='" . mysqli_real_escape_string($conn, $email) . "', phone='" . mysqli_real_escape_string($conn, $phone) . "', website='" . mysqli_real_escape_string($conn, $website) . "', location='" . mysqli_real_escape_string($conn, $location) . "', industry='" . mysqli_real_escape_string($conn, $industry) . "', description='" . mysqli_real_escape_string($conn, $description) . "'$logo_sql WHERE id = $employer_id");
                    }
                    set_flash('success', 'Employer updated successfully.');
                    header('Location: ' . BASE_URL . 'admin/manage_employers.php');
                    exit;
                }
            } else {
                mysqli_query($conn, "UPDATE employers SET company_name='" . mysqli_real_escape_string($conn, $company_name) . "', email='" . mysqli_real_escape_string($conn, $email) . "', phone='" . mysqli_real_escape_string($conn, $phone) . "', website='" . mysqli_real_escape_string($conn, $website) . "', location='" . mysqli_real_escape_string($conn, $location) . "', industry='" . mysqli_real_escape_string($conn, $industry) . "', description='" . mysqli_real_escape_string($conn, $description) . "' WHERE id = $employer_id");
                set_flash('success', 'Employer updated successfully.');
                header('Location: ' . BASE_URL . 'admin/manage_employers.php');
                exit;
            }
        }
    }
}

$page_title = 'Edit Employer';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container py-5">
    <h1 class="section-title">Edit Employer</h1>
    <p class="section-subtitle"><?php echo htmlspecialchars($employer['company_name']); ?></p>

    <div class="mb-3">
        <a href="manage_employers.php" class="btn btn-outline-secondary">Back to Employers</a>
    </div>

    <?php if ($err): ?><div class="alert alert-danger"><?php echo $err; ?></div><?php endif; ?>

    <div class="card">
        <div class="card-body p-4">
            <form method="post" action="" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Company Name *</label>
                        <input type="text" name="company_name" class="form-control" value="<?php echo htmlspecialchars($employer['company_name']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($employer['email']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">New Password (leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control" minlength="6">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($employer['phone'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Website</label>
                        <input type="url" name="website" class="form-control" value="<?php echo htmlspecialchars($employer['website'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($employer['location'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Industry</label>
                        <input type="text" name="industry" class="form-control" value="<?php echo htmlspecialchars($employer['industry'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Company Logo</label>
                        <?php if (!empty($employer['company_logo'])): ?>
                            <div class="mb-2">
                                <img src="<?php echo BASE_URL; ?>uploads/images/logos/<?php echo htmlspecialchars($employer['company_logo']); ?>" alt="Logo" style="max-height:80px;" class="img-thumbnail">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                        <small class="text-muted">JPEG, PNG, GIF, WebP. Max 1MB.</small>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($employer['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Update Employer</button>
                        <a href="manage_employers.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
