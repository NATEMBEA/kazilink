<?php

// Add at the very top
define('ADMIN_PATH', true);
require_once __DIR__ . '../../includes/db_connect.php'; // Add this line to include the database connection
require_once __DIR__ . '../../includes/auth.php';
requireAdmin();

// Rest of your manage_users code...

// Handle actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    if ($_GET['action'] === 'approve') {
        $stmt = $pdo->prepare("UPDATE worker_profiles SET is_approved = 1 WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($_GET['action'] === 'delete') {
        // First get profile image to delete it
        $stmt = $pdo->prepare("SELECT profile_image FROM worker_profiles WHERE id = ?");
        $stmt->execute([$id]);
        $profile = $stmt->fetch();
        
        if ($profile && $profile['profile_image'] && file_exists("../public/{$profile['profile_image']}")) {
            unlink("../public/{$profile['profile_image']}");
        }
        
        // Delete worker profile
        $stmt = $pdo->prepare("DELETE FROM worker_profiles WHERE id = ?");
        $stmt->execute([$id]);
    }
}

// Get all worker profiles
$stmt = $pdo->query("
    SELECT worker_profiles.*, users.name, users.email, users.phone, users.created_at 
    FROM worker_profiles 
    JOIN users ON worker_profiles.user_id = users.id
    ORDER BY worker_profiles.is_approved ASC, users.created_at DESC
");

require_once '../includes/header.php';
?>

<div class="container py-5">
    <h1 class="mb-4">Manage Worker Profiles</h1>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Skills</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($worker = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?= $worker['name'] ?></td>
                    <td><?= $worker['email'] ?></td>
                    <td><?= $worker['phone'] ?></td>
                    <td><?= $worker['skills'] ?></td>
                    <td><?= $worker['location'] ?></td>
                    <td>
                        <?php if ($worker['is_approved']): ?>
                            <span class="badge bg-success">Approved</span>
                        <?php else: ?>
                            <span class="badge bg-warning">Pending</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('M d, Y', strtotime($worker['created_at'])) ?></td>
                    <td>
                        <?php if (!$worker['is_approved']): ?>
                            <a href="manage_users.php?action=approve&id=<?= $worker['id'] ?>" class="btn btn-sm btn-success">Approve</a>
                        <?php endif; ?>
                        <a href="manage_users.php?action=delete&id=<?= $worker['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        <!-- <a href="../public/worker.php?id=<?= $worker['id'] ?>" class="btn btn-sm btn-primary">View</a> -->
                         <a href="worker.php?id=<?= $worker['id'] ?>" class="btn btn-sm btn-primary">View</a>

                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>