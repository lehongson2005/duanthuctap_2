<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/BannerModel.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $bannerModel = new BannerModel($conn);
    
    if ($bannerModel->delete($id)) {
        $_SESSION['message'] = "Xóa banner thành công!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Lỗi: Không thể xóa banner.";
        $_SESSION['message_type'] = "danger";
    }
} else {
    $_SESSION['message'] = "ID banner không hợp lệ.";
    $_SESSION['message_type'] = "danger";
}

header("Location: list_banner.php");
exit();
?>
