<?php
class PostModel {
    private $conn;
    private $table = 'posts';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT p.*, c.name as category_name FROM {$this->table} p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data) {
        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table} (category_id, title, slug, thumbnail, excerpt, content, author, published_at, status, meta_title, meta_description, tags, is_featured, is_hot, enable_toc, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        
        // Đếm chính xác 15 dấu chấm hỏi (?) tương ứng với 15 ký tự định dạng
        $types = "isssssssisssiii"; 
        $stmt->bind_param(
            $types,
            $data['category_id'],
            $data['title'],
            $data['slug'],
            $data['thumbnail'],
            $data['excerpt'],
            $data['content'],
            $data['author'],
            $data['published_at'],
            $data['status'],
            $data['meta_title'],
            $data['meta_description'],
            $data['tags'],
            $data['is_featured'],
            $data['is_hot'],
            $data['enable_toc']
        );
        return $stmt->execute();
    }

    public function update($data) {
        // 1. Khởi tạo câu lệnh SQL cơ bản (14 trường cố định ban đầu)
        $sql = "UPDATE {$this->table} SET 
                    category_id = ?,
                    title = ?,
                    slug = ?,
                    excerpt = ?,
                    content = ?,
                    author = ?,
                    published_at = ?,
                    status = ?,
                    meta_title = ?,
                    meta_description = ?,
                    tags = ?,
                    is_featured = ?,
                    is_hot = ?,
                    enable_toc = ?,
                    updated_at = NOW()";
        
        // 2. Định nghĩa kiểu dữ liệu tương ứng (14 ký tự)
        // Thứ tự: i-s-s-s-s-s-s-i-s-s-s-i-i-i
        $types = "issssssisssiii";
        $params = [
            $data['category_id'],
            $data['title'],
            $data['slug'],
            $data['excerpt'],
            $data['content'],
            $data['author'],
            $data['published_at'],
            $data['status'],
            $data['meta_title'],
            $data['meta_description'],
            $data['tags'],
            $data['is_featured'],
            $data['is_hot'],
            $data['enable_toc']
        ];

        // 3. Kiểm tra nếu có cập nhật ảnh Thumbnail mới
        if (!empty($data['thumbnail'])) {
            $sql .= ", thumbnail = ?";
            $types .= "s";
            $params[] = $data['thumbnail'];
        }

        // 4. Thêm điều kiện WHERE (ID luôn nằm cuối cùng)
        $sql .= " WHERE id = ?";
        $types .= "i";
        $params[] = $data['id'];

        // 5. Thực thi câu lệnh
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die("Lỗi Prepare SQL: " . $this->conn->error);
        }

        // Giải nén mảng $params vào bind_param bằng toán tử "..."
        $stmt->bind_param($types, ...$params);
        return $stmt->execute();
    }

    public function delete($id) {
        $item = $this->getById($id);
        if ($item && !empty($item['thumbnail'])) {
            // Lưu ý: Đường dẫn ảnh cần khớp với thư mục public của bạn
            $image_path = '../../../../' . $item['thumbnail'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getTotal($keyword = '', $category_id = '', $status = '', $is_featured = null, $is_hot = null) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND (title LIKE ?)";
            $params[] = "%{$keyword}%";
            $types .= "s";
        }
        if (!empty($category_id)) {
            $sql .= " AND category_id = ?";
            $params[] = $category_id;
            $types .= "i";
        }
        if ($status !== '') {
            $sql .= " AND status = ?";
            $params[] = $status;
            $types .= "i";
        }
        if ($is_featured !== null) {
            $sql .= " AND is_featured = ?";
            $params[] = $is_featured;
            $types .= "i";
        }
        if ($is_hot !== null) {
            $sql .= " AND is_hot = ?";
            $params[] = $is_hot;
            $types .= "i";
        }

        $stmt = $this->conn->prepare($sql);
        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    public function searchAndFilter($keyword = '', $category_id = '', $status = '', $is_featured = null, $is_hot = null, $limit = null, $offset = null, $exclude_id = null) {
        $sql = "SELECT p.id, p.title, p.slug, p.thumbnail, p.excerpt, p.status, p.is_featured, p.is_hot, p.published_at, c.name as category_name 
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND (p.title LIKE ?)";
            $params[] = "%{$keyword}%";
            $types .= "s";
        }
        if (!empty($category_id)) {
            $sql .= " AND p.category_id = ?";
            $params[] = $category_id;
            $types .= "i";
        }
        if ($status !== '') {
            $sql .= " AND p.status = ?";
            $params[] = $status;
            $types .= "i";
        }
        if ($is_featured !== null) {
            $sql .= " AND p.is_featured = ?";
            $params[] = $is_featured;
            $types .= "i";
        }
        if ($is_hot !== null) {
            $sql .= " AND p.is_hot = ?";
            $params[] = $is_hot;
            $types .= "i";
        }
        if ($exclude_id !== null) {
            $sql .= " AND p.id != ?";
            $params[] = $exclude_id;
            $types .= "i";
        }

        $sql .= " ORDER BY p.published_at DESC, p.id DESC";

        if ($limit !== null) {
            $sql .= " LIMIT ?";
            $params[] = (int)$limit;
            $types .= "i";
            if ($offset !== null) {
                $sql .= " OFFSET ?";
                $params[] = (int)$offset;
                $types .= "i";
            }
        }

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die("MySQL prepare error: " . $this->conn->error);
        }

        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt->get_result();
    }
}
?>