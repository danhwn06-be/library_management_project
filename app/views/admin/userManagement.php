<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <?php
// Tái sử dụng header
require_once __DIR__ . '/../layouts/headerAdmin.php';

?>



<div class="container">
    <h1 class="text-center mt-4 mb-4">User Management</h1>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'locked'): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-lock-fill me-2"></i> User account has been locked successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'unlocked'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-unlock-fill me-2"></i> User account has been unlocked successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Address</th>
                    <th>Gender</th>
                    <th>Date of Birth</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($users) && !empty($users)) : ?>
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <td><?= htmlspecialchars($user['user_id']) ?></td>
                            <td><?= htmlspecialchars($user['full_name']) ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['phone']) ?></td>
                            <td><?= htmlspecialchars($user['address']) ?></td>
                            <td>
                                <?php if ($user['gender'] == '0' || $user['gender'] === 'Nam'): ?>
                                    <span class="badge bg-primary bg-opacity-75">Nam</span>
                                <?php elseif ($user['gender'] == '1' || $user['gender'] === 'Nữ'): ?>
                                    <span class="badge bg-danger bg-opacity-75">Nữ</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($user['gender'] ?: 'N/A') ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($user['date_of_birth'] ?? 'N/A') ?></td>
                            <td>
                                <span class="badge <?= ($user['role'] == 1) ? 'bg-danger' : 'bg-secondary' ?>">
                                    <?= ($user['role'] == 1) ? 'Admin' : 'Member' ?>
                                </span>
                            </td>
                            <td>
                                <?php if (isset($user['is_locked']) && (int)$user['is_locked'] === 1) : ?>
                                    <span class="badge bg-danger">Blocked</span>
                                <?php else : ?>
                                    <span class="badge bg-success">Active</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (isset($user['is_locked']) && (int)$user['is_locked'] === 1) : ?>
                                    <a href="index.php?action=admin_unlock_user&id=<?= $user['user_id'] ?>" 
                                       class="btn btn-sm btn-success" 
                                       onclick="return confirm('Are you sure you want to unlock user <?= htmlspecialchars($user['username']) ?>?');">
                                        <i class="bi bi-unlock"></i> Unlock
                                    </a>
                                <?php else : ?>
                                    <a href="index.php?action=admin_lock_user&id=<?= $user['user_id'] ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Are you sure you want to block/lock user <?= htmlspecialchars($user['username']) ?>?');">
                                        <i class="bi bi-lock"></i> Lock
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="11" class="text-center">No user data available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Tái sử dụng footer
require_once __DIR__ . '/../layouts/footer.php';
?>
</body>
</html>
