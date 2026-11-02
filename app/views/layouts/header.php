<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Header</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bookListView.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <!-- TOP BAR -->
    <nav class="navbar">
        <div class="container d-flex justify-content-between align-items-center">

            <!-- LOGO -->
            <a class="navbar-brand d-flex align-items-center text-white fw-bold m-0" href="index.php?action=home">
                <img src="images/logo.png" alt="Logo" width="45" class="me-2 shadow-sm rounded bg-white p-1">
                <span style="letter-spacing: 1px;">TVAN LIBRARY</span>
            </a>

            <!-- USER -->
            <div class="d-flex align-items-center">
                <?php if (isset($_SESSION['user'])): ?>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                        <?= htmlspecialchars($_SESSION['user']['username']) ?>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li>
                            <a class="dropdown-item" href="index.php?action=profile">
                                <i class="bi bi-person me-2"></i> My Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="index.php?action=history">
                                <i class="bi bi-clock-history me-2"></i> My Borrowing History
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item text-danger fw-bold" href="index.php?action=logout">
                                <i class="bi bi-box-arrow-right me-2"></i> Log out
                            </a>
                        </li>
                    </ul>
                </div>
                <?php else: ?>
                <a href="?action=login" class="btn btn-outline-light btn-sm me-2">Login</a>
                <a href="?action=register" class="btn btn-outline-light btn-sm me-2">Register</a>
                <?php endif; ?>
            </div>

        </div>
    </nav>

    <!-- MENU BAR -->
    <div style="background-color: #f3e4c9; border-bottom: 1px solid #e0d0b0;">
        <div class="container py-2">
            <div class="row align-items-center">

                <!-- MENU -->
                <div class="col-md-8">
                    <ul class="nav">
                        <li class="nav-item">
                            <a class="nav-link fw-bold active" href="index.php?action=home">Homepage</a>
                        </li>

                        <!-- DROPDOWN -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fw-semibold" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Categories
                            </a>

                            <ul class="dropdown-menu shadow">

                                <!-- All books -->
                                <li>
                                    <a class="dropdown-item" href="index.php?action=home">All books</a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                <li>
                                    <a class="dropdown-item"
                                        href="index.php?action=category&id=<?= $cat['categories_id'] ?>">
                                        <?= htmlspecialchars($cat['categories_name']) ?> </a>
                                </li>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <li><span class="dropdown-item text-muted">No categories</span></li>
                                <?php endif; ?>
                            </ul>
                        </li>

                        <li class="nav-item"><a class="nav-link fw-semibold" href="index.php?action=about">About</a>
                        </li>
                        <li class="nav-item"><a class="nav-link fw-semibold" href="index.php?action=contact">Contact</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-semibold" href="index.php?action=mybook">My book</a>
                        </li>
                    </ul>
                </div>

                <!-- SEARCH -->
                <div class="col-md-4">
                    <form action="index.php" method="get" class="d-flex">
                        <input type="hidden" name="action" value="search">
                        <input type="text" name="keyword" class="form-control me-2" placeholder="Search Book...">
                        <button class="btn btn-warning">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS (BẮT BUỘC – ĐẶT CUỐI BODY) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
