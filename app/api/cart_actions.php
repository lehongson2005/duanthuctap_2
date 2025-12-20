<?php
// Sử dụng Output Buffering để hứng mọi rác rưởi (khoảng trắng, lỗi Warning) chèn vào JSON
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_URL')) {
    define('BASE_URL', '/DuAnThucTap_2'); 
}

// Kiểm tra và nhúng file cấu hình DB
$db_file = __DIR__ . '/../config/db.php';
if (file_exists($db_file)) {
    include_once $db_file;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$product_id = (int)($_POST['id'] ?? $_GET['id'] ?? $_POST['product_id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? $_GET['quantity'] ?? 1);

// Xử lý logic giỏ hàng
if ($product_id > 0 || $action === 'update') {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    switch ($action) {
        case 'add':
            $_SESSION['cart'][$product_id] = ($_SESSION['cart'][$product_id] ?? 0) + $quantity;
            $msg = 'Sản phẩm đã được thêm vào giỏ hàng!';
            break;

        case 'update':
            if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
                foreach ($_POST['quantities'] as $id => $qty) {
                    $id = (int)$id; $qty = (int)$qty;
                    if ($qty > 0) $_SESSION['cart'][$id] = $qty;
                    else unset($_SESSION['cart'][$id]);
                }
            } else if ($product_id > 0) {
                $_SESSION['cart'][$product_id] = $quantity;
            }
            $msg = 'Giỏ hàng đã được cập nhật!';
            break;
        
        case 'remove':
            unset($_SESSION['cart'][$product_id]);
            $msg = 'Sản phẩm đã được xóa khỏi giỏ hàng!';
            break;
    }
}

// KIỂM TRA AJAX
$is_ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || 
           (isset($_POST['is_ajax']) && $_POST['is_ajax'] == '1');

if ($is_ajax) {
    // Xóa sạch mọi thứ trong bộ đệm (nếu db.php có echo "Kết nối thành công" thì nó sẽ bị xóa ở đây)
    ob_clean(); 
    header('Content-Type: application/json');

    $response = [
        'status' => 'success',
        'message' => $msg ?? 'Thao tác thành công!',
        'cart_count' => array_sum($_SESSION['cart'] ?? []),
        'unique_items' => count($_SESSION['cart'] ?? [])
    ];

    echo json_encode($response);
    exit(); // Dừng ngay lập tức để không chạy xuống lệnh Redirect
}

// Chỉ Redirect khi KHÔNG PHẢI AJAX
$project_home = BASE_URL . "/app/Views/user/giohang/giohang.php";
$referer = $_SERVER['HTTP_REFERER'] ?? $project_home;
header("Location: " . $referer);
exit();