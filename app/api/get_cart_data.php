<?php
// QUAN TRỌNG: Phải khởi động session đầu tiên
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Include database và models (Kiểm tra lại đường dẫn cho đúng cấu trúc thư mục của bạn)
include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/../models/ProductModel.php';

// Đảm bảo BASE_URL đã được định nghĩa nếu db.php chưa có
if (!defined('BASE_URL')) {
    define('BASE_URL', '/DuAnThucTap_2'); 
}

$cart_item_count = 0;
$cart_total_price = 0;
$cart_products = [];

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

echo json_encode([
    'item_count' => $cart_item_count,
    'total_price' => $cart_total_price,
    'total_price_formatted' => number_format($cart_total_price, 0, ',', '.') . '₫',
    'cart_products' => $cart_products
]);