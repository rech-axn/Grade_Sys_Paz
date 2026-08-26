<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function current_user() {
    if (!is_logged_in()) {
        return null;
    }
    return [
        'id'        => $_SESSION['user_id'],
        'username'  => $_SESSION['username'] ?? '',
        'role'      => $_SESSION['role'] ?? '',
        'full_name' => $_SESSION['full_name'] ?? 'User',
        'email'     => $_SESSION['email'] ?? '',
        'student_id'=> $_SESSION['student_id'] ?? null, // PK in students table
    ];
}

function require_login() {
    if (!is_logged_in()) {
        header("Location: " . getBaseUrl() . "/index.php?error=unauthorized");
        exit();
    }
}

function require_teacher() {
    require_login();
    if ($_SESSION['role'] !== 'teacher') {
        header("Location: " . getBaseUrl() . "/student/dashboard.php?error=forbidden");
        exit();
    }
}

function require_student() {
    require_login();
    if ($_SESSION['role'] !== 'student') {
        header("Location: " . getBaseUrl() . "/teacher/dashboard.php?error=forbidden");
        exit();
    }
}

function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'danger', 'info', 'warning'
        'message' => $message
    ];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
            die("CSRF verification failed. Please refresh the page.");
        }
    }
}
