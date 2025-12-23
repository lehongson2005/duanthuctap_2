<?php
class ProductModel {
    private $conn;
    private $table = 'products';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAll() {
        $sql = "SELECT id, name FROM {$this->table} ORDER BY name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("
            SELECT p.*, 
                   c1.name as category_level1_name,
                   c2.name as category_level2_name,
                   c3.name as category_level3_name
            FROM {$this->table} p
            LEFT JOIN categories c1 ON p.category_level1_id = c1.id
            LEFT JOIN category_level2 c2 ON p.category_level2_id = c2.id
            LEFT JOIN category_level3 c3 ON p.category_level3_id = c3.id
            WHERE p.id=?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data) {
        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table} (
                category_level1_id, category_level2_id, category_level3_id, 
                sku, name, slug, short_description, long_description, thumbnail, image_hover, 
                price, discount_price, stock_quantity, weight, weight_unit, 
                status, is_featured, is_new, created_at, updated_at
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        // Types: i (int) s (string) d (double)
        $stmt->bind_param(
            "iiisssssssddsissii",
            $data['category_level1_id'],
            $data['category_level2_id'],
            $data['category_level3_id'],
            $data['sku'],
            $data['name'],
            $data['slug'],
            $data['short_description'],
            $data['long_description'],
            $data['thumbnail'],
            $data['image_hover'],
            $data['price'],
            $data['discount_price'],
            $data['stock_quantity'],
            $data['weight'],
            $data['weight_unit'],
            $data['status'],
            $data['is_featured'],
            $data['is_new']
        );
        return $stmt->execute();
    }

    public function update($data) {
        $sql = "UPDATE {$this->table} SET 
                    category_level1_id = ?, 
                    category_level2_id = ?, 
                    category_level3_id = ?, 
                    sku = ?, 
                    name = ?, 
                    slug = ?, 
                    short_description = ?, 
                    long_description = ?, 
                    price = ?, 
                    discount_price = ?, 
                    stock_quantity = ?, 
                    weight = ?,
                    weight_unit = ?,
                    status = ?, 
                    is_featured = ?, 
                    is_new = ?,
                    updated_at = NOW()";
        
        $types = "iiisssssddidsiii"; // Corrected types string: 3i, 5s, 2d, 1d, 1s, 3i
        $params = [
            $data['category_level1_id'],
            $data['category_level2_id'],
            $data['category_level3_id'],
            $data['sku'],
            $data['name'],
            $data['slug'],
            $data['short_description'],
            $data['long_description'],
            $data['price'],
            $data['discount_price'],
            $data['stock_quantity'],
            $data['weight'],
            $data['weight_unit'],
            $data['status'],
            $data['is_featured'],
            $data['is_new']
        ];

        if (!empty($data['thumbnail'])) {
            $sql .= ", thumbnail = ?";
            $types .= "s";
            $params[] = $data['thumbnail'];
        }
        
        if (!empty($data['image_hover'])) {
            $sql .= ", image_hover = ?";
            $types .= "s";
            $params[] = $data['image_hover'];
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
        if ($item) {
            if (!empty($item['thumbnail'])) {
                $thumbnail_path = '../../../../' . $item['thumbnail'];
                if (file_exists($thumbnail_path)) {
                    unlink($thumbnail_path);
                }
            }
            if (!empty($item['image_hover'])) {
                $image_hover_path = '../../../../' . $item['image_hover'];
                if (file_exists($image_hover_path)) {
                    unlink($image_hover_path);
                }
            }
        }
        
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getTotal($keyword = '', $category_level1_id = '', $category_level2_id = '', $category_level3_id = '', $status = '') {
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
        if (!empty($category_level1_id)) {
            $sql .= " AND category_level1_id = ?";
            $params[] = $category_level1_id;
            $types .= "i";
        }
        if (!empty($category_level2_id)) {
            $sql .= " AND category_level2_id = ?";
            $params[] = $category_level2_id;
            $types .= "i";
        }
        if (!empty($category_level3_id)) {
            $sql .= " AND category_level3_id = ?";
            $params[] = $category_level3_id;
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

    public function searchAndFilter($keyword = '', $category_level1_id = '', $category_level2_id = '', $category_level3_id = '', $status = '', $is_featured = '', $sort_by = 'newest', $limit = null, $offset = null) {
        $sql = "SELECT p.*, 
                       c1.name as category_level1_name,
                       c2.name as category_level2_name,
                       c3.name as category_level3_name
                FROM {$this->table} p
                LEFT JOIN categories c1 ON p.category_level1_id = c1.id
                LEFT JOIN category_level2 c2 ON p.category_level2_id = c2.id
                LEFT JOIN category_level3 c3 ON p.category_level3_id = c3.id
                WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
            $keyword_param = "%{$keyword}%";
            $params[] = $keyword_param;
            $params[] = $keyword_param;
            $types .= "ss";
        }
        if (!empty($category_level1_id)) {
            $sql .= " AND p.category_level1_id = ?";
            $params[] = $category_level1_id;
            $types .= "i";
        }
        if (!empty($category_level2_id)) {
            $sql .= " AND p.category_level2_id = ?";
            $params[] = $category_level2_id;
            $types .= "i";
        }
        if (!empty($category_level3_id)) {
            $sql .= " AND p.category_level3_id = ?";
            $params[] = $category_level3_id;
            $types .= "i";
        }
        if ($status !== '') {
            $sql .= " AND p.status = ?";
            $params[] = $status;
            $types .= "i";
        }
        if ($is_featured !== '') {
            $sql .= " AND p.is_featured = ?";
            $params[] = $is_featured;
            $types .= "i";
        }

        // Add sorting logic
        switch ($sort_by) {
            case 'price_asc':
                $sql .= " ORDER BY p.price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY p.price DESC";
                break;
            case 'newest':
            default:
                $sql .= " ORDER BY p.created_at DESC";
                break;
        }



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
            die("MySQL prepare error: " . $this->conn->error . " | SQL: " . $sql);
        }

        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt->get_result();
    }
}