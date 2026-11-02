<?php


require_once __DIR__ . '/../../public/lib/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../../public/lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../public/lib/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;




class BookCopies
{
    private $db;
    public function __construct($db)
    {
        $this->db = $db;
    }
    public function getItems($id)
{
    $sql = "SELECT 
                b.book_id,
                b.book_title,
                b.author,
                brb.quantity,
                c.categories_name AS category,
                bc.barcode -- Lấy trực tiếp barcode từ bảng copies
            FROM borrow_request_books brb
            JOIN books b ON brb.book_id = b.book_id
            LEFT JOIN categories c ON b.categories_id = c.categories_id
            -- Kết nối với bảng copies để lấy barcode đầu tiên khả dụng
            LEFT JOIN (
                SELECT book_id, MIN(barcode) as barcode 
                FROM book_copies 
                GROUP BY book_id
            ) bc ON b.book_id = bc.book_id
            WHERE brb.borrow_request_id = ?
            GROUP BY b.book_id, b.book_title, b.author, brb.quantity, c.categories_name, bc.barcode";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);

}

private function modifyStock($book_id, $quantity, $operation)
{
    $sql = "UPDATE books 
            SET quantity = quantity " . ($operation === '+' ? '+' : '-') . " ? 
            WHERE book_id = ?";
    $this->db->prepare($sql)->execute([$quantity, $book_id]);
}
}