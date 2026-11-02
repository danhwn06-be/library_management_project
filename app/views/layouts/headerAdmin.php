<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__, 2) . '/core/Auth.php';
Auth::admin();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/headerAdmin.css">
    <?php if (!empty($pageCSS)): ?>
    <link rel="stylesheet" href="css/<?= htmlspecialchars($pageCSS) ?>">
    <?php endif; ?>
</head>

<body>

    <nav class="navbar navbar-dark" style="background:#4f8fb8">
        <div class="container-fluid px-4">
            <img src="images/logo.png" alt="Logo" width="45"
                class="me-2 shadow-sm rounded bg-white p-1">
            <span class="navbar-brand fw-bold">TVAN LIBRARY – ADMIN</span>

            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i>
                    <?= $_SESSION['user']['username'] ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item text-danger" href="index.php?action=logout">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

   <?php
// Lấy action hiện tại từ URL để so sánh
$current_action = isset($_GET['action']) ? $_GET['action'] : '';
?>

<div class="bg-light border-bottom">
    <div class="container py-2">          

        <a class="btn <?php echo ($current_action == 'book_management' || $current_action == 'do_import_book' || $current_action == 'import_book_by_excel') ? 'btn-outline-primary' : 'btn-outline-secondary'; ?> me-2" 
           href="index.php?action=book_management">
            Book Management
        </a>

        <a class="btn <?php echo ($current_action == 'admin_user_list') ? 'btn-outline-primary' : 'btn-outline-secondary'; ?> me-2" 
           href="index.php?action=admin_user_list">
            User Management
        </a>

        <a class="btn <?php echo ($current_action == 'admin_borrow_list') ? 'btn-outline-primary' : 'btn-outline-secondary'; ?> me-2" 
           href="index.php?action=admin_borrow_list">
            Borrowing Management
        </a>

        <a class="btn <?php echo ($current_action == 'admin_overdue_list') ? 'btn-danger' : 'btn-outline-danger'; ?> me-2" 
           href="index.php?action=admin_overdue_list">
            <i class="bi bi-exclamation-triangle me-1"></i>Overdue Reminders
        </a>
        
        <a class="btn <?php echo ($current_action == 'admin_statistic') ? 'btn-outline-primary' : 'btn-outline-secondary'; ?>" 
           href="index.php?action=admin_statistic">
            Statistics
        </a>
             
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
