<?php
class ProvinceModel {
    private $conn;
    private $table = 'provinces';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($name, $type) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (name, type) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $type);
        return $stmt->execute();
    }

    public function update($id, $name, $type) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET name = ?, type = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ssi", $name, $type, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getTotal($keyword = '') {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND (name LIKE ? OR type LIKE ?)";
            $keyword_param = "%{$keyword}%";
            array_push($params, $keyword_param, $keyword_param);
            $types .= "ss";
        }

        $stmt = $this->conn->prepare($sql);
        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    public function searchAndFilter($keyword = '', $limit = null, $offset = null) {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND (name LIKE ? OR type LIKE ?)";
            $keyword_param = "%{$keyword}%";
            array_push($params, $keyword_param, $keyword_param);
            $types .= "ss";
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

    public function getAll() {
        $sql = "SELECT id, name, type FROM {$this->table} ORDER BY name ASC";
        return $this->conn->query($sql);
    }
}
?>