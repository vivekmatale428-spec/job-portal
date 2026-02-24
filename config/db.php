<?php
/**
 * Database connection - Job Portal Finder
 */
session_start();
require_once __DIR__ . '/constants.php';

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '123');
define('DB_NAME', 'job_portal');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
