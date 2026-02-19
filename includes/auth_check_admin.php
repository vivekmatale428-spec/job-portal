<?php
/**
 * Require admin to be logged in
 */
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== ROLE_ADMIN) {
    header('Location: ' . BASE_URL . 'auth/login.php');
    exit;
}
