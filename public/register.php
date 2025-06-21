<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $password = $_POST['password'];
    $skills = sanitize($_POST['skills']);
    $location = sanitize($_POST['location']);
    $description = sanitize($_POST['description']);
    $profile_image = '';
    
    // Validate inputs
    if (empty($name)) $errors[] = "Name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($phone)) $errors[] = "Phone number is required";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters";
    if (empty($skills)) $errors[] = "Skills are required";
    if (empty($location)) $errors[] = "Location is required";
    
    // Handle file upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $profile_image = uploadImage($_FILES['profile_image']);
        if (!$profile_image) $errors[] = "Invalid image file";
    }
    
    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) $errors[] = "Email already registered";
    
    if (empty($errors)) {
        // Create user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $pdo->beginTransaction();
        
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $hashed_password]);
            $user_id = $pdo->lastInsertId();
            
            $stmt = $pdo->prepare("
                INSERT INTO worker_profiles 
                (user_id, skills, location, description, profile_image) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$user_id, $skills, $location, $description, $profile_image]);
            
            $pdo->commit();
            $success = true;
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Registration failed: " . $e->getMessage();
        }
    }
}
?>

<div class="form-container">
    <h2 class="section-title mb-4">Register as a Worker</h2>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            Registration successful! Your profile is pending admin approval.
            <a href="login.php">Login here</a>
        </div>
    <?php else: ?>
        <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" class="form-control form-control-lg" required>
                        <div class="invalid-feedback">Please enter your full name</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Phone Number (WhatsApp) *</label>
                        <input type="tel" name="phone" class="form-control form-control-lg" required>
                        <div class="invalid-feedback">Please enter your WhatsApp number</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Email Address *</label>
                        <input type="email" name="email" class="form-control form-control-lg" required>
                        <div class="invalid-feedback">Please enter a valid email</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Password *</label>
                        <div class="password-container">
                            <input type="password" name="password" class="form-control form-control-lg" minlength="6" required>
                            <span class="password-toggle">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <div class="invalid-feedback">Password must be at least 6 characters</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Location *</label>
                        <input type="text" name="location" class="form-control form-control-lg" required>
                        <div class="invalid-feedback">Please enter your location</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Skills (comma separated) *</label>
                        <input type="text" name="skills" class="form-control form-control-lg" placeholder="e.g. Plumbing, Pipe Fitting" required>
                        <div class="invalid-feedback">Please enter your skills</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">Profile Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Describe your experience and qualifications"></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Profile Photo</label>
                        <input class="form-control form-control-lg" type="file" name="profile_image" accept="image/*">
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="terms" required>
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a>
                        </label>
                        <div class="invalid-feedback">You must agree to the terms</div>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-lg px-5 py-3">Create Profile</button>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>