<?php
// Authentication helper
session_start();

define('SESSION_TIMEOUT', 3600); // 1 hour
define('ALLOWED_ROLES', ['admin', 'owner']);

function isLoggedIn() {
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        return false;
    }
    
    // Check session timeout
    if (isset($_SESSION['login_time'])) {
        if (time() - $_SESSION['login_time'] > SESSION_TIMEOUT) {
            session_destroy();
            return false;
        }
        // Update last activity
        $_SESSION['login_time'] = time();
    }
    
    return true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function getUsername() {
    return $_SESSION['user'] ?? 'Guest';
}

function getRole() {
    return $_SESSION['role'] ?? 'guest';
}

function isAdmin() {
    return getRole() === 'admin';
}

function isOwner() {
    return getRole() === 'owner';
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        die('Access Denied');
    }
}

function requireOwner() {
    requireLogin();
    if (!isOwner()) {
        die('Access Denied');
    }
}

function requireAdminOrOwner() {
    requireLogin();
    if (!isAdmin() && !isOwner()) {
        die('Access Denied');
    }
}
?>
