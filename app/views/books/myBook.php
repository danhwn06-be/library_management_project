<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/bookRequest.css" class="">
</head>
<body>
    <?php  require_once __DIR__ . '/../layouts/header.php';?>
    <div class="borrow-container">
    <h2 class="title-blue">My borrowing book list</h2>
    <hr>
    <?php if (empty($listBooks)): ?>
    <p>Your list books need to borrow is null. <a href="index.php">Return to choose books</a></p>
    <?php else: ?>
    <?php endif; ?>
    <div class="book-list">
        <?php foreach ($listBooks as $book): ?>
        <div class="book-card">
            <button class="btn-qty" onclick="updateQty('<?php echo $book['id']; ?>', -1)">X</button>
            <img src="<?php echo $book['image']; ?>" alt="Sách">
            <div class="book-info">
                <h4><?php echo $book['title']; ?></h4>
                <p><strong>ID:</strong> <?php echo $book['id']; ?></p>
                <p><strong>Author:</strong> <?php echo $book['author']; ?></p>
                <div class="quantity-control">
                    <strong>Quantity:</strong>
                    <button class="btn-qty-minus" style="padding: 2px 8px; border-radius: 4px; border: 1px solid #ccc; background: #fff;" onclick="updateQty('<?php echo $book['id']; ?>', -1)">-</button>
                    <span id="qty-<?php echo $book['id']; ?>" style="margin: 0 10px; font-weight: bold;"><?php echo $book['quantity']; ?></span>
                    <button class="btn-qty-plus" style="padding: 2px 8px; border-radius: 4px; border: 1px solid #ccc; background: #fff;" onclick="updateQty('<?php echo $book['id']; ?>', 1)">+</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="button-group">
        <a href="index.php?action=show_borrow_form">
            <button class="btn-submit">Borrow book</button>
        </a>

         <a href="index.php?action=home">
            <button class="btn-cancel">Cancel</button>
        </a>        
    </div>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>
