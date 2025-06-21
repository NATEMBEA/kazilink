<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

if (!isset($_GET['id'])) {
    header('Location: search.php');
    exit;
}




$worker_id = $_GET['id'];
$stmt = $pdo->prepare("
    SELECT worker_profiles.*, users.name, users.phone 
    FROM worker_profiles 
    JOIN users ON worker_profiles.user_id = users.id
    WHERE worker_profiles.id = ? AND worker_profiles.is_approved = 1
");
$stmt->execute([$worker_id]);
$worker = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$worker) {
    echo "<div class='alert alert-danger'>Worker not found or profile not approved</div>";
    require_once '../includes/footer.php';
    exit;
}

$skills = explode(',', $worker['skills']);
$whatsapp_url = generateWhatsAppLink($worker['phone'], $worker['name']);
?>

<div class="container py-5">
    <div class="card mb-4">
        <div class="row g-0">
            <?php if($worker['profile_image']): ?>
                <div class="col-md-4">
                    <img src="<?= $worker['profile_image'] ?>" class="card-img rounded-start" alt="<?= $worker['name'] ?>">
                </div>
            <?php endif; ?>
            <div class="col-md-8">
                <div class="card-body">
                    <h1 class="card-title"><?= $worker['name'] ?></h1>
                    <p class="card-text">
                        <strong>Skills:</strong> <?= $worker['skills'] ?><br>
                        <strong>Location:</strong> <?= $worker['location'] ?><br>
                        <?php if (!empty($worker['description'])): ?>
                            <strong>Description:</strong> <?= $worker['description'] ?>
                        <?php endif; ?>
                    </p>
                    <a href="<?= $whatsapp_url ?>" 
                       class="btn btn-success btn-lg"
                       target="_blank">
                        <i class="fab fa-whatsapp"></i> Contact via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-5">
        <h3 class="section-title">Skills & Services</h3>
        <div class="d-flex flex-wrap gap-2">
            <?php foreach($skills as $skill): ?>
                <span class="skill-badge fs-5"><?= trim($skill) ?></span>
            <?php endforeach; ?>
        </div>
    </div>
    
    <?php if (!empty($worker['description'])): ?>
        <div class="mt-5">
            <h3 class="section-title">About Me</h3>
            <p class="lead"><?= nl2br($worker['description']) ?></p>
        </div>
    <?php endif; ?>
</div>
<?php echo "Worker page is working"; ?>

<?php require_once '../includes/footer.php'; ?>
