<?php
/**
 * CLI RUNNER: Overdue Book Loan Reminder (CLI Support for UC-27)
 * Project: TVAN Library Management System (v1.0.0-RELEASE)
 * 
 * PHẠM VI HỆ THỐNG VÀ ĐẶC TẢ NGHIỆP VỤ:
 * - Hệ thống TVAN Library có tổng cộng 27 Use Case (UC-01 đến UC-27).
 * - Tệp này là công cụ dòng lệnh (CLI Runner / Utility Script) hỗ trợ thực thi
 *   cho Use Case UC-27 ("Gửi email nhắc nhở quá hạn").
 * - LƯU Ý: Đây KHÔNG PHẢI là một Use Case riêng biệt (KHÔNG CÓ UC-28).
 *   Trong phạm vi nghiệp vụ hệ thống (SRS), UC-27 được kích hoạt chủ động bởi 
 *   Admin thông qua nút bấm trên giao diện Web ("Run Overdue Reminders Now").
 *   Tệp script này cung cấp thêm phương thức chạy qua Terminal (CLI) khi cần 
 *   kiểm thử nhanh hoặc cấu hình tự động khi triển khai lên VPS/Server 24/7.
 * 
 * QUY TẮC NGHIỆP VỤ (Khớp với UC-27 & BR-16 -> BR-19):
 * - Chỉ quét các đơn mượn Approved có schedule_return_date < NOW().
 * - Bộ lọc chống spam: Khoảng cách giữa 2 lần gửi nhắc nhở >= 2 ngày.
 * - Bảo toàn 100% tồn kho sách (stock_quantity) và trạng thái đơn mượn (Approved).
 * - Cập nhật last_reminder_date = NOW() sau khi gửi email thành công.
 * - Ghi vết nhật ký vào logs/cron_reminder.log.
 * 
 * CÁCH SỬ DỤNG:
 * - Chạy thủ công qua CLI: php cron/cron_overdue_reminders.php
 */

// Đảm bảo chỉ thực thi qua môi trường dòng lệnh (CLI)
if (php_sapi_name() !== 'cli') {
    die("This script can only be executed via the command line (CLI).\n");
}

date_default_timezone_set('Asia/Ho_Chi_Minh');

$rootDir = dirname(__DIR__);
require_once $rootDir . '/config/config.php';
require_once $rootDir . '/app/models/borrowRequestBook.php';

$logDir = $rootDir . '/logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}
$logFile = $logDir . '/cron_reminder.log';

function writeLog($message) {
    global $logFile;
    $timestamp = date('[Y-m-d H:i:s]');
    $formatted = "$timestamp $message\n";
    file_put_contents($logFile, $formatted, FILE_APPEND);
    echo $formatted;
}

writeLog("=========================================================");
writeLog("CLI RUNNER STARTED: Overdue Reminder Process (UC-27 Support)");
writeLog("=========================================================");

$database = new Database();
$db = $database->connect();

if (!$db) {
    writeLog("[FATAL] Could not connect to MySQL database!");
    exit(1);
}

$borrowModel = new BorrowRequestBook($db);

// Tần suất nhắc nhở chống spam: Cách nhau ít nhất 2 ngày (BR-17)
$intervalDays = 2;
$pendingRequests = $borrowModel->getOverdueRequestsPendingReminder($intervalDays);
$totalPending = count($pendingRequests);

writeLog("Found $totalPending overdue request(s) eligible for reminder (interval >= $intervalDays days).");

if ($totalPending === 0) {
    writeLog("No overdue borrowers need reminders today. Process finished.");
    writeLog("CLI PROCESS COMPLETED (UC-27).");
    writeLog("=========================================================\n");
    exit(0);
}

$successCount = 0;
$failCount = 0;

foreach ($pendingRequests as $index => $req) {
    $reqId = $req['id'];
    $fullName = $req['full_name'];
    $email = $req['email'];
    $overdueDays = $req['overdue_days'];
    $bookCount = count($req['books']);

    writeLog("Processing [" . ($index + 1) . "/$totalPending]: Request #$reqId | $fullName ($email) | Overdue: $overdueDays days | Books: $bookCount");

    try {
        $sent = $borrowModel->sendOverdueReminderEmail($reqId);

        if ($sent) {
            $successCount++;
            writeLog("  -> [SUCCESS] Reminder email sent to $email (Request #$reqId).");
        } else {
            $failCount++;
            writeLog("  -> [FAILED] PHPMailer could not deliver email to $email (Request #$reqId).");
        }
    } catch (Exception $e) {
        $failCount++;
        writeLog("  -> [ERROR] Exception for Request #$reqId: " . $e->getMessage());
    }
}

writeLog("SUMMARY (UC-27): Total eligible: $totalPending | Successfully sent: $successCount | Failed: $failCount");
writeLog("CLI PROCESS COMPLETED SUCCESSFULLY (UC-27).");
writeLog("=========================================================\n");

exit($failCount > 0 ? 1 : 0);
