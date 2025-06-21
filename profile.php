<?php
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireLogin();

// Get user data
$stmt = $pdo->prepare("
    SELECT users.*, worker_profiles.* 
    FROM users 
    LEFT JOIN worker_profiles ON users.id = worker_profiles.user_id 
    WHERE users.id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: login.php');
    exit;
}

// Handle profile update
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_type = $_POST['form_type'] ?? 'profile';
    
    if ($form_type === 'profile') {
        // Profile update form
        $name = sanitize($_POST['name']);
        $phone = sanitize($_POST['phone']);
        $skills = sanitize($_POST['skills']);
        $location = sanitize($_POST['location']);
        $description = sanitize($_POST['description']);
        $profile_image = $user['profile_image'];
        
        // Validate inputs
        if (empty($name)) $errors[] = "Name is required";
        if (empty($phone)) $errors[] = "Phone number is required";
        if (empty($skills)) $errors[] = "Skills are required";
        if (empty($location)) $errors[] = "Location is required";
        
        // Handle file upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $new_image = uploadImage($_FILES['profile_image']);
            if ($new_image) {
                // Delete old image if exists
                if ($profile_image && file_exists("../public/$profile_image")) {
                    unlink("../public/$profile_image");
                }
                $profile_image = $new_image;
            } else {
                $errors[] = "Invalid image file. Only JPG, PNG, and GIF are allowed.";
            }
        }
        
        if (empty($errors)) {
            try {
                $pdo->beginTransaction();
                
                // Update users table
                $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ?");
                $stmt->execute([$name, $phone, $_SESSION['user_id']]);
                
                // Update worker_profiles table
                $stmt = $pdo->prepare("
                    UPDATE worker_profiles 
                    SET skills = ?, location = ?, description = ?, profile_image = ?
                    WHERE user_id = ?
                ");
                $stmt->execute([$skills, $location, $description, $profile_image, $_SESSION['user_id']]);
                
                $pdo->commit();
                $success = true;
                $success_message = "Profile updated successfully!";
                
                // Update session
                $_SESSION['name'] = $name;
                
                // Refresh user data
                $stmt = $pdo->prepare("
                    SELECT users.*, worker_profiles.* 
                    FROM users 
                    LEFT JOIN worker_profiles ON users.id = worker_profiles.user_id 
                    WHERE users.id = ?
                ");
                $stmt->execute([$_SESSION['user_id']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $errors[] = "Update failed: " . $e->getMessage();
            }
        }
    } elseif ($form_type === 'password') {
        // Password update form
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validate password inputs
        if (empty($current_password)) $errors[] = "Current password is required";
        if (empty($new_password)) $errors[] = "New password is required";
        if ($new_password !== $confirm_password) $errors[] = "Passwords do not match";
        if (strlen($new_password) < 8) $errors[] = "Password must be at least 8 characters";
        
        if (empty($errors)) {
            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $db_password = $stmt->fetchColumn();
            
            if ($db_password && password_verify($current_password, $db_password)) {
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $_SESSION['user_id']]);
                
                $success = true;
                $success_message = "Password updated successfully!";
            } else {
                $errors[] = "Current password is incorrect";
            }
        }
    }
}

