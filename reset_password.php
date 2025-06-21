<?php
// Database configuration (replace with your actual credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wakazilink";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Forgot password request
    if (isset($_POST['forgot_password'])) {
        $email = $_POST['email'];
        
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Generate reset token (valid for 1 hour)
            $token = bin2hex(random_bytes(32));
            $expires = date("Y-m-d H:i:s", time() + 3600);
            
            // Store token in database
            $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $token, $expires);
            $stmt->execute();
            
            // Send email with reset link
            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?token=" . $token;
            $subject = "Password Reset Request";
            $message = "Hello,\n\nYou requested a password reset. Click the link below to reset your password:\n\n" . 
                       $resetLink . "\n\nThis link will expire in 1 hour.\n\nIf you didn't request this, please ignore this email.";
            $headers = "From: no-reply@yourdomain.com";
            
            // In a real application, you would use mail() or a library like PHPMailer
            // For this demo, we'll just display the link
            $emailSent = true;
            $demoResetLink = $resetLink;
        } else {
            $error = "Email not found in our system";
        }
    }
    
    // Password reset
    if (isset($_POST['reset_password'])) {
        $token = $_POST['token'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        if ($newPassword !== $confirmPassword) {
            $resetError = "Passwords do not match";
        } else {
            // Verify token
            $currentTime = date("Y-m-d H:i:s");
            $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires > ?");
            $stmt->bind_param("ss", $token, $currentTime);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $email = $row['email'];
                
                // Update password
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
                $stmt->bind_param("ss", $hashedPassword, $email);
                $stmt->execute();
                
                // Delete token
                $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
                $stmt->bind_param("s", $token);
                $stmt->execute();
                
                $resetSuccess = true;
            } else {
                $resetError = "Invalid or expired token";
            }
        }
    }
}

// Check for token in URL
$token = isset($_GET['token']) ? $_GET['token'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Recovery System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border: none;
        }
        .card-header {
            background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }
        .btn-primary {
            background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #3a5ca0 0%, #121f3d 100%);
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }
        .step-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 4px;
            background-color: #e0e0e0;
            z-index: 1;
        }
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 2;
        }
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            font-weight: bold;
            color: #777;
        }
        .step.active .step-circle {
            background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%);
            color: white;
        }
        .step-label {
            font-size: 14px;
            text-align: center;
            color: #777;
        }
        .step.active .step-label {
            color: #182848;
            font-weight: bold;
        }
        .reset-container {
            max-width: 500px;
            width: 100%;
            margin: 0 auto;
        }
        .demo-note {
            background-color: #f8f9fa;
            border-left: 4px solid #4b6cb7;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container reset-container">
        <div class="card">
            <div class="card-header text-center py-3">
                <h2>Password Recovery</h2>
            </div>
            <div class="card-body p-4">
                <div class="step-indicator">
                    <div class="step <?= $token ? '' : 'active' ?>">
                        <div class="step-circle">1</div>
                        <div class="step-label">Request Reset</div>
                    </div>
                    <div class="step <?= $token ? 'active' : '' ?>">
                        <div class="step-circle">2</div>
                        <div class="step-label">Reset Password</div>
                    </div>
                    <div class="step">
                        <div class="step-circle">3</div>
                        <div class="step-label">Complete</div>
                    </div>
                </div>
                
                <?php if ($token || isset($resetSuccess)): ?>
                    <!-- Password Reset Form -->
                    <h3 class="text-center mb-4">Reset Your Password</h3>
                    
                    <?php if (isset($resetSuccess)): ?>
                        <div class="alert alert-success text-center">
                            <h4><i class="bi bi-check-circle-fill"></i> Password Updated!</h4>
                            <p>Your password has been successfully reset.</p>
                            <a href="login.php" class="btn btn-primary mt-2">Log In Now</a>
                        </div>
                    <?php else: ?>
                        <?php if (isset($resetError)): ?>
                            <div class="alert alert-danger"><?= $resetError ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <input type="hidden" name="token" value="<?= $token ?>">
                            
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                                <div class="form-text">Minimum 8 characters with letters and numbers</div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            
                            <button type="submit" name="reset_password" class="btn btn-primary w-100 py-2">Reset Password</button>
                        </form>
                    <?php endif; ?>
                    
                <?php elseif (isset($emailSent)): ?>
                    <!-- Email Sent Confirmation -->
                    <div class="text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#4b6cb7" class="bi bi-envelope-check mb-3" viewBox="0 0 16 16">
                            <path d="M2 2a2 2 0 0 0-2 2v8.01A2 2 0 0 0 2 14h5.5a.5.5 0 0 0 0-1H2a1 1 0 0 1-.966-.741l5.64-3.471L8 9.583l7-4.2V8.5a.5.5 0 0 0 1 0V4a2 2 0 0 0-2-2zm3.708 6.208L1 11.105V5.383zM1 4.217V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v.217l-7 4.2z"/>
                            <path d="M16 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0m-1.993-1.679a.5.5 0 0 0-.686.172l-1.17 1.95-.547-.547a.5.5 0 0 0-.708.708l.774.773a.75.75 0 0 0 1.174-.144l1.335-2.226a.5.5 0 0 0-.172-.686"/>
                        </svg>
                        <h3>Reset Email Sent!</h3>
                        <p>We've sent instructions to reset your password to your email address.</p>
                        <p class="text-muted">Please check your inbox and follow the instructions in the email.</p>
                        
                        <div class="demo-note">
                            <strong>Demo Note:</strong> In a production environment, this would be a real email. 
                            For this demo, here's your reset link: 
                            <a href="<?= $demoResetLink ?>"><?= $demoResetLink ?></a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Forgot Password Form -->
                    <h3 class="text-center mb-4">Forgot Your Password?</h3>
                    <p class="text-center mb-4">Enter your email and we'll send you a link to reset your password.</p>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email">
                        </div>
                        
                        <button type="submit" name="forgot_password" class="btn btn-primary w-100 py-2">Send Reset Link</button>
                        
                        <div class="text-center mt-3">
                            <a href="login.php" class="text-decoration-none">Back to Login</a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>