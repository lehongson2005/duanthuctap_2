<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/CategoryLevel2Model.php'; // Updated model

session_start();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "ID danh mục cấp 2 không hợp lệ.";
    $_SESSION['message_type'] = "danger";
    header("Location: list_products.php");
    exit();
}

$id = $_GET['id'];
$categoryLevel2Model = new CategoryLevel2Model($conn); // Updated model instantiation

// No need to get product data to delete images anymore.
// The delete method in CategoryLevel2Model will handle the DB record.

if ($categoryLevel2Model->delete($id)) { // Updated model call
    $_SESSION['message'] = "Xóa danh mục cấp 2 thành công!";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "Lỗi: Không thể xóa danh mục cấp 2. Có thể còn danh mục cấp 3 liên quan.";
    $_SESSION['message_type'] = "danger";
}

header("Location: list_products.php");
exit();
?>
