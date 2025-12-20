<?php
class OrderModel {
    private $conn;
    private $table = 'orders';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT o.*, u.username FROM {$this->table} o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateStatus($id, $status) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }
    
    public function delete($id) {
        // The ON DELETE CASCADE in the database will handle deleting order_items
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getTotal($keyword = '', $status = '') {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            // Search by full name, email, or phone number
            $sql .= " AND (full_name LIKE ? OR email LIKE ? OR phone_number LIKE ?)";
            $keyword_param = "%{$keyword}%";
            array_push($params, $keyword_param, $keyword_param, $keyword_param);
            $types .= "sss";
        }

        if (!empty($status)) {
            $sql .= " AND status = ?";
            $params[] = $status;
            $types .= "s";
        }

        $stmt = $this->conn->prepare($sql);
        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    public function searchAndFilter($keyword = '', $status = '', $limit = null, $offset = null) {
        $sql = "SELECT id, user_id, full_name, total_money, order_date, status FROM {$this->table} WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($keyword)) {
            $sql .= " AND (full_name LIKE ? OR email LIKE ? OR phone_number LIKE ?)";
            $keyword_param = "%{$keyword}%";
            array_push($params, $keyword_param, $keyword_param, $keyword_param);
            $types .= "sss";
        }
        
        if (!empty($status)) {
            $sql .= " AND status = ?";
            $params[] = $status;
            $types .= "s";
        }

        $sql .= " ORDER BY order_date DESC";

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