<?php
// Add this at the top
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/');


// Rest of your auth functions...


if (!defined('ADMIN_PATH')) {
    require_once __DIR__ . '/db_connect.php';
}

session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../public/login.php');
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ../public/index.php');
        exit;
    }
}
?>