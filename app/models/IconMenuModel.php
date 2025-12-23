<?php
class IconMenuModel {
    private $conn;
    private $table = 'icon_menu';

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
            INSERT INTO {$this->table} (title, slug, image, link, link_type, link_target_id, sort_order, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->bind_param(
            "sssssiis",
            $data['title'],
            $data['slug'],
            $data['image'],
            $data['link'],
            $data['link_type'],
            $data['link_target_id'],
            $data['sort_order'],
            $data['status']
        );
        return $stmt->execute();
    }

    public function update($data) {
        $sql = "UPDATE {$this->table} SET 
                    title = ?, 
                    slug = ?, 
                    link = ?, 
                    link_type = ?,
                    link_target_id = ?,
                    sort_order = ?, 
                    status = ?, 
                    updated_at = NOW()";
        $types = "ssssiis";
        $params = [
            $data['title'],
            $data['slug'],
            $data['link'],
            $data['link_type'],
            $data['link_target_id'],
            $data['sort_order'],
            $data['status']
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

    public function getTotal($keyword = '', $status = '') {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND (title LIKE ?)";
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

    public function searchAndFilter($keyword = '', $status = '', $limit = null, $offset = null) {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND (title LIKE ?)";
            $keyword_param = "%{$keyword}%";
            $params[] = $keyword_param;
            $types .= "s";
        }
        if ($status !== '') {
            $sql .= " AND status = ?";
            $params[] = $status;
            $types .= "i";
        }

        $sql .= " ORDER BY sort_order ASC, id DESC";

        if ($limit !== null && $offset !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            $types .= "ii";
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