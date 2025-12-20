<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/CamNangCategoryModel.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $cnCategoryModel = new CamNangCategoryModel($conn);
    
    // Optional: Check if any posts are associated with this category before deleting.
    // For now, we proceed with direct deletion.
    if ($cnCategoryModel->delete($id)) {
        $_SESSION['message'] = "Xóa danh mục thành công!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Lỗi: Không thể xóa danh mục. Có thể vẫn còn bài viết trong danh mục này.";
        $_SESSION['message_type'] = "danger";
    }
} else {
    $_SESSION['message'] = "ID không hợp lệ.";
    $_SESSION['message_type'] = "danger";
}

header("Location: list_cn_categories.php");
exit();
?>