require_once '../includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-body text-center p-4">
                    <div class="position-relative mx-auto" style="width: 150px;">
                        <?php if ($user['profile_image']): ?>
                            <img src="<?= $user['profile_image'] ?>" class="rounded-circle img-thumbnail border-3 border-primary" 
                                 alt="<?= $user['name'] ?>" style="width: 150px; height: 150px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-light text-dark rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 150px; height: 150px; border: 3px solid #3498db;">
                                <i class="fas fa-user fa-3x"></i>
                            </div>
                        <?php endif; ?>
                        <div class="position-absolute bottom-0 end-0 bg-primary rounded-circle p-2">
                            <i class="fas fa-camera text-white"></i>
                        </div>
                    </div>
                    
                    <h3 class="mt-4 mb-1"><?= $user['name'] ?></h3>
                    <p class="text-muted mb-3">
                        <i class="fas fa-map-marker-alt me-1 text-primary"></i> 
                        <?= $user['location'] ?? 'Not specified' ?>
                    </p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <span class="badge bg-<?= $user['is_approved'] ? 'success' : 'warning' ?> rounded-pill px-3 py-2">
                            <i class="fas fa-<?= $user['is_approved'] ? 'check-circle' : 'clock' ?> me-1"></i> 
                            <?= $user['is_approved'] ? 'Verified' : 'Pending Approval' ?>
                        </span>
                        <span class="badge bg-info rounded-pill px-3 py-2">
                            <i class="fas fa-user me-1"></i> 
                            <?= ucfirst($user['role']) ?>
                        </span>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="worker.php?id=<?= $user['id'] ?>" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-1"></i> View Public Profile
                        </a>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="text-start">
                        <h5 class="mb-3"><i class="fas fa-chart-line me-2 text-primary"></i> Profile Strength</h5>
                        <?php 
                        $completion = 0;
                        if (!empty($user['profile_image'])) $completion += 20;
                        if (!empty($user['location'])) $completion += 20;
                        if (!empty($user['skills'])) $completion += 20;
                        if (!empty($user['description'])) $completion += 20;
                        if (!empty($user['phone'])) $completion += 20;
                        ?>
                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: <?= $completion ?>%;" 
                                 aria-valuenow="<?= $completion ?>" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100"></div>
                        </div>
                        <p class="mb-0 text-muted"><?= $completion ?>% Complete</p>
                        
                        <?php if ($completion < 100): ?>
                            <div class="mt-3">
                                <h6 class="mb-2"><i class="fas fa-lightbulb me-2 text-warning"></i> Recommendations:</h6>
                                <ul class="list-unstyled">
                                    <?php if (empty($user['profile_image'])): ?>
                                        <li><i class="fas fa-camera me-2"></i> Add a profile photo</li>
                                    <?php endif; ?>
                                    <?php if (empty($user['description'])): ?>
                                        <li><i class="fas fa-file-alt me-2"></i> Add a profile description</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom">
                    <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" 
                                    data-bs-target="#profile" type="button" role="tab">
                                <i class="fas fa-user-circle me-2"></i> Profile
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="password-tab" data-bs-toggle="tab" 
                                    data-bs-target="#password" type="button" role="tab">
                                <i class="fas fa-lock me-2"></i> Security
                            </button>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body p-4">
                    <div class="tab-content" id="profileTabsContent">
                        <!-- Profile Tab -->
                        <div class="tab-pane fade show active" id="profile" role="tabpanel">
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?= $error ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($success && ($form_type === 'profile')): ?>
                                <div class="alert alert-success">
                                    <?= $success_message ?>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" enctype="multipart/form-data" id="profileForm">
                                <input type="hidden" name="form_type" value="profile">
                                
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-floating mb-4">
                                            <input type="text" name="name" class="form-control" 
                                                   id="nameInput" placeholder="Full Name" 
                                                   value="<?= $user['name'] ?>" required>
                                            <label for="nameInput">Full Name *</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-4">
                                            <input type="tel" name="phone" class="form-control" 
                                                   id="phoneInput" placeholder="Phone Number" 
                                                   value="<?= $user['phone'] ?>" required>
                                            <label for="phoneInput">Phone (WhatsApp) *</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-4">
                                            <input type="email" class="form-control" 
                                                   id="emailInput" placeholder="Email" 
                                                   value="<?= $user['email'] ?>" readonly>
                                            <label for="emailInput">Email Address</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-4">
                                            <input type="text" class="form-control" 
                                                   id="roleInput" placeholder="Account Type" 
                                                   value="<?= ucfirst($user['role']) ?>" readonly>
                                            <label for="roleInput">Account Type</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-4">
                                            <input type="text" name="location" class="form-control" 
                                                   id="locationInput" placeholder="Location" 
                                                   value="<?= $user['location'] ?>" required>
                                            <label for="locationInput">Location *</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-4">
                                            <input type="text" name="skills" class="form-control" 
                                                   id="skillsInput" placeholder="Skills" 
                                                   value="<?= $user['skills'] ?>" required>
                                            <label for="skillsInput">Skills (comma separated) *</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-4">
                                            <label class="form-label fw-medium">Profile Description</label>
                                            <textarea name="description" class="form-control" 
                                                      rows="4" placeholder="Describe your experience and qualifications"><?= $user['description'] ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label class="form-label fw-medium">Profile Photo</label>
                                            <div class="d-flex align-items-start gap-4">
                                                <div class="flex-shrink-0">
                                                    <div id="imagePreview" class="rounded-circle border" 
                                                         style="width: 100px; height: 100px; overflow: hidden; background-color: #f8f9fa;">
                                                        <?php if ($user['profile_image']): ?>
                                                            <img src="<?= $user['profile_image'] ?>" alt="Current Photo" 
                                                                 class="w-100 h-100 object-fit-cover">
                                                        <?php else: ?>
                                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                                                <i class="fas fa-user fa-2x"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <input class="form-control" type="file" name="profile_image" 
                                                           id="imageUpload" accept="image/*">
                                                    <div class="form-text mt-2">Max file size: 2MB (JPG, PNG, GIF)</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label class="form-label fw-medium">Profile Status</label>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="bg-<?= $user['is_approved'] ? 'success' : 'warning' ?> rounded-circle" 
                                                     style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-<?= $user['is_approved'] ? 'check' : 'clock' ?> text-white" style="font-size: 12px;"></i>
                                                </div>
                                                <span class="fw-medium"><?= $user['is_approved'] ? 'Verified' : 'Pending Approval' ?></span>
                                            </div>
                                            <?php if (!$user['is_approved']): ?>
                                                <div class="alert alert-warning mt-2 mb-0">
                                                    <i class="fas fa-info-circle me-2"></i> Your profile is under review. It will be visible once approved.
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-end gap-3">
                                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                                <i class="fas fa-save me-2"></i> Update Profile
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Password Tab -->
                        <div class="tab-pane fade" id="password" role="tabpanel">
                            <?php if ($success && ($form_type === 'password')): ?>
                                <div class="alert alert-success">
                                    <?= $success_message ?>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" id="passwordForm">
                                <input type="hidden" name="form_type" value="password">
                                
                                <div class="row g-4">
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i> 
                                            Use a strong password with at least 8 characters including numbers and symbols.
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-4">
                                            <input type="password" name="current_password" class="form-control" 
                                                   id="currentPassword" placeholder="Current Password" required>
                                            <label for="currentPassword">Current Password *</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-4">
                                            <input type="password" name="new_password" class="form-control" 
                                                   id="newPassword" placeholder="New Password" required>
                                            <label for="newPassword">New Password *</label>
                                            <div class="password-strength mt-2">
                                                <div class="progress" style="height: 5px;">
                                                    <div class="progress-bar" id="passwordStrengthBar" role="progressbar" style="width: 0%"></div>
                                                </div>
                                                <small id="passwordStrengthText" class="text-muted">Password strength</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-4">
                                            <input type="password" name="confirm_password" class="form-control" 
                                                   id="confirmPassword" placeholder="Confirm Password" required>
                                            <label for="confirmPassword">Confirm Password *</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label class="form-label fw-medium">Password Requirements</label>
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item d-flex align-items-center px-0 py-1">
                                                    <span id="lengthCheck" class="text-danger me-2"><i class="fas fa-times-circle"></i></span>
                                                    At least 8 characters
                                                </li>
                                                <li class="list-group-item d-flex align-items-center px-0 py-1">
                                                    <span id="numberCheck" class="text-danger me-2"><i class="fas fa-times-circle"></i></span>
                                                    Contains a number
                                                </li>
                                                <li class="list-group-item d-flex align-items-center px-0 py-1">
                                                    <span id="specialCheck" class="text-danger me-2"><i class="fas fa-times-circle"></i></span>
                                                    Contains a special character
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-end gap-3">
                                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                                <i class="fas fa-lock me-2"></i> Change Password
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

