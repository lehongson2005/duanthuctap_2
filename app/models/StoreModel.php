<?php
class StoreModel {
    private $conn;
    private $table = 'stores';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // getById needs to join through wards to get province info
    public function getById($id) {
        $sql = "SELECT s.*, w.name as ward_name, w.type as ward_type, p.name as province_name, p.id as province_id
                FROM {$this->table} s
                LEFT JOIN wards w ON s.ward_id = w.id
                LEFT JOIN provinces p ON w.province_id = p.id
                WHERE s.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // create does NOT include province_id
    public function create($name, $address, $ward_id, $phone_number, $map_link, $status, $latitude, $longitude) {
        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table} (name, address, ward_id, phone_number, map_link, status, latitude, longitude)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssissidd", $name, $address, $ward_id, $phone_number, $map_link, $status, $latitude, $longitude);
        return $stmt->execute();
    }

    // update does NOT include province_id
    public function update($id, $name, $address, $ward_id, $phone_number, $map_link, $status, $latitude, $longitude) {
        $stmt = $this->conn->prepare("
            UPDATE {$this->table}
            SET name = ?, address = ?, ward_id = ?, phone_number = ?, map_link = ?, status = ?, latitude = ?, longitude = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->bind_param("ssissiddi", $name, $address, $ward_id, $phone_number, $map_link, $status, $latitude, $longitude, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // getTotal needs to join through wards to filter by province
    public function getTotal($keyword = '', $province_id = '', $ward_id = '', $status = '') {
        $sql = "SELECT COUNT(s.id) as total FROM {$this->table} s";
        $params = [];
        $types = "";

        // Need to join to filter by province
        if (!empty($province_id)) {
            $sql .= " LEFT JOIN wards w ON s.ward_id = w.id";
        }

        $sql .= " WHERE 1=1";

        if (!empty($keyword)) {
            $sql .= " AND (s.name LIKE ? OR s.address LIKE ? OR s.phone_number LIKE ?)";
            $keyword_param = "%{$keyword}%";
            array_push($params, $keyword_param, $keyword_param, $keyword_param);
            $types .= "sss";
        }
        if (!empty($province_id)) {
            $sql .= " AND w.province_id = ?";
            $params[] = $province_id;
            $types .= "i";
        }
        if (!empty($ward_id)) {
            $sql .= " AND s.ward_id = ?";
            $params[] = $ward_id;
            $types .= "i";
        }
        if ($status !== '') {
            $sql .= " AND s.status = ?";
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

    // searchAndFilter needs to join through wards to get province info
    public function searchAndFilter($keyword = '', $province_id = '', $ward_id = '', $status = '', $limit = null, $offset = null) {
        $sql = "SELECT s.*, w.name as ward_name, w.type as ward_type, p.name as province_name
                FROM {$this->table} s
                LEFT JOIN wards w ON s.ward_id = w.id
                LEFT JOIN provinces p ON w.province_id = p.id
                WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND (s.name LIKE ? OR s.address LIKE ? OR s.phone_number LIKE ?)";
            $keyword_param = "%{$keyword}%";
            array_push($params, $keyword_param, $keyword_param, $keyword_param);
            $types .= "sss";
        }
        if (!empty($province_id)) {
            $sql .= " AND w.province_id = ?"; // Correct join condition
            $params[] = $province_id;
            $types .= "i";
        }
        if (!empty($ward_id)) {
            $sql .= " AND s.ward_id = ?";
            $params[] = $ward_id;
            $types .= "i";
        }
        if ($status !== '') {
            $sql .= " AND s.status = ?";
            $params[] = $status;
            $types .= "i";
        }

        $sql .= " ORDER BY s.name ASC";

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