<?php
// QUAN TRỌNG: Phải khởi động session đầu tiên
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Include database và models (Kiểm tra lại đường dẫn cho đúng cấu trúc thư mục của bạn)
include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/../models/ProductModel.php';
include_once __DIR__ . '/../models/CartModel.php';
include_once __DIR__ . '/../models/CartItemModel.php';

// Đảm bảo BASE_URL đã được định nghĩa nếu db.php chưa có
if (!defined('BASE_URL')) {
    define('BASE_URL', '/DuAnThucTap_2'); 
}

$cart_item_count = 0;
$cart_total_price = 0;
$cart_products = [];

// Kiểm tra nếu user đã đăng nhập - đọc từ database
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $cartModel = new CartModel($conn);
    $cartItemModel = new CartItemModel($conn);
    
    // Lấy giỏ hàng của user từ database
    $cart = $cartModel->getOrCreateActiveCartByUserId($userId);
    if ($cart) {
        $cartId = $cart['id'];
        $items_result = $cartItemModel->getItemsByCartId($cartId);
        
        // Đồng bộ dữ liệu từ database vào session
        $_SESSION['cart'] = [];
        if ($items_result) {
            $productModel = new ProductModel($conn);
            while ($item = $items_result->fetch_assoc()) {
                // Lưu vào session
                $_SESSION['cart'][$item['product_id']] = $item['quantity'];
                
                // Chuẩn bị dữ liệu để trả về
                $price = $item['price'];
                $cart_products[] = [
                    'id' => $item['product_id'],
                    'name' => htmlspecialchars($item['product_name']),
                    'thumbnail' => BASE_URL . '/' . htmlspecialchars($item['product_thumbnail']),
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'sub_total' => $price * $item['quantity'],
                    'price_formatted' => number_format($price, 0, ',', '.') . '₫',
                ];
                $cart_total_price += $price * $item['quantity'];
            }
            $cart_item_count = $cartItemModel->getItemCountByCartId($cartId);
        }
    }
} else {
    // User chưa đăng nhập - đọc từ session
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $productModel = new ProductModel($conn);
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $product = $productModel->getById($product_id);
            if ($product) {
                $price = (isset($product['discount_price']) && $product['discount_price'] > 0) ? $product['discount_price'] : $product['price'];
                $cart_products[] = [
                    'id' => $product['id'],
                    'name' => htmlspecialchars($product['name']),
                    // Dùng BASE_URL để đảm bảo đường dẫn ảnh đúng
                    'thumbnail' => BASE_URL . '/' . htmlspecialchars($product['thumbnail']),
                    'quantity' => $quantity,
                    'price' => $price,
                    'sub_total' => $price * $quantity,
                    'price_formatted' => number_format($price, 0, ',', '.') . '₫',
                ];
                $cart_total_price += $price * $quantity;
            }
        }
        // cart_item_count nên là tổng số lượng (quantity) thay vì chỉ đếm dòng
        $cart_item_count = array_sum($_SESSION['cart']);
    }
}

echo json_encode([
    'item_count' => $cart_item_count,
    'total_price' => $cart_total_price,
    'total_price_formatted' => number_format($cart_total_price, 0, ',', '.') . '₫',
    'cart_products' => $cart_products
]);