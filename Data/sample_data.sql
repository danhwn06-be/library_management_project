-- ==========================================
-- DỮ LIỆU MẪU - chạy sau khi đã có DATABASE.sql
-- ==========================================

USE `LIBRARY_MANAGEMENT`;

-- Categories (Danh mục sách)
INSERT INTO `categories` (`categories_name`) VALUES
('Văn học'),
('Khoa học'),
('Lịch sử'),
('Kỹ năng sống'),
('Kinh tế'),
('Thiếu nhi');

-- Books (Sách)
INSERT INTO `books` (`book_title`, `author`, `categories_id`, `publisher`, `publish_year`, `price`, `stock_quantity`, `image_url`, `content`) VALUES
('Tôi thấy hoa vàng trên cỏ xanh', 'Nguyễn Nhật Ánh', 1, 'NXB Trẻ', 2010, 85000, 12, 'default.png', 'Cuốn tiểu thuyết kể về tuổi thơ trong sáng tại một vùng quê'),
('Dế mèn phiêu lưu ký', 'Tô Hoài', 1, 'NXB Kim Đồng', 1941, 45000, 8, 'default.png', 'Truyện thiếu nhi nổi tiếng về chú dế mèn'),
('Nhà giả kim', 'Paulo Coelho', 4, 'NXB Hội Nhà Văn', 2013, 79000, 15, 'default.png', 'Cuốn sách truyền cảm hứng về hành trình theo đuổi ước mơ'),
('Sapiens: Lược sử loài người', 'Yuval Noah Harari', 3, 'NXB Thế Giới', 2017, 189000, 6, 'default.png', 'Tác phẩm tái hiện lịch sử loài người từ thời tiền sử'),
('Đắc nhân tâm', 'Dale Carnegie', 4, 'NXB Tổng Hợp', 2016, 89000, 20, 'default.png', 'Nghệ thuật giao tiếp và thuyết phục người khác'),
('Tư duy nhanh và chậm', 'Daniel Kahneman', 5, 'NXB Thế Giới', 2018, 145000, 9, 'default.png', 'Khám phá hai hệ thống tư duy của con người'),
('Vũ trụ trong vỏ hạt dẻ', 'Stephen Hawking', 2, 'NXB Trẻ', 2005, 120000, 5, 'default.png', 'Khoa học vũ trụ giải thích dễ hiểu'),
('Doraemon tập 1', 'Fujiko F. Fujio', 6, 'NXB Kim Đồng', 1995, 25000, 30, 'default.png', 'Truyện tranh thiếu nhi nổi tiếng Nhật Bản'),
('Kỹ năng lãnh đạo', 'John C. Maxwell', 5, 'NXB Lao Động', 2019, 99000, 7, 'default.png', 'Phát triển kỹ năng lãnh đạo hiệu quả'),
('Lịch sử Việt Nam', 'Nhiều tác giả', 3, 'NXB Giáo Dục', 2020, 250000, 4, 'default.png', 'Toàn bộ lịch sử dân tộc Việt Nam'),
('Vật lý vui', 'Yakov Perelman', 2, 'NXB Khoa Học', 2015, 65000, 11, 'default.png', 'Các thí nghiệm và câu đố vật lý thú vị'),
('Cây cam ngọt của tôi', 'José Mauro de Vasconcelos', 1, 'NXB Hội Nhà Văn', 2020, 95000, 13, 'default.png', 'Câu chuyện cảm động về tuổi thơ');

-- Users mẫu (thêm 3 user thường)
INSERT INTO `users` (`full_name`, `username`, `email`, `phone`, `password`, `address`, `gender`, `date_of_birth`, `role`, `is_locked`) VALUES
('Nguyễn Văn An', 'nguyenvanan', 'an@example.com', '0901234567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hà Nội', 'Nam', '2000-05-15', 0, 0),
('Trần Thị Bình', 'tranthihinh', 'binh@example.com', '0912345678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'TP.HCM', 'Nữ', '2001-08-20', 0, 0),
('Lê Văn Cường', 'levancuong', 'cuong@example.com', '0923456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Đà Nẵng', 'Nam', '1999-03-10', 0, 0);

-- Borrow Requests mẫu
INSERT INTO `borrow_requests` (`user_id`, `full_name`, `phone`, `address`, `request_date`, `schedule_return_date`, `quantity`, `request_status`) VALUES
(2, 'Nguyễn Văn An', '0901234567', 'Hà Nội', NOW(), DATE_ADD(NOW(), INTERVAL 14 DAY), 2, 'Pending'),
(3, 'Trần Thị Bình', '0912345678', 'TP.HCM', NOW(), DATE_ADD(NOW(), INTERVAL 14 DAY), 1, 'Approved'),
(4, 'Lê Văn Cường', '0923456789', 'Đà Nẵng', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_ADD(NOW(), INTERVAL 9 DAY), 3, 'Pending');

-- Chi tiết sách trong phiếu mượn
INSERT INTO `borrow_request_books` (`borrow_request_id`, `book_id`, `quantity`) VALUES
(1, 1, 1),
(1, 3, 1),
(2, 5, 1),
(3, 2, 1),
(3, 4, 1),
(3, 7, 1);
