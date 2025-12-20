<?php
class CamNangPostModel {
    private $conn;
    private $table = 'cam_nang_posts';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT p.*, c.name as category_name FROM {$this->table} p LEFT JOIN cam_nang_categories c ON p.category_id = c.id WHERE p.id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data) {
        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table} (category_id, title, slug, summary, content, thumbnail, author, published_at, is_featured, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->bind_param(
            "isssssssii",
            $data['category_id'],
            $data['title'],
            $data['slug'],
            $data['summary'],
            $data['content'],
            $data['thumbnail'],
            $data['author'],
            $data['published_at'],
            $data['is_featured'],
            $data['status']
        );
        return $stmt->execute();
    }

    public function update($data) {
        $sql = "UPDATE {$this->table} SET 
                    category_id = ?,
                    title = ?,
                    slug = ?,
                    summary = ?,
                    content = ?,
                    author = ?,
                    published_at = ?,
                    is_featured = ?,
                    status = ?,
                    updated_at = NOW()";
        
        $types = "issssssii";
        $params = [
            $data['category_id'],
            $data['title'],
            $data['slug'],
            $data['summary'],
            $data['content'],
            $data['author'],
            $data['published_at'],
            $data['is_featured'],
            $data['status']
        ];

        if (!empty($data['thumbnail'])) {
            $sql .= ", thumbnail = ?";
            $types .= "s";
            $params[] = $data['thumbnail'];
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
        if ($item && !empty($item['thumbnail'])) {
            $image_path = '../../../../' . $item['thumbnail'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getTotal($keyword = '', $category_id = '', $status = '', $is_featured = null) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND (title LIKE ?)";
            $keyword_param = "%{$keyword}%";
            $params[] = $keyword_param;
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

        $stmt = $this->conn->prepare($sql);
        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    public function searchAndFilter($keyword = '', $category_id = '', $status = '', $is_featured = null, $limit = null, $offset = null, $exclude_id = null) {
        $sql = "SELECT p.id, p.title, p.slug, p.summary, p.thumbnail, p.status, p.is_featured, p.published_at, c.name as category_name 
                FROM {$this->table} p
                LEFT JOIN cam_nang_categories c ON p.category_id = c.id
                WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND (p.title LIKE ?)";
            $keyword_param = "%{$keyword}%";
            $params[] = $keyword_param;
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
        if ($exclude_id !== null) {
            $sql .= " AND p.id != ?";
            $params[] = $exclude_id;
            $types .= "i";
        }

        $sql .= " ORDER BY p.published_at DESC, p.id DESC";

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
