<?php
/**
 * CSRF Protection Helper
 */
if (!function_exists('generate_csrf_token')) {
    function generate_csrf_token() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    function verify_csrf_token($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    function csrf_field() {
        return '<input type="hidden" name="csrf_token" value="' . generate_csrf_token() . '">';
    }

    function require_csrf_post() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            http_response_code(400);
            die('Invalid CSRF token. Please refresh the page and try again.');
        }
    }
}
