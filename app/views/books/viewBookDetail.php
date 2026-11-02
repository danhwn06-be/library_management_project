<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['book_title']); ?> - TVAN Library</title>
    <link rel="stylesheet" href="css/viewBookDetail.css">
</head>

<body>
    <?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div class="book-detail-container">
        <div class="book-detail-wrapper">
            <!-- Phần hình ảnh và thông tin cơ bản -->
            <div class="book-image-section">
                <img src="images/<?php echo htmlspecialchars($book['image_url']); ?>"
                    alt="<?php echo htmlspecialchars($book['book_title']); ?>"
                    class="book-cover">

                <div class="book-price">
                    <?php echo number_format($book['price'], 0, ',', '.'); ?> đ
                </div>

                <div class="book-actions">
                    <a href="index.php?action=add_to_mybook&id=<?php echo $book['book_id']; ?>&title=<?php echo urlencode($book['book_title']); ?>&author=<?php echo urlencode($book['author']); ?>&img=<?php echo urlencode($book['image_url']); ?>"
                        style="text-decoration: none;">
                        <button class="btn-add-to-cart">Add to my book</button>
                    </a>

                </div>

                <div class="book-meta">
                    <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
                    <p><strong>Genre:</strong> <?php echo htmlspecialchars($book['categories_name']); ?></p>
                    <p><strong>Quantity:</strong> <?php echo $book['stock_quantity']; ?></p>
                    <p><strong>Publisher:</strong> <?php echo htmlspecialchars($book['publisher']); ?></p>
                    <p><strong>Publication year:</strong> <?php echo $book['publish_year']; ?></p>
                </div>
            </div>

            <!-- Phần nội dung chi tiết -->
            <div class="book-content-section">
                <h1 class="book-title"><?php echo htmlspecialchars($book['book_title']); ?></h1>

                <div class="book-introduction">
                    <h2>Introduce</h2>
                    <div class="book-description">
                        <?php echo nl2br(htmlspecialchars($book['content'])); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>

    <script>
        // // Xử lý nút Add to my book
        // document.querySelector('.btn-add-to-cart').addEventListener('click', function() {
        //     alert('Đã thêm vào danh sách của bạn!');
        //     // Thêm logic AJAX để thêm vào giỏ sách
        // }); 

        // Xử lý nút Borrow
        var btnBorrow = document.querySelector('.btn-borrow');
        if (btnBorrow) {
            btnBorrow.addEventListener('click', function() {
                <?php if ($book['stock_quantity'] > 0): ?>
                    window.location.href = 'index.php?controller=borrow&action=request&book_id=<?php echo $book['book_id']; ?>';
                <?php else: ?>
                    alert('Sách hiện đang hết! Vui lòng quay lại sau.');
                <?php endif; ?>
            });
        }
    </script>
</body>

</html>
