<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/IconMenuModel.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $iconMenuModel = new IconMenuModel($conn);
    
    if ($iconMenuModel->delete($id)) {
        $_SESSION['message'] = "Xóa mục thành công!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Lỗi: Không thể xóa mục.";
        $_SESSION['message_type'] = "danger";
    }
} else {
    $_SESSION['message'] = "ID không hợp lệ.";
    $_SESSION['message_type'] = "danger";
}

header("Location: list_icon_menu.php");
exit();
?>
