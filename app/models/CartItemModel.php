<?php
class CartItemModel {
    private $conn;
    private $table = 'cart_items';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getItemsByCartId($cart_id) {
        $sql = "
            SELECT ci.id, ci.product_id, ci.quantity, ci.price, p.name as product_name, p.thumbnail as product_thumbnail
            FROM {$this->table} ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.cart_id = ?
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $cart_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function addOrUpdateItem($cart_id, $product_id, $quantity, $price) {
        // Check if item already exists
        $find_stmt = $this->conn->prepare("SELECT id, quantity FROM {$this->table} WHERE cart_id = ? AND product_id = ?");
        $find_stmt->bind_param("ii", $cart_id, $product_id);
        $find_stmt->execute();
        $existing_item = $find_stmt->get_result()->fetch_assoc();
        $find_stmt->close();

        if ($existing_item) {
            // Update quantity
            $new_quantity = $existing_item['quantity'] + $quantity;
            $update_stmt = $this->conn->prepare("UPDATE {$this->table} SET quantity = ? WHERE id = ?");
            $update_stmt->bind_param("ii", $new_quantity, $existing_item['id']);
            return $update_stmt->execute();
        } else {
            // Insert new item
            $insert_stmt = $this->conn->prepare("INSERT INTO {$this->table} (cart_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("iiid", $cart_id, $product_id, $quantity, $price);
            return $insert_stmt->execute();
        }
    }

    public function updateItemQuantity($cart_item_id, $quantity) {
        if ($quantity <= 0) {
            return $this->removeItem($cart_item_id);
        }
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET quantity = ? WHERE id = ?");
        $stmt->bind_param("ii", $quantity, $cart_item_id);
        return $stmt->execute();
    }

    public function removeItem($cart_item_id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("i", $cart_item_id);
        return $stmt->execute();
    }
}
?>