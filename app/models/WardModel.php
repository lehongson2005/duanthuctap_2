<?php
class WardModel {
    private $conn;
    private $table = 'wards';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT w.*, p.name as province_name FROM {$this->table} w LEFT JOIN provinces p ON w.province_id = p.id WHERE w.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($name, $type, $province_id) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (name, type, province_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $name, $type, $province_id);
        return $stmt->execute();
    }

    public function update($id, $name, $type, $province_id) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET name = ?, type = ?, province_id = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ssii", $name, $type, $province_id, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getTotal($keyword = '', $province_id = '') {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND (name LIKE ? OR type LIKE ?)";
            $keyword_param = "%{$keyword}%";
            array_push($params, $keyword_param, $keyword_param);
            $types .= "ss";
        }
        if (!empty($province_id)) {
            $sql .= " AND province_id = ?";
            $params[] = $province_id;
            $types .= "i";
        }

        $stmt = $this->conn->prepare($sql);
        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    public function searchAndFilter($keyword = '', $province_id = '', $limit = null, $offset = null) {
        $sql = "SELECT w.*, p.name as province_name FROM {$this->table} w LEFT JOIN provinces p ON w.province_id = p.id WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND (w.name LIKE ? OR w.type LIKE ?)";
            $keyword_param = "%{$keyword}%";
            array_push($params, $keyword_param, $keyword_param);
            $types .= "ss";
        }
        if (!empty($province_id)) {
            $sql .= " AND w.province_id = ?";
            $params[] = $province_id;
            $types .= "i";
        }

        $sql .= " ORDER BY w.name ASC";

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

    public function getByProvinceId($province_id) {
        $stmt = $this->conn->prepare("SELECT id, name, type FROM {$this->table} WHERE province_id = ? ORDER BY name ASC");
        $stmt->bind_param("i", $province_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>