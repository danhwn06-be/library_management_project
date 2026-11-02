<?php

require_once __DIR__ . '/../models/borrowRequest.php';
require_once __DIR__ . '/../models/category.php';
require_once dirname(__DIR__) . '/core/Auth.php';

class BorrowController
{
    private $db;
    private $requestModel;

    private $categoryModel;

    public function __construct($dbConnection)
    {
        // Nhận biến kết nối từ index.php truyền sang
        $this->db = $dbConnection;
        $this->requestModel = new BorrowRequest($this->db);
        $this->categoryModel = new Category($this->db);
    }

    /**
     * Thêm sách vào danh sách chờ mượn (Session)
     */
    private function checkLogin()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?action=login");
            exit();
        }
    }


    public function addToMyBook($bookId, $bookTitle, $author, $image)
    {
        $this->checkLogin();

        // Kiểm tra tồn kho trước khi thêm vào giỏ (OI-16)
        $stmt = $this->db->prepare("SELECT stock_quantity FROM books WHERE book_id = ?");
        $stmt->execute([$bookId]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$book || (int)$book['stock_quantity'] <= 0) {
            echo "<script>alert('Sorry, this book is currently out of stock!'); window.history.back();</script>";
            exit();
        }

        $this->initCart();

        if ($this->isBookInCart($bookId)) {
            echo "<script>alert('This book is already in My book!'); window.history.back();</script>";
        } else {
            $this->addNewBookToCart($bookId, $bookTitle, $author, $image);
            echo "<script>alert('Added to My book!'); window.history.back();</script>";
        }
        exit();
    }

    // Private Helper Methods //

    private function initCart()
    {
        if (!isset($_SESSION['my_book_cart'])) {
            $_SESSION['my_book_cart'] = [];
        }
    }

    private function isBookInCart($bookId)
    {
        return isset($_SESSION['my_book_cart'][$bookId]);
    }

    private function addNewBookToCart($bookId, $bookTitle, $author, $image)
    {
        $_SESSION['my_book_cart'][$bookId] = [
            'id'       => $bookId,
            'title'    => $bookTitle,
            'author'   => $author,
            'image'    => $image,
            'quantity' => 1
        ];
    }
    
    // Hiển thị Form điền thông tin mượn //
    public function showFormBookRequest()
    {
        require_once __DIR__ . '/../views/books/formBookBorrowRequest.php';
    }

    // Hiển thị trang danh sách sách đã chọn //
    public function showMyBook()
    {
        $listBooks = isset($_SESSION['my_book_cart']) ? $_SESSION['my_book_cart'] : [];
        $categories = $this->categoryModel->getAllCategories();
        require_once __DIR__ . '/../views/books/myBook.php';
    }

    // Xử lý gửi yêu cầu mượn vào Database //    
    public function submitRequest()
    {
        Auth::user();

        $this->checkLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=mybook");
            exit();
        }

        $borrowData = $this->getBorrowFormData();
        $books = $_SESSION['my_book_cart'] ?? [];

        if (empty($books)) {
            $this->showMessage('Your borrowed book list is empty!', true);
        }

        // Validate số lượng sách tối đa 5 cuốn (OI-16)
        $totalQuantity = 0;
        foreach ($books as $b) {
            $totalQuantity += (int)$b['quantity'];
        }
        if ($totalQuantity > 5) {
            $this->showMessage('You can only borrow a maximum of 5 books per request!', true);
        }

        // Validate ngày mượn và ngày trả (OI-16)
        $bTime = strtotime(date('Y-m-d', strtotime($borrowData['borrowDate'])));
        $rTime = strtotime(date('Y-m-d', strtotime($borrowData['returnDate'])));

        if (!$rTime || $rTime < $bTime) {
            $this->showMessage('Return date cannot be earlier than borrow date!', true);
        }

        $durationDays = ($rTime - $bTime) / (60 * 60 * 24);
        if ($durationDays > 14) {
            $this->showMessage('Borrow duration cannot exceed 14 days!', true);
        }

        $isSuccess = $this->requestModel->createRequest(
            $borrowData['userId'],
            $borrowData['name'],
            $borrowData['phone'],
            $borrowData['address'],
            $borrowData['borrowDate'],
            $borrowData['returnDate'],
            $books,
            $borrowData['note']
        );

        if ($isSuccess) {
            unset($_SESSION['my_book_cart']);
            $this->showMessage(
                'Your request to borrow has been submitted!',
                false,
                'index.php?action=listbook'
            );
        } else {
            $this->showMessage('Failed to save data. Please try again!', true);
        }
    }




    // --- Các hàm hỗ trợ (Private Helper Methods) ---

    private function getBorrowFormData()
    {
        Auth::user();

        // Thời gian mượn: Lấy chính xác thời điểm thực tế bản ghi được tạo
        $borrowDateTime = date('Y-m-d H:i:s');

        // Thời gian hẹn trả: Lấy ngày chọn từ form và gán mốc cuối ngày (23:59:59)
        $returnDateInput = $_POST['return_date'] ?? '';
        $returnDateTime = !empty($returnDateInput) ? $returnDateInput . ' 23:59:59' : '';

        return [
            'userId'     => $_SESSION['user']['id'],
            'name'       => $_POST['name'] ?? '',
            'phone'      => $_POST['phone'] ?? '',
            'address'    => $_POST['address'] ?? '',
            'borrowDate' => $borrowDateTime,
            'returnDate' => $returnDateTime,
            'note'       => trim($_POST['note'] ?? '')
        ];
    }

    private function showMessage($msg, $isBack = true, $redirectUrl = '')
    {
        Auth::user();

        echo "<script>alert('$msg');";
        if ($isBack) {
            echo "window.history.back();";
        } elseif ($redirectUrl !== '') {
            echo "window.location.href = '$redirectUrl';";
        }
        echo "</script>";
        exit();
    }

    /**
     * Xóa sách khỏi danh sách mượn tạm (Session)
     */
    public function removeFromCart()
    {
        Auth::user();

        $id = $_GET['id'] ?? null;
        if ($id && isset($_SESSION['my_book_cart'][$id])) {
            unset($_SESSION['my_book_cart'][$id]);
            echo "Success";
        } else {
            echo "Error";
        }
        exit(); // Rất quan trọng: Dừng chương trình để không load thêm HTML thừa
    }

    /**
     * Cập nhật số lượng sách trong danh sách mượn (Session)
     */
    public function updateCartQty()
    {
        Auth::user();

        $id = $_GET['id'] ?? null;
        $qty = (int)($_GET['qty'] ?? 1);

        if ($id && isset($_SESSION['my_book_cart'][$id]) && $qty > 0) {
            $_SESSION['my_book_cart'][$id]['quantity'] = $qty;
            echo "Updated";
        } else {
            echo "Error";
        }
        exit(); // Ngắt kết nối để trả phản hồi sạch về cho Javascript
    }

    /**
     * Hiển thị lịch sử mượn sách của người dùng
     */
    public function history()
    {
        $this->checkLogin();
        Auth::user();

        $userId = $_SESSION['user']['id'];
        $categories = $this->categoryModel->getAllCategories();
        $history = $this->requestModel->getHistoryByUserId($userId);

        $successMsg = $_SESSION['history_success'] ?? null;
        $errorMsg = $_SESSION['history_error'] ?? null;
        unset($_SESSION['history_success'], $_SESSION['history_error']);

        require_once dirname(__DIR__) . '/views/user/history.php';
    }

    /**
     * Hủy yêu cầu mượn khi đang ở trạng thái Pending
     */
    public function cancelRequest()
    {
        $this->checkLogin();
        Auth::user();

        $requestId = $_GET['id'] ?? null;
        if ($requestId) {
            $isSuccess = $this->requestModel->cancelRequest($requestId, $_SESSION['user']['id']);
            if ($isSuccess) {
                $_SESSION['history_success'] = "Canceled borrow request #$requestId successfully!";
            } else {
                $_SESSION['history_error'] = "Unable to cancel borrow request #$requestId!";
            }
        }
        header("Location: index.php?action=history");
        exit();
    }
}
