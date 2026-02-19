<?php
$current_page = basename($_SERVER['PHP_SELF']);
$is_logged_user = isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === ROLE_JOBSEEKER;
$is_logged_employer = isset($_SESSION['employer_id']) && isset($_SESSION['role']) && $_SESSION['role'] === ROLE_EMPLOYER;
$is_logged_admin = isset($_SESSION['admin_id']) && isset($_SESSION['role']) && $_SESSION['role'] === ROLE_ADMIN;
?>
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>index.php">
            <i class="bi bi-briefcase-fill me-2"></i><?php echo SITE_NAME; ?>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>jobs.php">Jobs</a></li>
                <li class="nav-item"><a class="nav-link <?php echo $current_page === 'about.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>about.php">About</a></li>
                <li class="nav-item"><a class="nav-link <?php echo $current_page === 'contact.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>contact.php">Contact</a></li>
                <?php if ($is_logged_user): ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>jobseeker/dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>jobseeker/search_jobs.php">Search Jobs</a></li>
                <?php endif; ?>
                <?php if ($is_logged_employer): ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>employer/dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>employer/post_job.php">Post Job</a></li>
                <?php endif; ?>
                <?php if ($is_logged_admin): ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>admin/dashboard.php">Admin</a></li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if ($is_logged_user || $is_logged_employer || $is_logged_admin): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <?php
                            if ($is_logged_user) echo htmlspecialchars($_SESSION['user_name'] ?? 'User');
                            elseif ($is_logged_employer) echo htmlspecialchars($_SESSION['company_name'] ?? 'Employer');
                            else echo 'Admin';
                            ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if ($is_logged_user): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>jobseeker/profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>jobseeker/applied_jobs.php"><i class="bi bi-file-earmark me-2"></i>Applied Jobs</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>jobseeker/saved_jobs.php"><i class="bi bi-bookmark me-2"></i>Saved Jobs</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>jobseeker/job_alerts.php"><i class="bi bi-bell me-2"></i>Job Alerts</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>jobseeker/change_password.php"><i class="bi bi-key me-2"></i>Change Password</a></li>
                            <?php endif; ?>
                            <?php if ($is_logged_employer): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>employer/manage_jobs.php"><i class="bi bi-list-ul me-2"></i>Manage Jobs</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>employer/change_password.php"><i class="bi bi-key me-2"></i>Change Password</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>auth/login.php">Login</a></li>
                    <li class="nav-item"><a class="btn btn-outline-light btn-sm ms-2" href="<?php echo BASE_URL; ?>auth/register.php">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
