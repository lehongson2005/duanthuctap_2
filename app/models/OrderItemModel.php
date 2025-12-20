<?php
class OrderItemModel {
    private $conn;
    private $table = 'order_items';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getByOrderId($order_id) {
        $sql = "
            SELECT oi.quantity, oi.price, oi.total_money, p.name as product_name, p.thumbnail as product_thumbnail
            FROM {$this->table} oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
            ORDER BY p.name ASC
        ";
        
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die("MySQL prepare error: " . $this->conn->error);
        }

        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>