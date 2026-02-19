<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check_admin.php';
require_once __DIR__ . '/../includes/upload_helper.php';

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf_post();
    $company_name = trim($_POST['company_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $industry = trim($_POST['industry'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $logo_file = null;

    // Handle logo upload
    if (!empty($_FILES['logo']['name'])) {
        $upload = upload_image($_FILES['logo'], 'logo');
        if ($upload['success']) {
            $logo_file = $upload['filename'];
        } else {
            $err = $upload['error'];
        }
    }

    if (empty($company_name) || empty($email) || empty($password)) {
        $err = 'Company name, email and password are required.';
    } elseif (strlen($password) < 6) {
        $err = 'Password must be at least 6 characters.';
    } else {
        $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM employers WHERE email = '" . mysqli_real_escape_string($conn, $email) . "'"));
        if ($check) {
            $err = 'Email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            if ($logo_file) {
                $stmt = mysqli_prepare($conn, "INSERT INTO employers (company_name, email, password, phone, website, location, industry, description, company_logo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, 'sssssssss', $company_name, $email, $hash, $phone, $website, $location, $industry, $description, $logo_file);
            } else {
                $stmt = mysqli_prepare($conn, "INSERT INTO employers (company_name, email, password, phone, website, location, industry, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, 'ssssssss', $company_name, $email, $hash, $phone, $website, $location, $industry, $description);
            }
            if (mysqli_stmt_execute($stmt)) {
                set_flash('success', 'Employer added successfully.');
                header('Location: ' . BASE_URL . 'admin/manage_employers.php');
                exit;
            } else {
                $err = 'Failed to add employer.';
            }
        }
    }
}

$page_title = 'Add Employer';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="container py-5">
    <h1 class="section-title">Add New Employer</h1>
    <p class="section-subtitle">Create a new employer account</p>

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
                        <input type="text" name="company_name" class="form-control" value="<?php echo htmlspecialchars($_POST['company_name'] ?? ''); ?>" required>
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
                        <label class="form-label">Website</label>
                        <input type="url" name="website" class="form-control" placeholder="https://" value="<?php echo htmlspecialchars($_POST['website'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Industry</label>
                        <input type="text" name="industry" class="form-control" value="<?php echo htmlspecialchars($_POST['industry'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Company Logo</label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                        <small class="text-muted">JPEG, PNG, GIF, WebP. Max 1MB.</small>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Add Employer</button>
                        <a href="manage_employers.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
