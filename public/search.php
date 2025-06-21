<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

$skills = $_GET['skills'] ?? '';
$location = $_GET['location'] ?? '';

// Debugging: Log search parameters
error_log("Search initiated - Skills: $skills, Location: $location");

$sql = "SELECT worker_profiles.*, users.name, users.phone 
        FROM worker_profiles 
        JOIN users ON worker_profiles.user_id = users.id
        WHERE worker_profiles.is_approved = 1";

$params = [];

if (!empty($skills)) {
    $sql .= " AND skills LIKE ?";
    $params[] = "%$skills%";
}

if (!empty($location)) {
    $sql .= " AND location LIKE ?";
    $params[] = "%$location%";
}

$sql .= " ORDER BY users.name ASC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debugging: Log query results
    error_log("Search found " . count($results) . " workers");
} catch (PDOException $e) {
    // Log database errors
    error_log("Database error: " . $e->getMessage());
    $results = [];
}
?>

<div class="container py-5">
    <h2 class="display-5 fw-bold mb-4 text-center">Find Skilled Workers</h2>
    
    <div class="card shadow-lg rounded-4 mb-5 border-0">
        <div class="card-body p-4">
            <form method="GET" class="mb-0">
                <div class="row g-3 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-tools text-primary"></i></span>
                            <input type="text" name="skills" class="form-control form-control-lg border-start-0 ps-0" 
                                   placeholder="Search by skill (e.g. plumbing, carpentry)" 
                                   value="<?= htmlspecialchars($skills) ?>">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-map-marker-alt text-primary"></i></span>
                            <input type="text" name="location" class="form-control form-control-lg border-start-0 ps-0" 
                                   placeholder="Enter your location" 
                                   value="<?= htmlspecialchars($location) ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row" id="worker-results">
        <?php if (!empty($results)): ?>
            <?php foreach($results as $worker): 
                $skills_list = explode(',', $worker['skills']);
            ?>
            <div class="col-md-4 mb-4">
                <div class="card worker-card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                    <?php if($worker['profile_image']): ?>
                        <div class="position-relative">
                            <img src="<?= $worker['profile_image'] ?>" class="worker-img" alt="<?= $worker['name'] ?>" 
                                 style="height: 200px; width: 100%; object-fit: cover;">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-success rounded-pill px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i> Verified
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <?php if($worker['profile_image']): ?>
                                    <img src="<?= $worker['profile_image'] ?>" class="rounded-circle" 
                                         alt="<?= $worker['name'] ?>" style="width: 60px; height: 60px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 60px; height: 60px;">
                                        <i class="fas fa-user fs-5"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-0"><?= $worker['name'] ?></h5>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-map-marker-alt me-1 text-primary"></i> <?= $worker['location'] ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="mb-2"><i class="fas fa-tools me-1 text-primary"></i> Skills:</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach($skills_list as $skill): ?>
                                    <span class="skill-badge"><?= trim($skill) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button class="btn btn-whatsapp whatsapp-btn" 
                                    data-phone="<?= $worker['phone'] ?>" 
                                    data-name="<?= $worker['name'] ?>">
                                <i class="fab fa-whatsapp me-1"></i> Contact via WhatsApp
                            </button>
                            <a href="worker.php?id=<?= $worker['id'] ?>" class="btn btn-outline-primary">
                                <i class="fas fa-user-circle me-1"></i> View Full Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-search fa-4x text-muted mb-4"></i>
                            <h3>No Workers Found</h3>
                            <p class="text-muted">We couldn't find any workers matching your criteria.</p>
                            
                            <?php if (empty($skills) && empty($location)): ?>
                                <div class="alert alert-info mt-4">
                                    <h5>Is your worker profile missing?</h5>
                                    <p class="mb-3">New worker profiles require admin approval before appearing in search results.</p>
                                    <a href="register.php" class="btn btn-sm btn-outline-primary me-2">
                                        <i class="fas fa-user-plus me-1"></i> Register as Worker
                                    </a>
                                    <a href="../admin/dashboard.php" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-lock me-1"></i> Admin Dashboard
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-4">
                            <h5>Search Tips:</h5>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-lightbulb text-warning me-2"></i> Try different search terms</li>
                                <li><i class="fas fa-lightbulb text-warning me-2"></i> Broaden your location search</li>
                                <li><i class="fas fa-lightbulb text-warning me-2"></i> Check spelling</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

<style>
    .worker-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    .worker-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.1);
        border-color: rgba(52, 152, 219, 0.2);
    }
    
    .skill-badge {
        background-color: #e3f2fd;
        color: #1976d2;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
        display: inline-block;
    }
    
    .btn-whatsapp {
        background-color: #25D366;
        border-color: #25D366;
        color: white;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-whatsapp:hover {
        background-color: #128C7E;
        border-color: #128C7E;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .rounded-4 {
        border-radius: 20px !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // WhatsApp button functionality
        document.querySelectorAll('.whatsapp-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const phone = this.dataset.phone;
                const name = this.dataset.name;
                const message = `Hi ${name}, I saw your profile on WakaziLink and I'm interested in your services`;
                const url = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
                window.open(url, '_blank');
            });
        });
        
        // Animate cards on load
        const workerCards = document.querySelectorAll('.worker-card');
        workerCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100 + (index * 100));
        });
    });
</script>