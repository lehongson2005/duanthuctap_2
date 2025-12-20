<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/CategoryLevel3Model.php';

session_start();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "ID danh mục cấp 3 không hợp lệ.";
    $_SESSION['message_type'] = "danger";
    header("Location: list_category_level3.php");
    exit();
}

$id = $_GET['id'];
$categoryLevel3Model = new CategoryLevel3Model($conn);

// No need to get item data to delete images anymore.
// The delete method in CategoryLevel3Model will handle the DB record.

if ($categoryLevel3Model->delete($id)) {
    $_SESSION['message'] = "Xóa danh mục cấp 3 thành công!";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "Lỗi: Không thể xóa danh mục cấp 3. Có thể còn sản phẩm liên quan."; // Adjusted message
    $_SESSION['message_type'] = "danger";
}

header("Location: list_category_level3.php");
exit();
?>
