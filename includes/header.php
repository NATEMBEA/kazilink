<?php
// ADD THESE LINES AT THE TOP
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';
$base_path = '/wakazilink/'; // Change to your subdirectory
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . $base_path);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Ensure session is started
}
if (!function_exists('isAdmin')) {
    function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
}
// Define BASE_URL if not already defined
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Ensure session is started
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WakaziLink - Connect with Skilled Workers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-hands-helping me-2"></i>WakaziLink
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="search.php">Find Workers</a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">My Profile</a>
                        </li>
                        <?php if (isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/dashboard.php">Admin</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                <div class="d-flex">
                    <?php if (isLoggedIn()): ?>
                        <span class="navbar-text me-3">Welcome, <?= $_SESSION['name'] ?></span>
                        <a href="logout.php" class="btn btn-outline-light">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-light me-2">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </a>
                        <a href="register.php" class="btn btn-primary">
                            <i class="fas fa-user-plus me-1"></i> Sign Up
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <div class="container my-4"></div>