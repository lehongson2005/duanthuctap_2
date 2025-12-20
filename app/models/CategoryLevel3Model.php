<?php
class CategoryLevel3Model {
    private $conn;
    private $table = 'category_level3';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT c3.*, c2.name as category_level2_name FROM {$this->table} c3 LEFT JOIN category_level2 c2 ON c3.category_level2_id = c2.id WHERE c3.id=?");
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
            INSERT INTO {$this->table} (category_level2_id, sku, name, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->bind_param(
            "issis", // Adjusted types for category_level2_id, sku, name, status
            $data['category_level2_id'],
            $data['sku'],
            $data['name'],
            $data['status']
        );
        return $stmt->execute();
    }

    public function update($data) {
        $sql = "UPDATE {$this->table} SET 
                    category_level2_id = ?, 
                    sku = ?, 
                    name = ?, 
                    status = ?, 
                    updated_at = NOW()";

        $types = "issis"; // Adjusted types for category_level2_id, sku, name, status
        $params = [
            $data['category_level2_id'],
            $data['sku'],
            $data['name'],
            $data['status']
        ];

        $sql .= " WHERE id = ?";
        $types .= "i";
        $params[] = $data['id'];

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        return $stmt->execute();
    }

    public function delete($id) {
        // No image deletion logic needed for category_level3
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Renamed from getTotalItems to getTotal
    public function getTotal($keyword = '', $category_level2_id = '', $status = '') {
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
        if (!empty($category_level2_id)) {
            $sql .= " AND category_level2_id = ?";
            $params[] = $category_level2_id;
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

    public function searchAndFilter($keyword = '', $category_level2_id = '', $status = '', $limit = null, $offset = null) {
        $sql = "SELECT c3.*, c2.name as category_level2_name FROM {$this->table} c3 LEFT JOIN category_level2 c2 ON c3.category_level2_id = c2.id WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND (c3.name LIKE ? OR c3.sku LIKE ?)";
            $keyword_param = "%{$keyword}%";
            $params[] = $keyword_param;
            $params[] = $keyword_param;
            $types .= "ss";
        }
        if (!empty($category_level2_id)) {
            $sql .= " AND c3.category_level2_id = ?";
            $params[] = $category_level2_id;
            $types .= "i";
        }
        if ($status !== '') {
            $sql .= " AND c3.status = ?";
            $params[] = $status;
            $types .= "i";
        }

        $sql .= " ORDER BY c3.id ASC";

        if ($limit !== null) { // Fixed LIMIT/OFFSET binding
            $sql .= " LIMIT ?";
            $params[] = $limit;
            $types .= "i";
            if ($offset !== null) {
                $sql .= " OFFSET ?";
                $params[] = $offset;
                $types .= "i";
            }
        }

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die("MySQL prepare error: " . $this->conn->error . " | SQL: " . $sql);
        }

        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt->get_result();
    }

    public function getAll() {
        $sql = "SELECT c3.*, c2.name as category_level2_name FROM {$this->table} c3 LEFT JOIN category_level2 c2 ON c3.category_level2_id = c2.id ORDER BY c3.id ASC";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die("MySQL prepare error: " . $this->conn->error . " | SQL: " . $sql);
        }
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>