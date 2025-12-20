<?php
class CamNangCategoryModel {
    private $conn;
    private $table = 'cam_nang_categories';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data) {
        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table} (name, slug, status, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->bind_param("ssi", $data['name'], $data['slug'], $data['status']);
        return $stmt->execute();
    }

    public function update($data) {
        $sql = "UPDATE {$this->table} SET name = ?, slug = ?, status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssii", $data['name'], $data['slug'], $data['status'], $data['id']);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getAll() {
        $sql = "SELECT * FROM {$this->table} ORDER BY name ASC";
        return $this->conn->query($sql);
    }
    
    public function searchAndFilter($keyword = '', $status = '', $limit = null, $offset = null) {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND (name LIKE ?)";
            $keyword_param = "%{$keyword}%";
            $params[] = $keyword_param;
            $types .= "s";
        }
        if ($status !== '') {
            $sql .= " AND status = ?";
            $params[] = $status;
            $types .= "i";
        }

        $sql .= " ORDER BY name ASC";

        if ($limit !== null) {
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

    public function getTotal($keyword = '', $status = '') {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND (name LIKE ?)";
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
}
?>
