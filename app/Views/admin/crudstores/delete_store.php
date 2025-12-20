<?php
// delete_store.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: ../../auth/login.php");
    exit;
}

include_once '../../../../app/config/db.php';
require_once '../../../models/StoreModel.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "ID Cửa hàng không hợp lệ.";
    header("Location: list_stores.php");
    exit;
}

$id = (int)$_GET['id'];
$storeModel = new StoreModel($conn);

// Check if store exists
$store = $storeModel->getById($id);
if (!$store) {
    $_SESSION['error_message'] = "Cửa hàng không tồn tại.";
    header("Location: list_stores.php");
    exit;
}

if ($storeModel->delete($id)) {
    $_SESSION['success_message'] = "Xóa Cửa hàng thành công.";
} else {
    $_SESSION['error_message'] = "Xóa Cửa hàng thất bại. Vui lòng thử lại.";
}

header("Location: list_stores.php");
exit;
?>
