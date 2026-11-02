<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book borrow information</title>
    <link rel="stylesheet" href="css/formAddBook.css" class="">
</head>
<body>
    <?php  require_once __DIR__ . '/../layouts/headerAdmin.php';?>
    <div class="add-book-container">
    <h2 class="title-blue" style="text-align:center;">Edit book</h2>  
    <form action="index.php?action=do_edit_book" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
        <div class="form-group">
            <label>Book title</label>
            <input type="text" name="book_title" value="<?=  $book['book_title'] ?>">
        </div>

        <div class="form-group">
            <label>Author</label>
            <input type="text" name="author" value="<?=  $book['author'] ?>">
        </div>

        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="categories_id" >
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['categories_id']; ?>" <?=  ($cat['categories_id'] == $book['categories_id']) ? 'selected' : ''; ?>>
                <?=  $cat['categories_name'] ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>  

        <div class="form-group-flex">
            <div class="form-group">
                <label>Price</label>
                <input type="text" name="price" class="price" value="<?= $book['price'] ?>">
            </div>
            <div class="form-group">
                <label>Stock quantity</label>
                <input type="number" name="stock_quantity" class="stock_quantity" value="<?= $book['stock_quantity'] ?>">
            </div>        
        </div>

        <div class="form-group-flex">
            <div class="form-group">
                <label>Pulisher</label>
                <input type="text" name="publisher" value="<?= $book['publisher'] ?>">
            </div>
            <div class="form-group">
                <label>Pulish year</label>
                <input type="text" name="publish_year" class="publish_year" value="<?= $book['publish_year'] ?>">
            </div>        
        </div>

        <div class="form-group">
            <label for="book_image">Book Image</label>
            <img src="public/images/<?= $book['image_url'] ?>" width="100">
            <input type="file" name="book_image">
        </div>  
        
        <div class="form-group" style="flex-direction: column; align-items: flex-start; gap: 10px;">
            <label>Content</label>
            <input name="content" class="content-area" value="<?= $book['content'] ?>">
        </div>

        <div class="button-group">
            <button type="submit" class="btn-submit">Submit</button>
            <button type="button" class="btn-cancel" onclick="window.history.back()">Cancel</button>
        </div>
    </form> 
 </div>
 <?php require_once __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>
