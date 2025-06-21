<?php
// public/logout.php

// Start session and include necessary files
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../includes/functions.php';

// Initialize variables
$message = '';
$countdown = 5; // Seconds to redirect after logout

// Handle logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['confirm'])) {
    // Destroy the session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Unset all session variables
    $_SESSION = array();
    
    // Delete session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
    
    $message = "You have been successfully logged out. Redirecting in $countdown seconds...";
    
    // Redirect after countdown
    header("Refresh: $countdown; url=index.php");
} else if (isLoggedIn()) {
    // Show logout confirmation
    $message = "Are you sure you want to log out?";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - WakaziLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2c3e50;
            --accent: #f39c12;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e7f1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .logout-container {
            max-width: 500px;
            width: 100%;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .logout-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 40px 20px;
        }
        
        .logout-body {
            padding: 40px;
        }
        
        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            color: var(--secondary);
            font-weight: 700;
            font-size: 28px;
        }
        
        .logo-icon {
            background: var(--primary);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        
        .btn-lg {
            padding: 12px 30px;
            font-size: 18px;
            border-radius: 12px;
        }
        
        .countdown {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary);
            margin-top: 20px;
        }
        
        .logout-icon {
            font-size: 5rem;
            color: var(--primary);
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .progress {
            height: 10px;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="logout-header">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-hands-helping"></i>
                </div>
                WakaziLink
            </div>
            <h2>Account Logout</h2>
        </div>
        
        <div class="logout-body">
            <?php if (!empty($message)): ?>
                <div class="logout-icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                
                <h3 class="mb-4"><?= $message ?></h3>
                
                <?php if (isset($_GET['confirm']) || $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                    <!-- Logout in progress -->
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" style="width: 0%" id="logout-progress"></div>
                    </div>
                    
                    <div class="countdown" id="countdown">
                        Redirecting in <?= $countdown ?> seconds...
                    </div>
                    
                    <script>
                        // Progress bar animation
                        let seconds = <?= $countdown ?>;
                        const progressBar = document.getElementById('logout-progress');
                        const countdownEl = document.getElementById('countdown');
                        
                        const interval = setInterval(() => {
                            seconds--;
                            const progress = 100 - (seconds / <?= $countdown ?> * 100);
                            progressBar.style.width = progress + '%';
                            countdownEl.textContent = `Redirecting in ${seconds} seconds...`;
                            
                            if (seconds <= 0) {
                                clearInterval(interval);
                                window.location.href = 'index.php';
                            }
                        }, 1000);
                    </script>
                <?php else: ?>
                    <!-- Logout confirmation -->
                    <form method="POST" class="mt-4">
                        <button type="submit" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-sign-out-alt me-2"></i> Yes, Logout
                        </button>
                        <a href="index.php" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i> Cancel
                        </a>
                    </form>
                <?php endif; ?>
                
            <?php else: ?>
                <!-- Not logged in message -->
                <div class="logout-icon text-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="mb-3">You are not logged in</h3>
                <p class="mb-4">There's no active session to log out from.</p>
                <div class="d-grid gap-2">
                    <a href="login.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i> Login Now
                    </a>
                    <a href="index.php" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-home me-2"></i> Return Home
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>