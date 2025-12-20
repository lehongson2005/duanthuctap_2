<?php
class CategoryLevel2Model {
    private $conn;
    private $table = 'category_level2';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT p.*, c.name as category_name FROM {$this->table} p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getByName($name) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE LOWER(name)=LOWER(?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data) {
        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table} (category_id, sku, name, image, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");
        
        // Types: i (cat_id), s (sku), s (name), s (image), i (status)
        $types = "isssi"; 
        $stmt->bind_param(
            $types,
            $data['category_id'],
            $data['sku'],
            $data['name'],
            $data['image'],
            $data['status']
        );
        return $stmt->execute();
    }

    public function update($data) {
        $sql = "UPDATE {$this->table} SET 
                    category_id = ?, 
                    sku = ?, 
                    name = ?, 
                    status = ?, 
                    updated_at = NOW()";

        $types = "issi"; 
        $params = [
            $data['category_id'],
            $data['sku'],
            $data['name'],
            $data['status']
        ];

        // Kiểm tra nếu có cập nhật ảnh mới
        if (!empty($data['image'])) {
            $sql .= ", image = ?";
            $types .= "s";
            $params[] = $data['image'];
        }

        $sql .= " WHERE id = ?";
        $types .= "i";
        $params[] = $data['id'];

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die("MySQL prepare error: " . $this->conn->error);
        }

        $stmt->bind_param($types, ...$params);
        return $stmt->execute();
    }

    public function delete($id) {
        // Lấy thông tin để xóa ảnh vật lý trên server trước khi xóa record
        $item = $this->getById($id);
        if ($item && !empty($item['image'])) {
            $image_path = '../../../../' . $item['image']; // Điều chỉnh đường dẫn này cho đúng với folder upload của bạn
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getTotal($keyword = '', $category_id = '', $status = '') {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND (name LIKE ? OR sku LIKE ?)";
            $keyword_param = "%{$keyword}%";
            $params[] = $keyword_param;
            $params[] = $keyword_param;
            $types .= "ss";
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

        $stmt = $this->conn->prepare($sql);
        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    public function searchAndFilter($keyword = '', $category_id = '', $status = '', $limit = null, $offset = null) {
        $sql = "SELECT p.*, c.name as category_name FROM {$this->table} p LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
            $keyword_param = "%{$keyword}%";
            $params[] = $keyword_param;
            $params[] = $keyword_param;
            $types .= "ss";
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

        $sql .= " ORDER BY p.id ASC";

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

    public function getAll() {
        $sql = "SELECT p.*, c.name as category_name FROM {$this->table} p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id ASC";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die("MySQL prepare error: " . $this->conn->error);
        }
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>