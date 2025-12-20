<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/PostModel.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $postModel = new PostModel($conn);
    
    if ($postModel->delete($id)) {
        $_SESSION['message'] = "Xóa bài viết thành công!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Lỗi: Không thể xóa bài viết.";
        $_SESSION['message_type'] = "danger";
    }
} else {
    $_SESSION['message'] = "ID bài viết không hợp lệ.";
    $_SESSION['message_type'] = "danger";
}

header("Location: list_posts.php");
exit();
?>
