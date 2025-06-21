<?php
require_once '../includes/db_connect.php';
require_once __DIR__ . '/../includes/header.php';
require_once '../includes/functions.php';
?>

<section class="hero position-relative overflow-hidden">
    <!-- Animated background elements -->
    <div class="position-absolute top-0 start-0 w-100 h-100">
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-primary opacity-10"></div>
        <div class="position-absolute bottom-0 end-0" style="width: 50%; height: 50%;">
            <div class="position-relative w-100 h-100">
                <div class="position-absolute rounded-circle bg-primary opacity-10" style="width: 400px; height: 400px; top: -150px; right: -150px;"></div>
                <div class="position-absolute rounded-circle bg-secondary opacity-10" style="width: 300px; height: 300px; bottom: -100px; right: -100px;"></div>
            </div>
        </div>
    </div>
    
    <div class="container position-relative z-1">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-3 fw-bold mb-4">Connect with Skilled Professionals<span class="text-primary">Professionals</span> Near You</h1>
                <p class="lead fs-4 mb-5">WakaziLink bridges the gap between skilled informal workers and clients looking for quality services.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="search.php" class="btn btn-light btn-lg px-4 py-3 fw-bold shadow-sm">
                        <i class="fas fa-search me-2"></i> Find Workers
                    </a>
                    <a href="register.php" class="btn btn-outline-light btn-lg px-4 py-3 border-2">
                        <i class="fas fa-user-plus me-2"></i> Register as Worker
                    </a>
                </div>
                
                <div class="d-flex align-items-center mt-5">
                    <div class="d-flex">
                        <div class="avatar-group">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&h=100&q=80" class="avatar" alt="User">
                            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100&h=100&q=80" class="avatar" alt="User">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&h=100&q=80" class="avatar" alt="User">
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="mb-0 fw-medium">Join 1,500+</span> skilled professionals</p>
                        <div class="d-flex align-items-center">
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <span class="ms-2">4.7/5 from 300+ reviews</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center d-none d-lg-block">
                <div class="position-relative">
                    <div class="position-absolute top-0 start-50 translate-middle-x mt-n5">
                        <div class="pulse-animation">
                            <div class="pulse-circle"></div>
                        </div>
                    </div>
                    <img src="https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=600&h=500&q=80" class="img-fluid rounded-4 shadow-lg border border-4 border-white" alt="Skilled Workers">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container mt-5">
    <div class="search-box shadow-lg rounded-4 overflow-hidden">
        <div class="bg-primary text-white p-4 text-center">
            <h2 class="mb-0"><i class="fas fa-search me-2"></i> Find Skilled Workers</h2>
        </div>
        <div class="p-4">
            <form id="search-form" method="GET" action="search.php">
                <div class="row g-3">
                    <div class="col-md-5">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-tools text-primary"></i></span>
                            <input type="text" name="skills" class="form-control border-start-0 ps-0" placeholder="Search by skill (e.g. Plumbing, Carpentry)">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-map-marker-alt text-primary"></i></span>
                            <input type="text" name="location" class="form-control border-start-0 ps-0" placeholder="Enter your location">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-lg w-100 h-100">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="py-6 bg-light position-relative overflow-hidden">
    <!-- Decorative elements -->
    <div class="position-absolute top-0 start-0 w-100 h-100">
        <div class="position-absolute top-0 end-0" style="width: 40%;">
            <div class="position-relative w-100 h-100">
                <div class="position-absolute rounded-circle bg-primary opacity-05" style="width: 300px; height: 300px; top: -100px; right: -100px;"></div>
            </div>
        </div>
    </div>
    
    <div class="container position-relative z-1">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <h2 class="display-5 fw-bold mb-3">How WakaziLink Works</h2>
                <p class="lead text-muted">Connecting skilled workers with clients in just a few simple steps</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-circle bg-primary text-white">
                                <i class="fas fa-user-plus fs-3"></i>
                            </div>
                            <div class="ms-3">
                                <h5 class="mb-0">1. Register</h5>
                            </div>
                        </div>
                        <p class="mb-0">Workers create a profile showcasing their skills, experience, and location to start getting discovered.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-circle bg-success text-white">
                                <i class="fas fa-search fs-3"></i>
                            </div>
                            <div class="ms-3">
                                <h5 class="mb-0">2. Discover</h5>
                            </div>
                        </div>
                        <p class="mb-0">Clients search for workers by skill, location, and availability to find the perfect match.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-circle bg-warning text-white">
                                <i class="fab fa-whatsapp fs-3"></i>
                            </div>
                            <div class="ms-3">
                                <h5 class="mb-0">3. Connect</h5>
                            </div>
                        </div>
                        <p class="mb-0">Contact workers directly via WhatsApp to discuss your project and get started.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-6">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <h2 class="display-5 fw-bold mb-3">Featured Workers</h2>
                <p class="lead text-muted">Top-rated professionals in your area</p>
            </div>
        </div>
        
        <div class="row">
            <?php
            $stmt = $pdo->query("
                SELECT worker_profiles.*, users.name, users.phone 
                FROM worker_profiles 
                JOIN users ON worker_profiles.user_id = users.id
                WHERE worker_profiles.is_approved = 1 
                ORDER BY RAND() LIMIT 3
            ");
            
            while ($worker = $stmt->fetch(PDO::FETCH_ASSOC)):
                $skills = explode(',', $worker['skills']);
            ?>
            <div class="col-md-4 mb-4">
                <div class="worker-card shadow rounded-4 overflow-hidden border-0 h-100">
                    <?php if($worker['profile_image']): ?>
                        <div class="position-relative">
                            <img src="<?= $worker['profile_image'] ?>" class="worker-img" alt="<?= $worker['name'] ?>">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-success rounded-pill px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i> Verified
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <?php if($worker['profile_image']): ?>
                                        <img src="<?= $worker['profile_image'] ?>" class="rounded-circle" alt="<?= $worker['name'] ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-user fs-5"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-0"><?= $worker['name'] ?></h5>
                                <p class="text-muted mb-0"><i class="fas fa-map-marker-alt me-1 text-primary"></i> <?= $worker['location'] ?></p>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <p class="mb-2"><i class="fas fa-tools me-1 text-primary"></i> <strong>Skills:</strong></p>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach($skills as $skill): ?>
                                    <span class="skill-badge"><?= trim($skill) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <button class="btn btn-whatsapp whatsapp-btn" 
                                    data-phone="<?= $worker['phone'] ?>" 
                                    data-name="<?= $worker['name'] ?>">
                                <i class="fab fa-whatsapp me-1"></i> Contact
                            </button>
                            <a href="worker.php?id=<?= $worker['id'] ?>" class="btn btn-outline-primary">
                                View Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="search.php" class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow-sm">
                View All Workers <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<section class="py-6 bg-primary text-white position-relative overflow-hidden">
    <!-- Decorative elements -->
    <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10">
        <div class="position-absolute top-0 start-0" style="width: 40%;">
            <div class="position-relative w-100 h-100">
                <div class="position-absolute rounded-circle bg-white" style="width: 400px; height: 400px; top: -200px; left: -200px;"></div>
            </div>
        </div>
    </div>
    
    <div class="container position-relative z-1">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="display-5 fw-bold mb-4">Ready to Get Started?</h2>
                <p class="lead mb-5">Join thousands of skilled professionals and clients connecting through WakaziLink</p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="register.php" class="btn btn-light btn-lg px-5 py-3 fw-bold rounded-pill">
                        <i class="fas fa-user-plus me-2"></i> Register as Worker
                    </a>
                    <a href="search.php" class="btn btn-outline-light btn-lg px-5 py-3 rounded-pill border-2">
                        <i class="fas fa-search me-2"></i> Find Workers
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>

<style>
    /* Custom styles for the enhanced homepage */
    .hero {
        padding: 120px 0;
        background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .avatar-group {
        display: flex;
    }
    
    .avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 3px solid white;
        margin-left: -15px;
        object-fit: cover;
    }
    
    .avatar:first-child {
        margin-left: 0;
    }
    
    .icon-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .pulse-animation {
        position: relative;
        width: 80px;
        height: 80px;
    }
    
    .pulse-circle {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        animation: pulse 2s infinite;
    }
    
    .pulse-circle::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        animation: pulse 2s infinite 0.5s;
    }
    
    @keyframes pulse {
        0% {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }
        100% {
            transform: translate(-50%, -50%) scale(2);
            opacity: 0;
        }
    }
    
    .worker-card {
        transition: all 0.3s ease;
    }
    
    .worker-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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
    
    .search-box {
        background: white;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-radius: 20px;
        overflow: hidden;
        margin-top: -50px;
        position: relative;
        z-index: 10;
    }
    
    .rounded-4 {
        border-radius: 20px !important;
    }
    
    .py-6 {
        padding-top: 5rem !important;
        padding-bottom: 5rem !important;
    }
    
    .border-2 {
        border-width: 2px !important;
    }
</style>

<script>
    // Animation for worker cards
    document.addEventListener('DOMContentLoaded', function() {
        // Animate worker cards on scroll
        const workerCards = document.querySelectorAll('.worker-card');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = 1;
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1
        });
        
        workerCards.forEach(card => {
            card.style.opacity = 0;
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            observer.observe(card);
        });
        
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
    });
</script>