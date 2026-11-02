# 📚 TVAN Library Management System

Hệ thống quản lý thư viện trực tuyến xây dựng bằng **PHP thuần** theo mô hình **MVC**, cho phép người dùng tìm kiếm và đăng ký mượn sách, đồng thời cung cấp bảng điều khiển toàn diện cho Admin/Thủ thư quản lý kho sách, đơn mượn và người dùng.

---

## 🗂️ Cấu Trúc Thư Mục

```
library_management_project/
├── app/
│   ├── controllers/
│   │   ├── adminController.php      # Quản lý sách, người dùng, đơn mượn, thống kê
│   │   ├── authController.php       # Đăng nhập, đăng ký, hồ sơ cá nhân
│   │   ├── bookController.php       # Danh sách, tìm kiếm, lọc theo danh mục
│   │   ├── borrowController.php     # Giỏ mượn, gửi yêu cầu, lịch sử
│   │   └── SiteController.php       # Trang giới thiệu, liên hệ
│   ├── core/
│   │   └── Auth.php                 # Middleware xác thực phân quyền
│   ├── models/
│   │   ├── book.php                 # Model sách (CRUD, tìm kiếm, thống kê)
│   │   ├── bookCopies.php           # Model bản sao sách vật lý
│   │   ├── borrowRequest.php        # Model phiếu mượn
│   │   ├── borrowRequestBook.php    # Model chi tiết sách trong phiếu mượn
│   │   ├── category.php             # Model danh mục
│   │   └── user.php                 # Model người dùng
│   └── views/
│       ├── admin/                   # Giao diện Admin
│       │   ├── bookManagement.php   # Quản lý kho sách (có phân trang)
│       │   ├── borrowDetail.php     # Chi tiết đơn mượn
│       │   ├── borrowList.php       # Danh sách tất cả đơn mượn
│       │   ├── formAddBook.php      # Form thêm sách mới
│       │   ├── formEditBook.php     # Form chỉnh sửa sách
│       │   ├── formImportBook.php   # Form nhập sách từ Excel
│       │   ├── overdueList.php      # Danh sách đơn mượn quá hạn
│       │   ├── statistic.php        # Trang thống kê tổng quan
│       │   └── userManagement.php   # Quản lý tài khoản người dùng
│       ├── auth/
│       │   ├── login.php            # Trang đăng nhập
│       │   └── register.php         # Trang đăng ký
│       ├── books/
│       │   ├── bookListView.php     # Danh sách sách (có phân trang)
│       │   ├── filterBook.php       # Lọc sách theo danh mục
│       │   ├── formBookBorrowRequest.php # Form xác nhận thông tin mượn
│       │   ├── myBook.php           # Giỏ sách muốn mượn
│       │   ├── searchResult.php     # Kết quả tìm kiếm
│       │   └── viewBookDetail.php   # Chi tiết một cuốn sách
│       ├── layouts/
│       │   ├── header.php           # Header người dùng
│       │   ├── headerAdmin.php      # Header Admin
│       │   └── footer.php           # Footer chung
│       ├── pages/
│       │   ├── about.php            # Trang giới thiệu
│       │   └── contact.php          # Trang liên hệ
│       └── user/
│           ├── history.php          # Lịch sử mượn sách
│           └── profile.php          # Hồ sơ cá nhân
├── config/
│   └── config.php                   # Kết nối CSDL & cấu hình SMTP
├── cron/
│   └── cron_overdue_reminders.php   # Script CLI gửi email nhắc quá hạn
├── Data/
│   ├── DATABASE.sql                 # Script tạo CSDL và bảng
│   └── sample_data.sql              # Dữ liệu mẫu
├── public/
│   ├── index.php                    # Entry point – Front Controller
│   ├── css/                         # Stylesheet
│   ├── js/                          # JavaScript
│   ├── images/                      # Ảnh bìa sách (upload)
│   ├── lib/PHPMailer/               # Thư viện gửi email
│   ├── templates/
│   │   └── book_template.xlsx       # File Excel mẫu để import sách
│   └── .htaccess                    # Cấu hình Apache
├── logs/                            # Log hệ thống (cron reminder)
├── vendor/                          # Thư viện Composer (PhpSpreadsheet)
├── composer.json
└── README.md
```

---

## ✨ Tính Năng

