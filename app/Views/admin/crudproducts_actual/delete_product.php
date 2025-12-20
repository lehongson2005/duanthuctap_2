<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/ProductModel.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $productModel = new ProductModel($conn);
    
    if ($productModel->delete($id)) {
        $_SESSION['message'] = "Xóa sản phẩm thành công!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Lỗi: Không thể xóa sản phẩm.";
        $_SESSION['message_type'] = "danger";
    }
} else {
    $_SESSION['message'] = "ID sản phẩm không hợp lệ.";
    $_SESSION['message_type'] = "danger";
}

header("Location: list_products.php");
exit();
?>
