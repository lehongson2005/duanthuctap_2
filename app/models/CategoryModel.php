<?php
class CategoryModel {
    private $conn;
    private $table = 'categories';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Lấy category theo ID
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Lấy category theo Name
    public function getByName($name) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE LOWER(name)=LOWER(?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Thêm category
    public function create($name, $status) {
        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table} (name, status, created_at, updated_at)
            VALUES (?, ?, NOW(), NOW())
        ");
        $stmt->bind_param("si", $name, $status);
        return $stmt->execute();
    }

    // Cập nhật category
    public function update($id, $name, $status) {
        $stmt = $this->conn->prepare("
            UPDATE {$this->table}
            SET name=?, status=?, updated_at=NOW()
            WHERE id=?
        ");
        $stmt->bind_param("sii", $name, $status, $id);
        return $stmt->execute();
    }

    // Xóa category
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Lấy toàn bộ categories (mới nhất lên đầu)
    public function getAll($limit = null, $offset = null) {
        $sql = "SELECT * FROM {$this->table} ORDER BY id ASC";

        if ($limit !== null && $offset !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $limit, $offset);
            $stmt->execute();
            return $stmt->get_result();
        }

        return $this->conn->query($sql);
    }

    // Đếm tổng số category có filter
    public function getTotal($keyword = '', $status = '') { // Renamed from getTotalCategories
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND name LIKE ?"; // Only search by name
            $keyword_param = "%{$keyword}%";
            $params[] = $keyword_param;
            $types .= "s";
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

    // Tìm kiếm + lọc + phân trang (mới nhất lên đầu)
    public function searchAndFilter($keyword = '', $status = '', $limit = null, $offset = null) {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND name LIKE ?"; // Only search by name
            $keyword_param = "%{$keyword}%";
            $params[] = $keyword_param;
            $types .= "s";
        }

        if ($status !== '') {
            $sql .= " AND status = ?";
            $params[] = $status;
            $types .= "i";
        }

        $sql .= " ORDER BY id ASC";

        if ($limit !== null) { // Allow LIMIT without OFFSET
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
        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt->get_result();
    }
}
?>