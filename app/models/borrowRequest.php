<?php

class BorrowRequest
{
    private $db;
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createRequest($userId, $name, $phone, $address, $borrowDate, $returnDate, $books, $note = null)
    {
        try {
            $this->db->beginTransaction();

            // 1. Tính tổng số lượng sách
            $totalQty = $this->calculateTotalQuantity($books);

            // 2. Chèn đơn mượn chính và lấy ID
            $requestId = $this->insertBorrowRequest($userId, $name, $phone, $address, $borrowDate, $returnDate, $totalQty, $note);

            // 3. Xử lý từng cuốn sách (Lưu chi tiết)
            foreach ($books as $book) {
                $this->insertRequestDetail($requestId, $book['id'], $book['quantity']);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            // Ghi log lỗi hoặc die để debug nếu cần
            // error_log($e->getMessage());
            return false;
        }
    }

    // --- Các hàm hỗ trợ riêng biệt (Private Helpers) ---

    private function calculateTotalQuantity($books)
    {
        $total = 0;
        foreach ($books as $book) {
            $total += $book['quantity'];
        }
        return $total;
    }

    private function insertBorrowRequest($userId, $name, $phone, $address, $bDate, $rDate, $totalQty, $note = null)
    {
        $query = "INSERT INTO borrow_requests (user_id, full_name, phone, address, request_date, schedule_return_date, quantity, request_status, note) 
                  VALUES (:user_id, :name, :phone, :address, :b_date, :r_date, :total_qty, 'Pending', :note)";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':user_id'   => $userId,
            ':name'      => $name,
            ':phone'     => $phone,
            ':address'   => $address,
            ':b_date'    => $bDate,
            ':r_date'    => $rDate,
            ':total_qty' => $totalQty,
            ':note'      => $note
        ]);

        return $this->db->lastInsertId();
    }

    private function insertRequestDetail($requestId, $bookId, $qty)
    {
        $query = "INSERT INTO borrow_request_books (borrow_request_id, book_id, quantity) 
                  VALUES (:request_id, :book_id, :qty)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':request_id' => $requestId,
            ':book_id'    => $bookId,
            ':qty'         => $qty
        ]);
    }

    private function updateBookStock($bookId, $qty)
    {
        $query = "UPDATE books SET stock_quantity = stock_quantity - :qty 
                  WHERE book_id = :id AND stock_quantity >= :qty";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':qty' => $qty, ':id' => $bookId]);
        // Nếu rowCount == 0 nghĩa là không tìm thấy sách hoặc kho không đủ để trừ
        if ($stmt->rowCount() == 0) {
            throw new Exception("Sách ID $bookId không đủ số lượng trong kho.");
        }
    }

    public function getHistoryByUserId($userId)
    {
        $sql = "SELECT br.borrow_request_id, br.full_name, br.phone, br.address, 
                       br.request_date, br.schedule_return_date, br.actual_return_date, 
                       br.quantity, br.request_status, br.note 
                FROM borrow_requests br 
                WHERE br.user_id = ? 
                ORDER BY br.borrow_request_id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($requests as &$req) {
            $itemSql = "SELECT brb.quantity, b.book_id, b.book_title, b.author, b.image_url, b.price 
                        FROM borrow_request_books brb 
                        JOIN books b ON brb.book_id = b.book_id 
                        WHERE brb.borrow_request_id = ?";
            $itemStmt = $this->db->prepare($itemSql);
            $itemStmt->execute([$req['borrow_request_id']]);
            $req['books'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        unset($req);

        return $requests;
    }

    public function cancelRequest($requestId, $userId)
    {
        $stmt = $this->db->prepare("SELECT request_status FROM borrow_requests WHERE borrow_request_id = ? AND user_id = ?");
        $stmt->execute([$requestId, $userId]);
        $req = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$req || $req['request_status'] !== 'Pending') {
            return false;
        }

        $updateStmt = $this->db->prepare("UPDATE borrow_requests SET request_status = 'Rejected' WHERE borrow_request_id = ?");
        return $updateStmt->execute([$requestId]);
    }
}
?>