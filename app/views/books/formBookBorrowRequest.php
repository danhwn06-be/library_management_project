<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book borrow information</title>
    <link rel="stylesheet" href="css/bookRequest.css" class="">
</head>
<body>
    <?php  require_once __DIR__ . '/../layouts/header.php';?>
    <div class="borrow-container">
    <h2 class="title-blue" style="text-align:center;">Book order information</h2>
    
    <div style="border: 1px solid #ddd; padding: 15px; border-radius: 8px;">
        <p><strong>Book Informations</strong></p>
        
        <?php foreach ($_SESSION['my_book_cart'] as $book): ?>
        <div class="book-card ">
            <img src="<?php echo $book['image']; ?>" alt="Sách">
            <div class="book-info">
                <h4><?php echo $book['title']; ?></h4>
                <p><strong>ID:</strong> <?php echo $book['id']; ?></p>
                <p><strong>Author:</strong> <?php echo $book['author']; ?></p>
                <div class="quantity-control">
                    <strong>Quantity:</strong>
                    <span>1</span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <form action="index.php?action=submit_borrow" method="POST">
    <div class="form-group">
        <label>Full Name:</label>
        <input type="text" name="name" required placeholder="Full name">
    </div>

    <div class="form-group">
        <label>Phone Number:</label>
        <input type="text" name="phone" required placeholder="Phone number">
    </div>

    <div class="form-group">
        <label>Address:</label>
        <input type="text" name="address" required placeholder="Address">
    </div>

    <div class="form-group">
        <label>Borrow date:</label>
        <input type="date" name="borrow_date" id="borrow_date" value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>">
    </div>
    
    <div class="form-group" style="flex-direction: column; align-items: flex-start;">
        <div style="display: flex; width: 100%; align-items: center;">
            <label style="min-width: 120px;">Return date:</label> 
            <input type="date" name="return_date" id="return_date" required 
                   min="<?php echo date('Y-m-d'); ?>" 
                   max="<?php echo date('Y-m-d', strtotime('+14 days')); ?>"
                   value="<?php echo date('Y-m-d', strtotime('+14 days')); ?>" style="flex: 1;">
        </div>
        <small style="color: #666; margin-left: 120px; font-size: 0.85rem;">* Thời hạn mượn tối đa 14 ngày (Maximum borrow duration: 14 days)</small>
    </div>

    <div class="form-group" style="flex-direction: column; align-items: flex-start; gap: 10px;">
        <label>Note:</label>
        <textarea name="note" class="note-area"></textarea>
    </div>

    <div class="button-group">
        <button type="submit" class="btn-submit">Submit</button>
        <button type="button" class="btn-cancel" onclick="window.history.back()">Cancel</button>
    </div>
</form>
</div>
</form>
 <?php require_once __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>
