<?php
require_once __DIR__ . '/../config/db.php';

$error = '';
$redirect = $_GET['redirect'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf_post();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'jobseeker';

    if (empty($email) || empty($password)) {
        $error = 'Please fill all fields.';
    } else {
        if ($role === 'admin') {
            $stmt = mysqli_prepare($conn, "SELECT id, username, password FROM admin WHERE username = ?");
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($res);
            if ($row && password_verify($password, $row['password'])) {
                $_SESSION['admin_id'] = $row['id'];
                $_SESSION['role'] = ROLE_ADMIN;
                header('Location: ' . BASE_URL . 'admin/dashboard.php');
                exit;
            }
        } elseif ($role === 'employer') {
            $stmt = mysqli_prepare($conn, "SELECT id, company_name, email, password FROM employers WHERE email = ?");
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($res);
            if ($row && password_verify($password, $row['password'])) {
                $_SESSION['employer_id'] = $row['id'];
                $_SESSION['company_name'] = $row['company_name'];
                $_SESSION['role'] = ROLE_EMPLOYER;
                header('Location: ' . ($redirect ?: BASE_URL . 'employer/dashboard.php'));
                exit;
            }
        } else {
            $stmt = mysqli_prepare($conn, "SELECT id, full_name, email, password FROM users WHERE email = ?");
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($res);
            if ($row && password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['full_name'];
                $_SESSION['role'] = ROLE_JOBSEEKER;
                header('Location: ' . ($redirect ?: BASE_URL . 'jobseeker/dashboard.php'));
                exit;
            }
        }
        $error = 'Invalid email or password.';
    }
}

$page_title = 'Login';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="auth-wrapper">
    <div class="container">
        <div class="auth-card mx-auto">
            <div class="auth-header">
                <h1><i class="bi bi-box-arrow-in-right me-2"></i>Login</h1>
                <p class="mb-0 mt-2 opacity-75 small">Sign in to your account</p>
            </div>
            <div class="auth-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="post" action="">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label">Login as</label>
                        <select name="role" class="form-select" required>
                            <option value="jobseeker" <?php echo ($_POST['role'] ?? '') === 'jobseeker' ? 'selected' : ''; ?>>Job Seeker</option>
                            <option value="employer" <?php echo ($_POST['role'] ?? '') === 'employer' ? 'selected' : ''; ?>>Employer</option>
                            <option value="admin" <?php echo ($_POST['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email / Username</label>
                        <input type="text" name="email" class="form-control" placeholder="Email or username" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
                    <button type="submit" class="btn btn-primary w-100 py-2">Login</button>
                </form>
                <p class="text-center mt-3 mb-0 small text-muted">Don't have an account? <a href="<?php echo BASE_URL; ?>auth/register.php">Sign Up</a></p>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
