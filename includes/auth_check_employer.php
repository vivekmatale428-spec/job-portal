<?php
/**
 * Require employer to be logged in
 */
if (!isset($_SESSION['employer_id']) || $_SESSION['role'] !== ROLE_EMPLOYER) {
    header('Location: ' . BASE_URL . 'auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}
