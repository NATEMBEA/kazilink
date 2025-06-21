<?php
require_once __DIR__ . '/../includes/header.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];
        
        if ($user['role'] === 'admin') {
            header('Location: ../dashboard.php');
        } else {
            header('Location: profile.php');
        }
        exit;
    } else {
        $error = "Invalid email or password";
    }
}
?>

<div class="form-container">
    <h2 class="section-title mb-4">Login to Your Account</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form action="/login.php" method="POST" class="needs-validation" novalidate></form>
    
    <form method="POST" class="needs-validation" novalidate>
        <div class="mb-4">
            <label class="form-label">Email Address *</label>
            <input type="email" name="email" class="form-control form-control-lg" required>
            <div class="invalid-feedback">Please enter your email</div>
        </div>
        
        <div class="mb-4">
            <label class="form-label">Password *</label>
            <div class="password-container">
                <input type="password" name="password" class="form-control form-control-lg" required>
                <span class="password-toggle">
                    <i class="fas fa-eye"></i>
                </span>
            </div>
            <div class="invalid-feedback">Please enter your password</div>
            <div class="d-flex justify-content-end mt-2">
                <a href="reset_password.php" class="text-decoration-none">Forgot password?</a>
            </div>
        </div>
        
        <div class="mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
        </div>
        
        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg">Sign In</button>
        </div>
    </form>
    
    <div class="auth-switch mt-4">
        Don't have an account? <a href="register.php">Register Now</a>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>