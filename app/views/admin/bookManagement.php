<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Book Management</title>
    <link rel="stylesheet" href="css/admin.css">

</head>
<body>
    <?php
    // Tái sử dụng header
    require_once __DIR__ . '/../layouts/headerAdmin.php';

    ?>

   
    <div class="container">
        <h1 class="text-center mt-4 mb-4">Book Management</h1>
        <?php if (isset($_SESSION['import_result'])): ?>
            <div class="alert alert-info">
                Import completed: <?= $_SESSION['import_result']['success'] ?> success, <?= $_SESSION['import_result']['skipped'] ?> skipped.
            </div>
            <?php unset($_SESSION['import_result']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['delete_error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['delete_error'] ?>
            </div>
            <?php unset($_SESSION['delete_error']); ?>
        <?php endif; ?>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="<?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                    <a href="index.php?action=book_management&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
        <div class="select-add-book">
                <a href="index.php?action=import_book_by_excel">
                    <button class="btn-add-book">Import books</button>
                </a>

                <a href="index.php?action=show_form_add_book">
                    <button class="btn-add-book">Add new book</button>
                </a>
            </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Book ID</th>
                        <th>Title</th>
                        <th>Images</th>
                        <th>Author</th>
                        <th>Price</th>
                        <th>Genre</th>
                        <th>Pulisher</th>
                        <th>Pulish year</th>
                        <th>Stock quantity</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($books)): // Sửa từ $book thành $books ?>
                            <?php foreach ($books as $book): ?>
                                <tr>
                                    <td><?php echo $book['book_id']; ?></td>
                                    <td><?php echo htmlspecialchars($book['book_title']); ?></td>
                                    <td>
                                        <img src="public/images/<?php echo $book['image_url']; ?>" width="50" alt="" class="Cover">
                                    </td>
                                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                                    
                                    <td><?php echo number_format($book['price'] ?? 0); ?>đ</td> 
                                    <td><?php echo htmlspecialchars($book['categories_name'] ?? "N/A"); ?></td>
                                    <td><?php echo htmlspecialchars($book['publisher'] ?? "N/A"); ?></td>
                                    <td><?php echo htmlspecialchars($book['publish_year'] ?? "N/A"); ?></td>
                                    
                                    <td><?php echo $book['stock_quantity']; ?></td>
                                    <td>
                                        <a href="index.php?action=edit_book&id=<?php echo $book['book_id']; ?>" class="btn btn-primary btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>
                                        <a href="index.php?action=delete_book&id=<?php echo $book['book_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');"><i class="fa-solid fa-trash-can"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center">No books found.</td>
                            </tr>
                        <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <ul class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="<?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                <a href="index.php?action=book_management&page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>

    <?php
    // Tái sử dụng footer
    require_once __DIR__ . '/../layouts/footer.php';
    ?>
</body>
</html>
    