### 👤 Người dùng (Member)
| Tính năng | Mô tả |
|---|---|
| Đăng ký / Đăng nhập | Validation đầy đủ, mật khẩu hash Bcrypt, tái tạo Session ID |
| Duyệt & tìm kiếm sách | Phân trang 20 cuốn/trang, tìm kiếm theo từ khóa |
| Lọc theo danh mục | Lọc sách theo thể loại có phân trang |
| Xem chi tiết sách | Thông tin đầy đủ: tác giả, nhà xuất bản, mô tả, tồn kho |
| Giỏ mượn (My Book) | Thêm/xóa/cập nhật số lượng sách vào giỏ (lưu Session) |
| Gửi yêu cầu mượn | Tối đa 5 cuốn/lượt, thời hạn tối đa 14 ngày |
| Lịch sử mượn | Xem toàn bộ lịch sử, hủy đơn đang chờ (Pending) |
| Hồ sơ cá nhân | Cập nhật thông tin, đổi mật khẩu |

### 🔑 Quản trị viên / Thủ thư (Admin)
| Tính năng | Mô tả |
|---|---|
| Quản lý kho sách | Thêm, sửa, xóa sách; phân trang 20 cuốn/trang |
| Import sách từ Excel | Nhập hàng loạt qua file `.xlsx` (PhpSpreadsheet) |
| Quản lý đơn mượn | Xem danh sách, duyệt (Approved), từ chối (Rejected), xác nhận trả (Returned) |
| Quản lý người dùng | Xem danh sách, khóa/mở khóa tài khoản |
| Thống kê | Tổng số sách, đơn mượn theo danh mục, số lượng quá hạn |
| Danh sách quá hạn | Lọc các đơn đã được duyệt và vượt quá ngày hẹn trả |
| Gửi email nhắc nhở | Gửi email cảnh báo quá hạn thủ công hoặc hàng loạt (chống spam: ≥ 2 ngày/lần) |

---

## 🛠️ Yêu Cầu Hệ Thống

| Thành phần | Phiên bản |
|---|---|
| PHP | ≥ 8.0 |
| MySQL / MariaDB | ≥ 5.7 |
| Web Server | Apache (với `mod_rewrite`) hoặc Nginx |
| Composer | ≥ 2.x |
| Extension PHP | `pdo_mysql`, `fileinfo`, `zip`, `mbstring` |

