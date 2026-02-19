<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$job_id = (int)($_GET['id'] ?? 0);
if (!$job_id) {
    header('Location: ' . BASE_URL . 'jobseeker/search_jobs.php');
    exit;
}

$uid = (int)$_SESSION['user_id'];
$exists = (int) mysqli_fetch_column(mysqli_query($conn, "SELECT COUNT(*) FROM jobs WHERE id = $job_id AND status = 'approved'"));
if (!$exists) {
    header('Location: ' . BASE_URL . 'jobs.php');
    exit;
}

mysqli_query($conn, "INSERT IGNORE INTO saved_jobs (user_id, job_id) VALUES ($uid, $job_id)");

$redirect = $_GET['redirect'] ?? BASE_URL . 'job_detail.php?id=' . $job_id;
header('Location: ' . $redirect);
exit;
