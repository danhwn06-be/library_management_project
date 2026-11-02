<?php require_once __DIR__ . '/../../views/layouts/header.php'; ?>

<div class="main-container mt-4 mb-5">
    <!-- Tiêu đề kết quả -->
    <div class="d-flex align-items-center mb-4">
        <h4 class="text-primary m-0">
            <i class="bi bi-search me-2"></i>Search results for: "<?= htmlspecialchars($keyword) ?>"
        </h4>
        <span class="badge bg-secondary ms-3"><?= count($books) ?> Results</span>
    </div>

    <?php if (empty($books)): ?>
        <!-- Thông báo nếu không tìm thấy -->
        <div class="alert alert-warning text-center py-5 shadow-sm" role="alert">
            <i class="bi bi-emoji-frown display-4 d-block mb-3"></i>
            <h5>Sorry, no books match your search criteria!</h5>
            <p>Please try again with a different keyword or check your spelling.</p>
            <a href="index.php?action=home" class="btn btn-outline-primary mt-2">
                <i class="bi bi-arrow-left me-2"></i>Back to Home
            </a>
        </div>
    <?php else: ?>
        <!-- Lưới hiển thị sách -->
       <div class="book-grid">
    <?php foreach ($books as $book): ?>
        <div class="book-item ">
            <a href="index.php?action=bookdetail&id=<?= $book['book_id'] ?>" style="text-decoration: none; color: inherit;">
                <div class="book-image-wrapper">
                    <img src="images/<?= htmlspecialchars($book['image_url']) ?>"
                         onerror="this.onerror=null; this.src='images/default-book.png';"
                         alt="<?= htmlspecialchars($book['book_title']) ?>"
                         class="book-image">
                </div>

                <div class="card-body p-2">
                    <h3 class="book-title" title="<?= htmlspecialchars($book['book_title']) ?>">
                        <?= htmlspecialchars($book['book_title']) ?>
                    </h3>
            </a> <p class="book-info mb-1">
                        <strong>Author:</strong> <?= htmlspecialchars($book['author']) ?>
                    </p>

                    <p class="book-info">
                        <strong>Genre:</strong> <?= htmlspecialchars($book['categories_name']) ?>
                    </p>
                    
                    <p class="book-info">
                        <strong>Stock:</strong> <?= htmlspecialchars($book['stock_quantity']) ?>
                    </p>
                </div>
        </div>
    <?php endforeach; ?>
</div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>
