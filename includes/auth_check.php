<?php
/**
 * Require jobseeker to be logged in
 */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== ROLE_JOBSEEKER) {
    header('Location: ' . BASE_URL . 'auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}
