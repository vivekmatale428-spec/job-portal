<?php
require_once __DIR__ . '/../config/db.php';

$error = '';
$success = '';
$role = $_GET['role'] ?? 'jobseeker';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf_post();
    $role = $_POST['role'] ?? 'jobseeker';
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $cpassword = $_POST['cpassword'] ?? '';

    if (empty($email) || empty($password) || empty($cpassword)) {
        $error = 'Please fill all fields.';
    } elseif ($password !== $cpassword) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        if ($role === 'employer') {
            $company_name = trim($_POST['company_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            if (empty($company_name)) {
                $error = 'Company name is required.';
            } else {
                $stmt = mysqli_prepare($conn, "INSERT INTO employers (company_name, email, password, phone) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, 'ssss', $company_name, $email, $hash, $phone);
                if (mysqli_stmt_execute($stmt)) {
                    $success = 'Registration successful! You can now <a href="login.php">login</a>.';
                } else {
                    $error = mysqli_errno($conn) === 1062 ? 'Email already registered.' : 'Registration failed.';
                }
            }
        } else {
            $full_name = trim($_POST['full_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            if (empty($full_name)) {
                $error = 'Full name is required.';
            } else {
                $stmt = mysqli_prepare($conn, "INSERT INTO users (full_name, email, password, phone) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, 'ssss', $full_name, $email, $hash, $phone);
                if (mysqli_stmt_execute($stmt)) {
                    $success = 'Registration successful! You can now <a href="login.php">login</a>.';
                } else {
                    $error = mysqli_errno($conn) === 1062 ? 'Email already registered.' : 'Registration failed.';
                }
            }
        }
    }
}

$page_title = 'Sign Up';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="auth-wrapper">
    <div class="container">
        <div class="auth-card mx-auto">
            <div class="auth-header">
                <h1><i class="bi bi-person-plus me-2"></i>Sign Up</h1>
                <p class="mb-0 mt-2 opacity-75 small">Create your account</p>
            </div>
            <div class="auth-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php else: ?>
                <form method="post" action="">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label">Register as</label>
                        <select name="role" class="form-select" id="regRole">
                            <option value="jobseeker" <?php echo $role === 'jobseeker' ? 'selected' : ''; ?>>Job Seeker</option>
                            <option value="employer" <?php echo $role === 'employer' ? 'selected' : ''; ?>>Employer</option>
                        </select>
                    </div>
                    <div class="mb-3" id="fieldFullName">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" placeholder="Your full name" value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                    </div>
                    <div class="mb-3 d-none" id="fieldCompanyName">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="company_name" class="form-control" placeholder="Company name" value="<?php echo htmlspecialchars($_POST['company_name'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" placeholder="Phone (optional)" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Min 6 characters" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="cpassword" class="form-control" placeholder="Confirm password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2">Create Account</button>
                </form>
                <?php endif; ?>
                <p class="text-center mt-3 mb-0 small text-muted">Already have an account? <a href="<?php echo BASE_URL; ?>auth/login.php">Login</a></p>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('regRole').addEventListener('change', function() {
    var v = this.value;
    document.getElementById('fieldFullName').classList.toggle('d-none', v === 'employer');
    document.getElementById('fieldCompanyName').classList.toggle('d-none', v !== 'employer');
});
document.getElementById('regRole').dispatchEvent(new Event('change'));
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
