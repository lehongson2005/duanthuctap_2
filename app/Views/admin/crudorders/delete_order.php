<?php
// delete_order.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: ../../auth/login.php");
    exit;
}

include_once '../../../../app/config/db.php';
require_once '../../../models/OrderModel.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "ID đơn hàng không hợp lệ.";
    header("Location: list_orders.php");
    exit;
}

$id = (int)$_GET['id'];
$orderModel = new OrderModel($conn);

// Check if order exists
$order = $orderModel->getById($id);
if (!$order) {
    $_SESSION['error_message'] = "Đơn hàng không tồn tại.";
    header("Location: list_orders.php");
    exit;
}

if ($orderModel->delete($id)) {
    $_SESSION['success_message'] = "Xóa đơn hàng thành công.";
} else {
    $_SESSION['error_message'] = "Xóa đơn hàng thất bại. Vui lòng thử lại.";
}

header("Location: list_orders.php");
exit;
?>