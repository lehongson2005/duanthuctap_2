<?php
class BannerModel {
    private $conn;
    private $table = 'banners';

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
            INSERT INTO {$this->table} (title, image, link, position, sort_order, status, description, start_date, end_date, target, device, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->bind_param(
            "ssssissssss",
            $data['title'],
            $data['image'],
            $data['link'],
            $data['position'],
            $data['sort_order'],
            $data['status'],
            $data['description'],
            $data['start_date'],
            $data['end_date'],
            $data['target'],
            $data['device']
        );
        return $stmt->execute();
    }

    public function update($data) {
        $sql = "UPDATE {$this->table} SET 
                    title = ?, 
                    link = ?, 
                    position = ?, 
                    sort_order = ?, 
                    status = ?, 
                    description = ?, 
                    start_date = ?, 
                    end_date = ?, 
                    target = ?, 
                    device = ?, 
                    updated_at = NOW()";
        $types = "sssissssss";
        $params = [
            $data['title'],
            $data['link'],
            $data['position'],
            $data['sort_order'],
            $data['status'],
            $data['description'],
            $data['start_date'],
            $data['end_date'],
            $data['target'],
            $data['device']
        ];

        if (!empty($data['image'])) {
            $sql .= ", image = ?";
            $types .= "s";
            $params[] = $data['image'];
        }

        $sql .= " WHERE id = ?";
        $types .= "i";
        $params[] = $data['id'];

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        return $stmt->execute();
    }

    public function delete($id) {
        $item = $this->getById($id);
        if ($item && !empty($item['image'])) {
            $image_path = '../../../../' . $item['image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getTotal($keyword = '', $position_filter = '', $status = '') {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND title LIKE ?"; // Chỉ tìm theo tiêu đề
            $keyword_param = "%{$keyword}%";
            $params[] = $keyword_param;
            $types .= "s";
        }
        if (!empty($position_filter)) {
            $sql .= " AND position = ?"; // Lọc chính xác theo vị trí
            $params[] = $position_filter;
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

    public function searchAndFilter($keyword = '', $position_filter = '', $status = '', $limit = null, $offset = null) {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND title LIKE ?";
            $keyword_param = "%{$keyword}%";
            $params[] = $keyword_param;
            $types .= "s";
        }
        if (!empty($position_filter)) {
            $sql .= " AND position = ?";
            $params[] = $position_filter;
            $types .= "s";
        }
        if ($status !== '') {
            $sql .= " AND status = ?";
            $params[] = $status;
            $types .= "i";
        }

        $sql .= " ORDER BY sort_order ASC, id DESC";

        if ($limit !== null) { // Change to allow LIMIT without OFFSET
            $sql .= " LIMIT ?";
            $params[] = $limit;
            $types .= "i"; // Type for LIMIT

            if ($offset !== null) {
                $sql .= " OFFSET ?";
                $params[] = $offset;
                $types .= "i"; // Type for OFFSET
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