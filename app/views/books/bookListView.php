<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/bookListView.css" class="">
    <link rel="stylesheet" href="css/style.css" class="">
    <link rel="stylesheet" href="js/slide.js" class="">
</head>

<body>
    <?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <div class="container">
        <?php if (!isset($_GET['id'])): ?>
            <div class="slider-container">
                <div class="slider">
                    <div class="slide"><img src="images/slide1.jpg"></div>
                    <div class="slide"><img src="images/slide2.jpeg"></div>
                    <div class="slide"><img src="images/slide3.jpg"></div>
                    <div class="slide"><img src="images/slide4.jpg"></div>
                </div>

                <button class="prev" onclick="moveSlide(-1)">&#10094;</button>
                <button class="next" onclick="moveSlide(1)">&#10095;</button>
            </div>
        <?php endif; ?>



        <script>
            // move slide 
            let index = 0;

            function moveSlide(step) {
                const slides = document.querySelectorAll(".slide");
                const slider = document.querySelector(".slider");
                index += step;

                if (index >= slides.length) index = 0;
                if (index < 0) index = slides.length - 1;

                slider.style.transform = `translateX(${-index * 100}%)`;
            }
            // Tự động chuyển slide sau 5 giây
            setInterval(() => moveSlide(1), 5000);
        </script>

        <div class=" book-grid">
            <?php foreach ($books as $book): ?>
                <div class="book-item">
                    <a href="index.php?action=bookdetail&id=<?php echo $book['book_id']; ?>" class="book-link">
                        
                        <div class="book-img-container">
                            <?php
                                $imageName = !empty($book['image_url']) ? trim($book['image_url']) : 'default-book.png';
                                $displayPath = "images/" . $imageName; 
                            ?>
                            <img src="<?php echo $displayPath; ?>" 
                                onerror="this.onerror=null; this.src='images/default-book.png';" 
                                alt="<?php echo htmlspecialchars($book['book_title']); ?>">
                        </div>

                        <h3><?php echo htmlspecialchars($book['book_title']); ?></h3>
                    </a> <p><strong>Genre:</strong> <?php echo htmlspecialchars($book['categories_name'] ?? 'N/A'); ?></p>
                    <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
                    <p><strong>Stock quantity:</strong> <?php echo (int)$book['stock_quantity']; ?></p>
                    
                    </div>
            <?php endforeach; ?>
        </div>
            
        </div>

        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a class="<?= ($i == $currentPage) ? 'active' : '' ?>"
                    href="index.php?action=listbook&page=<?= $i ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>
</body>

</html>
