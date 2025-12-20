<?php
// delete_ward.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: ../../auth/login.php");
    exit;
}

include_once '../../../../app/config/db.php';
require_once '../../../models/WardModel.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "ID Xã/Phường không hợp lệ.";
    header("Location: list_wards.php");
    exit;
}

$id = (int)$_GET['id'];
$wardModel = new WardModel($conn);

// Check if ward exists
$ward = $wardModel->getById($id);
if (!$ward) {
    $_SESSION['error_message'] = "Xã/Phường không tồn tại.";
    header("Location: list_wards.php");
    exit;
}

if ($wardModel->delete($id)) {
    $_SESSION['success_message'] = "Xóa Xã/Phường thành công.";
} else {
    $_SESSION['error_message'] = "Xóa Xã/Phường thất bại. Vui lòng thử lại.";
}

header("Location: list_wards.php");
exit;
?>
