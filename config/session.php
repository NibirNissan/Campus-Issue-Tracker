<?php
/**
 * Session Configuration
 * Campus Issue Tracker
 */

require_once __DIR__ . '/app.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Get base URL path.
 */
function base_url($path = '') {
    return BASE_URL . $path;
}

/**
 * Check if user is logged in.
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if logged-in user is admin.
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Check if logged-in user is student.
 */
function isStudent() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'student';
}

/**
 * Redirect if not logged in.
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: " . base_url("/login.php"));
        exit();
    }
}

/**
 * Redirect if not admin.
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: " . base_url("/index.php"));
        exit();
    }
}

/**
 * Redirect if not student.
 */
function requireStudent() {
    requireLogin();
    if (!isStudent()) {
        header("Location: " . base_url("/index.php"));
        exit();
    }
}

/**
 * Set flash message.
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message.
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
