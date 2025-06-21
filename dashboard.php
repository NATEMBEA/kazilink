<?php

// Add at the very top
define('ADMIN_PATH', true);
require_once __DIR__ . '../../includes/auth.php';
requireAdmin();

// Include database connection
require_once __DIR__ . '../../includes/db_connect.php';
// If the file is actually at a different location, adjust the path accordingly, for example:
// require_once __DIR__ . '/../../../includes/db.php';

// Rest of your dashboard code...




// Get counts for dashboard
$workers_count = $pdo->query("SELECT COUNT(*) FROM worker_profiles")->fetchColumn();
$users_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$pending_count = $pdo->query("SELECT COUNT(*) FROM worker_profiles WHERE is_approved = 0")->fetchColumn();

require_once __DIR__ . '../../includes/header.php';
?>

<div class="container py-5">
    <h1 class="mb-4">Admin Dashboard</h1>
    
    <div class="row mb-5">
        <div class="col-md-4">
            <div class="card bg-primary text-white text-center p-4">
                <h2><?= $workers_count ?></h2>
                <p class="mb-0">Worker Profiles</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white text-center p-4">
                <h2><?= $users_count ?></h2>
                <p class="mb-0">Registered Users</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark text-center p-4">
                <h2><?= $pending_count ?></h2>
                <p class="mb-0">Pending Approvals</p>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-between mb-4">
        <h2>Pending Approvals</h2>
        <a href="manage_users.php" class="btn btn-primary">Manage All Users</a>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Skills</th>
                    <th>Location</th>
                    <th>Date Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("
                    SELECT worker_profiles.*, users.name, users.created_at 
                    FROM worker_profiles 
                    JOIN users ON worker_profiles.user_id = users.id
                    WHERE worker_profiles.is_approved = 0
                    ORDER BY users.created_at DESC
                    LIMIT 5
                ");
                
                while ($worker = $stmt->fetch(PDO::FETCH_ASSOC)):
                ?>
                <tr>
                    <td><?= $worker['name'] ?></td>
                    <td><?= $worker['skills'] ?></td>
                    <td><?= $worker['location'] ?></td>
                    <td><?= date('M d, Y', strtotime($worker['created_at'])) ?></td>
                    <td>
                        <a href="approve.php?id=<?= $worker['id'] ?>" class="btn btn-sm btn-success">Approve</a>
                        <a href="delete.php?id=<?= $worker['id'] ?>" class="btn btn-sm btn-danger">Delete</a>
                        <a href="../public/worker.php?id=<?= $worker['id'] ?>" class="btn btn-sm btn-primary">View</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>