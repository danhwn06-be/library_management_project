CREATE DATABASE IF NOT EXISTS `LIBRARY_MANAGEMENT` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `LIBRARY_MANAGEMENT`;

-- ==========================================
-- 1. Bảng Categories (Danh mục sách)
-- ==========================================
CREATE TABLE `categories` (
  `categories_id` INT AUTO_INCREMENT PRIMARY KEY,
  `categories_name` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- 2. Bảng Books (Đầu sách)
-- ==========================================
CREATE TABLE `books` (
  `book_id` INT AUTO_INCREMENT PRIMARY KEY,
  `book_title` VARCHAR(255) NOT NULL,
  `author` VARCHAR(255),
  `categories_id` INT,
  `publisher` VARCHAR(255),
  `publish_year` INT,
  `price` DECIMAL(10,2) DEFAULT 0.00,
  `stock_quantity` INT DEFAULT 0,
  `image_url` VARCHAR(255),
  `content` TEXT,
  FOREIGN KEY (`categories_id`) REFERENCES `categories`(`categories_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- 3. Bảng Book Copies (Các bản sao sách vật lý thực tế)
-- ==========================================
CREATE TABLE `book_copies` (
  `barcode` VARCHAR(100) PRIMARY KEY,
  `book_id` INT NOT NULL,
  FOREIGN KEY (`book_id`) REFERENCES `books`(`book_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- 4. Bảng Users (Người dùng / Thành viên)
-- ==========================================
CREATE TABLE `users` (
  `user_id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(255) NOT NULL,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `phone` VARCHAR(20),
  `password` VARCHAR(255) NOT NULL,
  `address` VARCHAR(255),
  `gender` VARCHAR(10),
  `date_of_birth` DATE,
  `role` TINYINT DEFAULT 0 COMMENT '0: User thường, 1: Admin/Thủ thư',
  `is_locked` TINYINT DEFAULT 0 COMMENT '0: Bình thường, 1: Bị khoá'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- 5. Bảng Borrow Requests (Yêu cầu mượn sách)
-- ==========================================
CREATE TABLE `borrow_requests` (
  `borrow_request_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `full_name` VARCHAR(255) NOT NULL COMMENT 'Tên người nhận tại thời điểm mượn',
  `phone` VARCHAR(20) COMMENT 'Số điện thoại tại thời điểm mượn',
  `address` VARCHAR(255) COMMENT 'Địa chỉ tại thời điểm mượn',
  `request_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `schedule_return_date` DATETIME,
  `actual_return_date` DATETIME NULL,
  `quantity` INT DEFAULT 0 COMMENT 'Tổng số lượng sách trong phiếu mượn',
  `request_status` VARCHAR(50) DEFAULT 'Pending' COMMENT 'Pending, Approved, Rejected, Returned',
  `note` TEXT NULL COMMENT 'Ghi chú của người mượn',
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- 6. Bảng Borrow Request Books (Chi tiết sách trong yêu cầu mượn)
-- ==========================================
CREATE TABLE `borrow_request_books` (
  `borrow_request_id` INT NOT NULL,
  `book_id` INT NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  PRIMARY KEY (`borrow_request_id`, `book_id`),
  FOREIGN KEY (`borrow_request_id`) REFERENCES `borrow_requests`(`borrow_request_id`) ON DELETE CASCADE,
  FOREIGN KEY (`book_id`) REFERENCES `books`(`book_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- 7. DỮ LIỆU MẪU (Bổ sung để dễ test)
-- ==========================================
INSERT INTO `users` (`full_name`, `username`, `email`, `password`, `role`, `is_locked`) VALUES 
('Quản trị viên', 'admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 0); 
-- Mật khẩu mặc định: 'password' (đã hash bằng Bcrypt)