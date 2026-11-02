<?php

// Nhảy ra khỏi models, nhảy ra khỏi app, đi vào public/lib
require_once __DIR__ . '/../../public/lib/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../../public/lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../public/lib/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



class BorrowRequestBook
{
    private $db;
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll()
    {
        $sql = "SELECT br.borrow_request_id AS id, br.full_name, br.address, br.phone, 
                       br.request_date, br.schedule_return_date, br.request_status AS status, br.note
                FROM borrow_requests br
                ORDER BY br.borrow_request_id DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT br.borrow_request_id AS id, br.user_id, br.full_name, br.phone, br.address, 
                       br.request_date, br.schedule_return_date, br.request_status AS status, br.note
                FROM borrow_requests br 
                WHERE br.borrow_request_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getItems($requestId)
    {
        $sql = "SELECT book_id, quantity FROM borrow_request_books WHERE borrow_request_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$requestId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

public function modifyStock($bookId, $quantity, $operator)
{
    $sql = "UPDATE books SET stock_quantity = stock_quantity $operator ? WHERE book_id = ?";
    if ($operator === '-') $sql .= " AND stock_quantity >= ?";
    
    $params = ($operator === '-') ? [$quantity, $bookId, $quantity] : [$quantity, $bookId];
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    if ($operator === '-' && $stmt->rowCount() == 0) {
        throw new Exception("Sách ID $bookId không đủ số lượng.");
    }
}

public function handleNotifications($requestId, $status) {
    if (!in_array($status, ['Approved', 'Rejected'])) return;
    
    $sqlUser = "SELECT u.email, u.full_name FROM borrow_requests br 
                JOIN users u ON br.user_id = u.user_id WHERE br.borrow_request_id = ?";
    $stmt = $this->db->prepare($sqlUser);
    $stmt->execute([$requestId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $this->sendBorrowStatusEmail($user['email'], $user['full_name'], $status);
    }
}
public function updateStatus($id, $status)
{
    $current = $this->getById($id);
    if (!$current) return false;
    
    $currentStatus = $current['status'];
    if ($currentStatus === 'Pending') {
        if (!in_array($status, ['Approved', 'Rejected'])) return false;
    } elseif ($currentStatus === 'Approved') {
        if ($status !== 'Returned') return false;
    } else {
        return false;
    }

    try {
        $this->db->beginTransaction();

        // 1. Cập nhật trạng thái và ngày trả thực tế nếu là Returned
        $sql = "UPDATE borrow_requests 
                SET request_status = ?, 
                    actual_return_date = CASE WHEN ? = 'Returned' THEN NOW() ELSE actual_return_date END 
                WHERE borrow_request_id = ?";
        $this->db->prepare($sql)->execute([$status, $status, $id]);

        // 2. Xử lý kho (Stock)
        $items = $this->getItems($id);
        foreach ($items as $item) {
            if ($status === 'Approved') {
                $this->modifyStock($item['book_id'], $item['quantity'], '-');
            } elseif ($status === 'Returned') {
                $this->modifyStock($item['book_id'], $item['quantity'], '+');
            }
        }

        // 3. Gửi Email (Chỉ cho Approved/Rejected)
        $this->handleNotifications($id, $status);

        $this->db->commit();
        return true;
    } catch (Exception $e) {
        $this->db->rollBack();
        return false;
    }
}

    public function sendBorrowStatusEmail($email, $name, $status)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom(SMTP_USERNAME, 'TVAN Library');
            $mail->addAddress($email, $name);

            $mail->isHTML(true);

            if ($status === 'Approved') {
                $mail->Subject = 'Borrow Request Approved';
                $mail->Body = "
                <p>Hello <b>$name</b>,</p>
                <p>Your book borrowing request has been 
                <b style='color:green;'>APPROVED</b>.</p>
                <p>Please come to the library to collect your books.</p>
                <p>— TVAN Library</p>
            ";
            } elseif ($status === 'Rejected') {
                $mail->Subject = 'Borrow Request Rejected';
                $mail->Body = "
                <p>Hello <b>$name</b>,</p>
                <p>Your book borrowing request has been 
                <b style='color:red;'>REJECTED</b>.</p>
                <p>Please contact the library for more details.</p>
                <p>— TVAN Library</p>
            ";
            }

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Lấy danh sách các phiếu mượn đang quá hạn (status = 'Approved' và schedule_return_date < NOW())
     */
    public function getOverdueRequests()
    {
        $sql = "SELECT br.borrow_request_id AS id, br.user_id, br.full_name, br.phone, br.address, 
                       br.request_date, br.schedule_return_date, br.request_status AS status, br.note,
                       br.last_reminder_date,
                       DATEDIFF(NOW(), br.schedule_return_date) AS overdue_days,
                       u.email, u.username
                FROM borrow_requests br
                JOIN users u ON br.user_id = u.user_id
                WHERE br.request_status = 'Approved' 
                  AND br.schedule_return_date < NOW()
                ORDER BY overdue_days DESC, br.schedule_return_date ASC";
        $stmt = $this->db->query($sql);
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy thông tin sách cho từng phiếu mượn
        foreach ($requests as &$req) {
            $sqlBooks = "SELECT b.book_id, b.book_title, brb.quantity 
                         FROM borrow_request_books brb
                         JOIN books b ON brb.book_id = b.book_id
                         WHERE brb.borrow_request_id = ?";
            $bookStmt = $this->db->prepare($sqlBooks);
            $bookStmt->execute([$req['id']]);
            $req['books'] = $bookStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        unset($req);

        return $requests;
    }

    /**
     * Lấy các phiếu mượn quá hạn cần gửi nhắc nhở tự động
     * Điều kiện: Chưa từng gửi nhắc nhở HOẶC lần gửi trước đã cách đây ít nhất $intervalDays ngày
     */
    public function getOverdueRequestsPendingReminder($intervalDays = 3)
    {
        $sql = "SELECT br.borrow_request_id AS id, br.user_id, br.full_name, br.phone, br.address, 
                       br.request_date, br.schedule_return_date, br.request_status AS status, br.note,
                       br.last_reminder_date,
                       DATEDIFF(NOW(), br.schedule_return_date) AS overdue_days,
                       u.email, u.username
                FROM borrow_requests br
                JOIN users u ON br.user_id = u.user_id
                WHERE br.request_status = 'Approved' 
                  AND br.schedule_return_date < NOW()
                  AND (br.last_reminder_date IS NULL OR br.last_reminder_date < NOW() - INTERVAL ? DAY)
                ORDER BY overdue_days DESC, br.schedule_return_date ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$intervalDays]);
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($requests as &$req) {
            $sqlBooks = "SELECT b.book_id, b.book_title, brb.quantity 
                         FROM borrow_request_books brb
                         JOIN books b ON brb.book_id = b.book_id
                         WHERE brb.borrow_request_id = ?";
            $bookStmt = $this->db->prepare($sqlBooks);
            $bookStmt->execute([$req['id']]);
            $req['books'] = $bookStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        unset($req);

        return $requests;
    }

    /**
     * Đếm tổng số phiếu mượn quá hạn
     */
    public function countOverdueRequests()
    {
        $sql = "SELECT COUNT(*) FROM borrow_requests WHERE request_status = 'Approved' AND schedule_return_date < NOW()";
        $stmt = $this->db->query($sql);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Gửi email nhắc nhở trả sách cho người dùng mượn quá hạn
     */
    public function sendOverdueReminderEmail($requestId)
    {
        $sql = "SELECT br.borrow_request_id, br.full_name, br.schedule_return_date,
                       DATEDIFF(NOW(), br.schedule_return_date) AS overdue_days,
                       u.email
                FROM borrow_requests br
                JOIN users u ON br.user_id = u.user_id
                WHERE br.borrow_request_id = ? AND br.request_status = 'Approved'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$requestId]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$request) {
            return false;
        }

        // Lấy danh sách sách đang mượn
        $sqlBooks = "SELECT b.book_title, brb.quantity 
                     FROM borrow_request_books brb
                     JOIN books b ON brb.book_id = b.book_id
                     WHERE brb.borrow_request_id = ?";
        $bookStmt = $this->db->prepare($sqlBooks);
        $bookStmt->execute([$requestId]);
        $books = $bookStmt->fetchAll(PDO::FETCH_ASSOC);

        $bookListHtml = "<ul style='padding-left: 20px; line-height: 1.8;'>";
        foreach ($books as $b) {
            $bookListHtml .= "<li><b>" . htmlspecialchars($b['book_title']) . "</b> (Quantity: " . (int)$b['quantity'] . ")</li>";
        }
        $bookListHtml .= "</ul>";

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom(SMTP_USERNAME, 'TVAN Library');
            $mail->addAddress($request['email'], $request['full_name']);

            $mail->isHTML(true);
            $mail->Subject = '[TVAN Library] Reminder: Overdue Book Loan Notice';
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px; padding: 24px;'>
                    <h2 style='color: #dc3545; border-bottom: 2px solid #dc3545; padding-bottom: 8px;'>OVERDUE BOOK RETURN NOTICE</h2>
                    <p>Dear <b>" . htmlspecialchars($request['full_name']) . "</b>,</p>
                    <p>TVAN Library records indicate that your borrow request <b>#" . $request['borrow_request_id'] . "</b> is currently overdue by 
                    <span style='color: #dc3545; font-weight: bold;'>" . (int)$request['overdue_days'] . " days</span> 
                    (Scheduled return date: <b>" . date('M d, Y H:i', strtotime($request['schedule_return_date'])) . "</b>).</p>
                    
                    <p><b>Borrowed Books:</b></p>
                    " . $bookListHtml . "
                    
                    <div style='background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 12px; margin: 16px 0;'>
                        <p style='margin: 0; color: #856404;'><b>Notice:</b> Please visit the library and return the books as soon as possible to avoid account suspension or late return penalties.</p>
                    </div>

                    <p>If you have already returned these books within the last 24 hours, please disregard this notice or contact the library librarian.</p>
                    <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                    <p style='color: #6c757d; font-size: 0.9em; margin-bottom: 0;'>
                        <b>TVAN Library</b><br>
                        Support Email: " . SMTP_USERNAME . "
                    </p>
                </div>
            ";

            $mail->send();

            // Cập nhật ngày gửi nhắc nhở gần nhất vào CSDL
            $updateSql = "UPDATE borrow_requests SET last_reminder_date = NOW() WHERE borrow_request_id = ?";
            $this->db->prepare($updateSql)->execute([$requestId]);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Quét và gửi nhắc nhở cho tất cả các phiếu mượn quá hạn đủ điều kiện
     * Được kích hoạt bởi nút bấm của Admin hoặc Cron Job
     */
    public function sendAllPendingOverdueReminders($intervalDays = 2)
    {
        $pendingRequests = $this->getOverdueRequestsPendingReminder($intervalDays);
        $total = count($pendingRequests);

        $success = 0;
        $failed = 0;
        $details = [];

        // Ghi log thực hiện
        $logDir = dirname(__DIR__, 2) . '/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $logFile = $logDir . '/cron_reminder.log';
        $timestamp = date('[Y-m-d H:i:s]');
        file_put_contents($logFile, "$timestamp [ADMIN_TRIGGER] Process started. Found $total eligible request(s).\n", FILE_APPEND);

        foreach ($pendingRequests as $req) {
            $reqId = $req['id'];
            $email = $req['email'];
            $name = $req['full_name'];

            $isSent = $this->sendOverdueReminderEmail($reqId);
            if ($isSent) {
                $success++;
                $statusText = "SUCCESS";
                file_put_contents($logFile, "$timestamp   -> [SUCCESS] Reminder sent to $email (Request #$reqId)\n", FILE_APPEND);
            } else {
                $failed++;
                $statusText = "FAILED";
                file_put_contents($logFile, "$timestamp   -> [FAILED] Could not send to $email (Request #$reqId)\n", FILE_APPEND);
            }

            $details[] = [
                'id' => $reqId,
                'name' => $name,
                'email' => $email,
                'status' => $statusText
            ];
        }

        file_put_contents($logFile, "$timestamp [ADMIN_TRIGGER] Process finished. Success: $success, Failed: $failed.\n", FILE_APPEND);

        return [
            'total_eligible' => $total,
            'success_count' => $success,
            'failed_count' => $failed,
            'details' => $details
        ];
    }
}