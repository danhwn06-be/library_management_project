<?php
class Book
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    
    // Lấy sách theo category + phân trang
    public function getBooksByCategory($categoryId, $limit, $offset)
    {
        $sql = "SELECT b.book_id, b.book_title, b.author, b.stock_quantity, b.image_url, c.categories_name 
                FROM books b
                LEFT JOIN categories c ON b.categories_id = c.categories_id 
                WHERE b.categories_id = :cat_id
                LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':cat_id', (int)$categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đếm tổng số sách theo category
    public function getTotalBooksByCategory($categoryId)
    {
        $sql = "SELECT COUNT(*) 
                FROM books 
                WHERE categories_id = :categoryId";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':categoryId', (int)$categoryId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    // Lấy sách có giới hạn để phân trang
    public function getBooksPagination($limit, $offset)
    {
        // Thêm b.price, b.publisher, b.publish_year vào câu SELECT
        $sql = "SELECT b.book_id, b.book_title, b.author, b.stock_quantity, b.image_url, 
                   b.price, b.publisher, b.publish_year, c.categories_name 
            FROM books b
            LEFT JOIN categories c ON b.categories_id = c.categories_id 
            LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Đếm tổng số sách để tính số trang
    public function getTotalBooks()
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM books");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    // Lấy thông tin chi tiết sách theo ID
    public function getBookById($bookId)
    {
        $query = "SELECT b.*, c.categories_name 
                  FROM books b 
                  LEFT JOIN categories c ON b.categories_id = c.categories_id 
                  WHERE b.book_id = :book_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':book_id', $bookId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    // Tìm kiếm sách theo tên hoặc tác giả
    public function searchBooks($keyword)
    {
        $keyword = "%" . $keyword . "%";
        $sql = "SELECT b.book_id, b.book_title, b.author, b.stock_quantity, b.image_url, c.categories_name 
        FROM books b
        LEFT JOIN categories c ON b.categories_id = c.categories_id 
        WHERE b.book_title LIKE :keyword OR b.author LIKE :keyword";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':keyword', $keyword, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // thêm sách vào database
    public function checkBookExist($title)
    {
        $sql = "SELECT COUNT(*) FROM books WHERE book_title = :title";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    public function addNewBook($data)
    {
        if ($this->checkBookExist($data['book_title'])) {
            return [
                'status' => false,
                'message' => 'The book title already exists in the system!'
            ];
        }
        try {
            $sql = "INSERT INTO books 
                    (book_title, author, categories_id, publisher, publish_year, price, stock_quantity, image_url, content) 
                    VALUES 
                    (:book_title, :author, :categories_id, :publisher, :publish_year, :price, :stock_quantity, :image_url, :content)";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                ':book_title'      => $data['book_title'],
                ':author'          => $data['author'],
                ':categories_id'   => $data['categories_id'],
                ':publisher'       => $data['publisher'],
                ':publish_year'    => $data['publish_year'],
                ':price'           => $data['price'],
                ':stock_quantity'  => $data['stock_quantity'],
                ':image_url'       => $data['image_url'],
                ':content'         => $data['content']
            ]);
            if ($result) {
                return ['status' => true];
            }
            return ['status' => false];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    public function updateBook($id, $data)
    {
        try {
            $sql = "UPDATE books SET 
                        book_title = :book_title,
                        author = :author,
                        categories_id = :categories_id,
                        publisher = :publisher,
                        publish_year = :publish_year,
                        price = :price,
                        stock_quantity = :stock_quantity,
                        image_url = :image_url,
                        content = :content
                    WHERE book_id = :book_id";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                ':book_title'      => $data['book_title'],
                ':author'          => $data['author'],
                ':categories_id'   => $data['categories_id'],
                ':publisher'       => $data['publisher'],
                ':publish_year'    => $data['publish_year'],
                ':price'           => $data['price'],
                ':stock_quantity'  => $data['stock_quantity'],
                ':image_url'       => $data['image_url'],
                ':content'         => $data['content'],
                ':book_id'        => $id
            ]);
            if ($result) {
                return ['status' => true];
            }
            return ['status' => false];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    public function deleteBook($id)
    {
        $check = $this->conn->prepare("SELECT COUNT(*) FROM borrow_request_books WHERE book_id = ?");
        $check->execute([$id]);
        if ($check->fetchColumn() > 0) {
            return false;
        }

        $sql = "DELETE FROM books WHERE book_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    //import book
    public function importBook($data)
    {
        try {
            $sql = "INSERT INTO books (book_title, price, author, publisher, publish_year, stock_quantity, categories_id, content, image_url) 
                VALUES (:book_title, :price, :author, :publisher, :publish_year, :stock_quantity, :categories_id, :content, :image_url)";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                ':book_title'     => $data['book_title'],
                ':price'          => $data['price'],
                ':author'         => $data['author'],
                ':publisher'      => $data['publisher'],
                ':publish_year'   => $data['publish_year'],
                ':stock_quantity' => $data['stock_quantity'],
                ':categories_id'  => $data['categories_id'],
                ':content'        => $data['content'],
                ':image_url'      => $data['image_url']
            ]);
        } catch (Exception $e) {
            return false;
        }
    }
    // Thống kê chung
    public function getGeneralStatistics() {
    // Đếm tổng số sách
    $stmt1 = $this->conn->query("SELECT COUNT(*) as total FROM books");
    $totalBooks = $stmt1->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Đếm số yêu cầu mượn đang chờ (Giả sử status = 'pending' hoặc 0)
    $stmt2 = $this->conn->query("SELECT COUNT(*) as total FROM borrow_requests"); // Bạn có thể thêm WHERE status = 0
    $totalRequests = $stmt2->fetch(PDO::FETCH_ASSOC)['total'];
    
    return [
        'total_books' => $totalBooks,
        'total_requests' => $totalRequests
    ];
}

    public function countBooksByCategory() {
    $sql = "SELECT c.categories_name, COUNT(b.book_id) as count 
            FROM categories c 
            LEFT JOIN books b ON c.categories_id = b.categories_id 
            GROUP BY c.categories_id, c.categories_name";
            
    $stmt = $this->conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Trả về mảng tất cả các dòng
}
}    
?>
