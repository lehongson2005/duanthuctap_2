<?php
class CartModel {
    private $conn;
    private $table = 'carts';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function findActiveCartByUserId($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE user_id = ? AND status = 'active' LIMIT 1");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function createCartForUser($user_id) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (user_id, status) VALUES (?, 'active')");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        return false;
    }

    public function getOrCreateActiveCartByUserId($user_id) {
        $cart = $this->findActiveCartByUserId($user_id);
        if ($cart) {
            return $cart;
        }
        $cart_id = $this->createCartForUser($user_id);
        return $this->getById($cart_id);
    }

    public function updateCartStatus($cart_id, $status) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $cart_id);
        return $stmt->execute();
    }
}
?>