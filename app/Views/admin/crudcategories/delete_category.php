<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/CategoryModel.php';

session_start();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "ID danh mục không hợp lệ.";
    $_SESSION['message_type'] = "danger";
    header("Location: list_categories.php");
    exit();
}

$id = $_GET['id'];
$categoryModel = new CategoryModel($conn);

if ($categoryModel->delete($id)) {
    $_SESSION['message'] = "Xóa danh mục thành công!";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "Lỗi: Không thể xóa danh mục.";
    $_SESSION['message_type'] = "danger";
}

header("Location: list_categories.php");
exit();
?>
