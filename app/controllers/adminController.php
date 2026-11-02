<?php
use PhpOffice\PhpSpreadsheet\IOFactory;
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/book.php';
require_once __DIR__ . '/../models/category.php';
require_once __DIR__ . '/../models/borrowRequestBook.php';
require_once __DIR__ . '/../models/bookCopies.php';
require_once dirname(__DIR__, 2) . '/app/core/Auth.php';

class AdminController
{
    private $userModel;
    private $bookModel;

    private $categoryModel;
    private $model;
    private $borrowRequestBookModel;
    private $bookCopiesModel;
        

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
        $this->userModel = new User($db);
        
        $this->bookModel = new Book($db);

        $this->categoryModel = new Category($db);
        $this->model = new BorrowRequest($db);
        $this->borrowRequestBookModel = new BorrowRequestBook($db);
        $this->bookCopiesModel = new BookCopies($db);
    }

    public function list()
    {
        Auth::admin();
        $requests = $this->borrowRequestBookModel->getAll();
        require dirname(__DIR__, 2) . '/app/views/admin/borrowList.php';
    }

    public function detail($id)
    {
        Auth::admin();
        $request = $this->borrowRequestBookModel->getById($id);
        $items = $this->bookCopiesModel->getItems($id);
        require dirname(__DIR__, 2) . '/app/views/admin/borrowDetail.php';
    }

    public function bookManagement()
    {
        Auth::admin();
        $categories = $this->categoryModel->getAllCategories();

        // 2. Phân trang
        $limit = 20;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($currentPage < 1) $currentPage = 1;
        $offset = ($currentPage - 1) * $limit;

        // 3. Lấy sách
        $books = $this->bookModel->getBooksPagination($limit, $offset);
        $totalBooks = $this->bookModel->getTotalBooks();
        $totalPages = ceil($totalBooks / $limit);

        require_once __DIR__ . '/../views/admin/bookManagement.php';
    }           
                                                            

    public function showAddBookForm() {
        Auth::admin();
        $categories = $this->categoryModel->getAllCategories();
        require_once __DIR__ . '/../views/admin/formAddBook.php';
    }
    public function doAddBook() {
    Auth::admin();
    // Debug: Kiểm tra xem dữ liệu có gửi lên không
    // print_r($_POST); print_r($_FILES); die(); 

    $data = [
        "book_title"    => $_POST['book_title'] ?? '',
        "author"        => $_POST['author'] ?? '',
        "categories_id" => $_POST['categories_id'] ?? '',
        "price"         => $_POST['price'] ?? 0,
        "stock_quantity"=> $_POST['stock_quantity'] ?? 0,
        "publisher"     => $_POST['publisher'] ?? '',
        "publish_year"  => $_POST['publish_year'] ?? '',
        "image_url"     => 'default.png', // Mặc định
        "content"       => $_POST['content'] ?? ''
    ];

    if (isset($_FILES['book_image']) && $_FILES['book_image']['error'] == 0) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['book_image']['name'], PATHINFO_EXTENSION));
        $fileSize = $_FILES['book_image']['size'];

        if (!in_array($ext, $allowedExtensions)) {
            die("Error: Only JPG, JPEG, PNG and WEBP image files are allowed.");
        }
        if ($fileSize > 2 * 1024 * 1024) {
            die("Error: Uploaded image size cannot exceed 2MB.");
        }

        // Lưu ý: Đảm bảo thư mục "public/images/" đã tồn tại
        $folder = "public/images/";
        if (!is_dir($folder)) mkdir($folder, 0777, true); 

        $filename = time() . "_" . preg_replace('/[^a-zA-Z0-9_\.]/', '', $_FILES['book_image']['name']);
        $destination = $folder . $filename;

        if (move_uploaded_file($_FILES['book_image']['tmp_name'], $destination)) {
            $data['image_url'] = $filename;
        }
    }

    $result = $this->bookModel->addNewBook($data);

    if (!$result['status']) {
        // Hiện lỗi ra để bạn biết sai ở đâu (ví dụ: sai tên cột, sai ID...)
        die("Lỗi Database: " . ($result['message'] ?? 'Unknown Error'));
    } else {
        header("Location: index.php?action=book_management");
        exit();
    }
    }
    
     public function showEditBookForm($id) {
    Auth::admin();
    // Nếu $id truyền vào bị rỗng hoặc false, thử lấy trực tiếp từ $_GET lần nữa để chắc chắn
    if (!$id) {
        $id = $_GET['id'] ?? 0;
    }

    $book = $this->bookModel->getBookById($id);

    if (!$book) {
        // Debug nếu vẫn lỗi
        echo "Không tìm thấy sách với ID: " . $id; 
        die();
    }

    $categories = $this->categoryModel->getAllCategories();
    require_once __DIR__ . '/../views/admin/formEditBook.php';
}

    public function doEditBook() {
        Auth::admin();
        $id = $_POST['book_id'] ?? 0;
        $oldBook = $this->bookModel->getBookById($id);
        $img_url = $oldBook['image_url'];

        if (isset($_FILES['book_image']) && $_FILES['book_image']['error'] == 0){
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['book_image']['name'], PATHINFO_EXTENSION));
            $fileSize = $_FILES['book_image']['size'];

            if (!in_array($ext, $allowedExtensions)) {
                die("Error: Only JPG, JPEG, PNG and WEBP image files are allowed.");
            }
            if ($fileSize > 2 * 1024 * 1024) {
                die("Error: Uploaded image size cannot exceed 2MB.");
            }

            $folder = "public/images/";
            if (!is_dir($folder)) mkdir($folder, 0777, true);

            $filename = time() . "_" . preg_replace('/[^a-zA-Z0-9_\.]/', '', $_FILES['book_image']['name']);
            if (move_uploaded_file($_FILES['book_image']['tmp_name'], $folder . $filename)){
                $img_url = $filename;
            }
        }
        $data = [
            "book_id"       => $id,
            "book_title"    => $_POST['book_title'] ?? '',
            "author"        => $_POST['author'] ?? '',
            "categories_id" => $_POST['categories_id'] ?? '',
            "price"         => $_POST['price'] ?? 0,
            "stock_quantity"=> $_POST['stock_quantity'] ?? 0,
            "publisher"     => $_POST['publisher'] ?? '',
            "publish_year"  => $_POST['publish_year'] ?? '',
            "image_url"     => $img_url,
            "content"       => $_POST['content'] ?? ''
        ];

        $updateResult = $this->bookModel->updateBook($id, $data);
        if ($updateResult['status']) { // Kiểm tra key 'status' bên trong mảng
            header("Location: index.php?action=book_management");
            exit();
        } else {
            die("Error: " . $updateResult['message']);
        }
            }
        public function doDeleteBook($id) {
        Auth::admin();
        if ($id) {
            $success = $this->bookModel->deleteBook($id);
            if (!$success) {
                $_SESSION['delete_error'] = "Cannot delete this book because it is in a borrow request.";
            }
        }
        header("Location: index.php?action=book_management"); 
        }
    public function updateStatus($id, $status)
{
    Auth::admin();

    // Cho phép thêm trạng thái 'Returned'
    if (!in_array($status, ['Approved', 'Rejected', 'Returned'])) {
        die('Invalid status');
    }

    $result = $this->borrowRequestBookModel->updateStatus($id, $status);
    
    if ($result) {
        header("Location: index.php?action=admin_borrow_list&msg=success");
    } else {
        header("Location: index.php?action=admin_borrow_list&msg=error");
    }
    exit();
}
    public function show_form_import() {
        Auth::admin();
        require_once __DIR__ . '/../views/admin/formImportBook.php';
    }
    public function doImportBook() {
    Auth::admin();
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel_file'])) {
        $filePath = $_FILES['excel_file']['tmp_name'];

        try {
            $spreadsheet = IOFactory::load($filePath);
            $data = $spreadsheet->getActiveSheet()->toArray();

        if (empty($data)) {
            die("Lỗi: Không đọc được dữ liệu từ file Excel!");
        }

            $success = 0;
            $skipped = 0;

            $this->db->beginTransaction();

            foreach ($data as $index => $row) {
                if ($index == 0) continue; 
                if (empty($row[1])) continue;
                $bookData = [
                    'book_title'     => $row[1], // Cột B
                    'price'          => $row[2], // Cột C
                    'author'         => $row[3], // Cột D
                    'publisher'      => $row[4], // Cột E
                    'publish_year'   => $row[5], // Cột F
                    'stock_quantity' => $row[6], // Cột G
                    'categories_id'  => $row[7], // Cột H
                    'content'        => $row[8], // Cột I
                    'image_url'      => $row[9]  // Cột J
                ];

                if ($this->bookModel->importBook($bookData)) {
                    $success++;
                } else {
                    $skipped++;
                }
            }
            $this->db->commit();
           $_SESSION['import_result'] = [
                'success' => $success,
                'skipped' => $skipped
            ];

            header("Location: index.php?action=book_management");
            exit();

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            die("Error loading file: " . $e->getMessage());
        }
    }
}
    
    public function userManagement()
    {
        Auth::admin();
        $users = $this->userModel->getAllUsers();
        $categories = $this->categoryModel->getAllCategories();
        require_once __DIR__ . '/../views/admin/userManagement.php';
    }

    public function lockUser()
    {
        Auth::admin();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->userModel->lockUser($id);
        }
        header("Location: index.php?action=admin_user_list&msg=locked");
        exit();
    }

    public function unlockUser()
    {
        Auth::admin();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->userModel->unlockUser($id);
        }
        header("Location: index.php?action=admin_user_list&msg=unlocked");
        exit();
    }


    public function showStatistic()
    {
        Auth::admin();
        $generalStats = $this->bookModel->getGeneralStatistics();
        $categoryStats = $this->bookModel->countBooksByCategory();
        $overdueCount = $this->borrowRequestBookModel->countOverdueRequests();
        
        require_once __DIR__ . '/../views/admin/statistic.php';
    }

    public function overdueList()
    {
        Auth::admin();
        $overdueRequests = $this->borrowRequestBookModel->getOverdueRequests();
        $pendingReminderRequests = $this->borrowRequestBookModel->getOverdueRequestsPendingReminder(2);
        $totalOverdue = count($overdueRequests);
        $totalPendingReminder = count($pendingReminderRequests);

        require dirname(__DIR__, 2) . '/app/views/admin/overdueList.php';
    }

    public function sendReminder($id)
    {
        Auth::admin();
        if (!$id) {
            header("Location: index.php?action=admin_overdue_list&msg=invalid");
            exit();
        }

        $result = $this->borrowRequestBookModel->sendOverdueReminderEmail($id);
        if ($result) {
            header("Location: index.php?action=admin_overdue_list&msg=reminder_success");
        } else {
            header("Location: index.php?action=admin_overdue_list&msg=reminder_error");
        }
        exit();
    }

    public function batchSendReminders()
    {
        Auth::admin();
        $result = $this->borrowRequestBookModel->sendAllPendingOverdueReminders(2);
        $_SESSION['batch_reminder_result'] = $result;
        header("Location: index.php?action=admin_overdue_list&msg=batch_completed");
        exit();
    }
}
?>