<style>
    .card {
        border-radius: 16px;
        overflow: hidden;
        border: none;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        padding: 15px 20px;
        font-weight: 600;
        color: #6c757d;
        transition: all 0.3s;
    }
    
    .nav-tabs .nav-link.active {
        color: #3498db;
        border-bottom: 3px solid #3498db;
        background: transparent;
    }
    
    .form-floating > label {
        padding: 1rem 1.25rem;
        color: #6c757d;
    }
    
    .form-control, .form-control:focus {
        border: 1px solid #e1e5eb;
        border-radius: 12px;
        padding: 16px 20px;
        height: auto;
        box-shadow: none;
    }
    
    .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
    }
    
    .progress-bar {
        border-radius: 10px;
    }
    
    .badge {
        font-weight: 500;
        padding: 8px 12px;
    }
    
    .form-container {
        background: white;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        padding: 30px;
        margin: 30px 0;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Image preview functionality
        const imageUpload = document.getElementById('imageUpload');
        const imagePreview = document.getElementById('imagePreview');
        
        if (imageUpload) {
            imageUpload.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.innerHTML = `<img src="${e.target.result}" class="w-100 h-100 object-fit-cover">`;
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
        
        // Password strength checker
        const passwordInput = document.getElementById('newPassword');
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                const strengthBar = document.getElementById('passwordStrengthBar');
                const strengthText = document.getElementById('passwordStrengthText');
                const lengthCheck = document.getElementById('lengthCheck');
                const numberCheck = document.getElementById('numberCheck');
                const specialCheck = document.getElementById('specialCheck');
                
                // Reset checks
                lengthCheck.innerHTML = '<i class="fas fa-times-circle"></i>';
                lengthCheck.className = 'text-danger me-2';
                numberCheck.innerHTML = '<i class="fas fa-times-circle"></i>';
                numberCheck.className = 'text-danger me-2';
                specialCheck.innerHTML = '<i class="fas fa-times-circle"></i>';
                specialCheck.className = 'text-danger me-2';
                
                let strength = 0;
                
                // Check length
                if (password.length >= 8) {
                    strength += 25;
                    lengthCheck.innerHTML = '<i class="fas fa-check-circle"></i>';
                    lengthCheck.className = 'text-success me-2';
                }
                
                // Check for numbers
                if (/\d/.test(password)) {
                    strength += 25;
                    numberCheck.innerHTML = '<i class="fas fa-check-circle"></i>';
                    numberCheck.className = 'text-success me-2';
                }
                
                // Check for special characters
                if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                    strength += 25;
                    specialCheck.innerHTML = '<i class="fas fa-check-circle"></i>';
                    specialCheck.className = 'text-success me-2';
                }
                
                // Check for uppercase and lowercase
                if (/[a-z]/.test(password) && /[A-Z]/.test(password)) {
                    strength += 25;
                }
                
                // Update strength bar
                strengthBar.style.width = strength + '%';
                
                // Update strength text
                if (strength < 25) {
                    strengthBar.className = 'progress-bar bg-danger';
                    strengthText.textContent = 'Very Weak';
                    strengthText.className = 'text-danger';
                } else if (strength < 50) {
                    strengthBar.className = 'progress-bar bg-warning';
                    strengthText.textContent = 'Weak';
                    strengthText.className = 'text-warning';
                } else if (strength < 75) {
                    strengthBar.className = 'progress-bar bg-info';
                    strengthText.textContent = 'Medium';
                    strengthText.className = 'text-info';
                } else {
                    strengthBar.className = 'progress-bar bg-success';
                    strengthText.textContent = 'Strong';
                    strengthText.className = 'text-success';
                }
            });
        }
    });
</script>