> **Gợi ý:** Sử dụng [XAMPP](https://www.apachefriends.org/) hoặc [Laragon](https://laragon.org/) để cài đặt nhanh trên Windows.

---

## 🚀 Hướng Dẫn Cài Đặt

### Bước 1 – Clone dự án

```bash
git clone <repository-url>
cd library_management_project
```

### Bước 2 – Cài đặt thư viện PHP

```bash
composer install
```

### Bước 3 – Tạo cơ sở dữ liệu

Mở **phpMyAdmin** hoặc MySQL client và chạy file SQL:

```sql
source Data/DATABASE.sql;
-- (Tùy chọn) Nạp dữ liệu mẫu:
source Data/sample_data.sql;
```

**Schema bao gồm 6 bảng:**

```
categories          – Danh mục sách
books               – Đầu sách
book_copies         – Bản sao sách vật lý (barcode)
users               – Tài khoản người dùng
borrow_requests     – Phiếu mượn
borrow_request_books – Chi tiết sách trong phiếu mượn
```

Tài khoản Admin mặc định sau khi chạy `DATABASE.sql`:
- **Username:** `admin` | **Email:** `admin@example.com` | **Mật khẩu:** `password`

### Bước 4 – Cấu hình kết nối CSDL

Mở file [`config/config.php`](config/config.php) và chỉnh sửa thông tin:

```php
private $host     = "localhost";
private $db_name  = "LIBRARY_MANAGEMENT";
private $username = "root";
private $password = "";          // Thay bằng mật khẩu MySQL của bạn
```

### Bước 5 – Cấu hình SMTP (Tính năng gửi email)

Trong file `config/config.php`, cấu hình thông tin email:

```php
define('SMTP_USERNAME', getenv('SMTP_USERNAME') ?: 'your_email@gmail.com');
define('SMTP_PASSWORD', getenv('SMTP_PASSWORD') ?: 'your_app_password');
```

> **Khuyến nghị:** Sử dụng **biến môi trường** (`$_ENV` hoặc `.env`) thay vì hardcode thông tin nhạy cảm vào file cấu hình, đặc biệt khi triển khai lên môi trường production.

### Bước 6 – Cấu hình thư mục ảnh

Đảm bảo thư mục `public/images/` có quyền ghi:

```bash
chmod 775 public/images/   # Linux/macOS
```

Trên Windows với XAMPP, đây thường không cần thiết.

### Bước 7 – Truy cập ứng dụng

Đặt thư mục dự án vào `htdocs` (XAMPP) hoặc `www` (Laragon), sau đó truy cập:

```
http://localhost/library_management_project/public/index.php
```

---

## 📋 Các Route Chính (`index.php?action=...`)

| Action | Mô tả | Quyền |
|---|---|---|
| `login` | Trang đăng nhập | Tất cả |
| `register` | Trang đăng ký | Tất cả |
| `listbook` | Danh sách sách (trang chủ) | User |
| `bookdetail&id={id}` | Chi tiết sách | User |
| `category&id={id}` | Lọc theo danh mục | User |
| `search&keyword={kw}` | Tìm kiếm sách | User |
| `mybook` | Giỏ sách muốn mượn | User |
| `show_borrow_form` | Form xác nhận mượn | User |
| `history` | Lịch sử mượn | User |
| `profile` | Hồ sơ cá nhân | User |
| `admin_borrow_list` | Danh sách đơn mượn | Admin |
| `book_management` | Quản lý kho sách | Admin |
| `admin_user_list` | Quản lý người dùng | Admin |
| `admin_statistic` | Thống kê tổng quan | Admin |
| `admin_overdue_list` | Danh sách quá hạn | Admin |
| `import_book_by_excel` | Import sách từ Excel | Admin |

---

## 📧 Tính Năng Gửi Email Nhắc Nhở Quá Hạn

Hệ thống hỗ trợ hai cách gửi email nhắc nhở cho người mượn quá hạn:

### 1. Qua giao diện Web (Admin)
Truy cập `Admin → Danh sách quá hạn` → nhấn nút **"Gửi nhắc nhở"** (từng đơn) hoặc **"Gửi hàng loạt"**.

### 2. Qua CLI (Script tự động)

```bash
php cron/cron_overdue_reminders.php
```

Có thể cấu hình chạy tự động qua **Cron Job** (Linux/VPS):

```cron
0 8 * * * php /var/www/library_management_project/cron/cron_overdue_reminders.php
```

**Quy tắc chống spam:** Khoảng cách giữa 2 lần gửi nhắc nhở cho cùng một đơn ≥ **2 ngày**. Log được ghi vào `logs/cron_reminder.log`.

---

## 📦 Import Sách Từ Excel

1. Tải file mẫu tại: `public/templates/book_template.xlsx`
2. Điền thông tin sách theo đúng thứ tự cột:

| Cột | Trường |
|---|---|
| B | Tên sách (`book_title`) |
| C | Giá (`price`) |
| D | Tác giả (`author`) |
| E | Nhà xuất bản (`publisher`) |
| F | Năm xuất bản (`publish_year`) |
| G | Số lượng tồn kho (`stock_quantity`) |
| H | ID Danh mục (`categories_id`) |
| I | Mô tả (`content`) |
| J | Tên file ảnh bìa (`image_url`) |

3. Vào `Admin → Quản lý sách → Import từ Excel` và tải file lên.

---

## 🔒 Bảo Mật

- Mật khẩu được hash bằng **Bcrypt** (`password_hash`)
- Chống **SQL Injection** bằng **PDO Prepared Statements**
- Phân quyền qua middleware `Auth::admin()` và `Auth::user()`
- Tái tạo **Session ID** sau khi đăng nhập (chống Session Fixation)
- Kiểm tra **MIME type** và **giới hạn kích thước (2MB)** khi upload ảnh

---

## 📚 Thư Viện Sử Dụng

| Thư viện | Mục đích |
|---|---|
| [PhpSpreadsheet](https://phpspreadsheet.readthedocs.io/) `^5.4` | Đọc/ghi file Excel (import sách) |
| [PHPMailer](https://github.com/PHPMailer/PHPMailer) | Gửi email SMTP (nhắc nhở quá hạn) |

---

## 📝 Ghi Chú Phát Triển

- Dự án sử dụng kiến trúc **Front Controller**: mọi request đều đi qua `public/index.php`.
- Định tuyến được xử lý thủ công bằng `switch ($action)` trong `index.php`.
- File `public/hash.php` là tiện ích để tạo hash mật khẩu Bcrypt (chỉ dùng khi cần tạo tài khoản Admin thủ công).
