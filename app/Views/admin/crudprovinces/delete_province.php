<?php
// delete_province.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: ../../auth/login.php");
    exit;
}

include_once '../../../../app/config/db.php';
require_once '../../../models/ProvinceModel.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "ID Tỉnh/Thành phố không hợp lệ.";
    header("Location: list_provinces.php");
    exit;
}

$id = (int)$_GET['id'];
$provinceModel = new ProvinceModel($conn);

// Check if province exists
$province = $provinceModel->getById($id);
if (!$province) {
    $_SESSION['error_message'] = "Tỉnh/Thành phố không tồn tại.";
    header("Location: list_provinces.php");
    exit;
}

if ($provinceModel->delete($id)) {
    $_SESSION['success_message'] = "Xóa Tỉnh/Thành phố thành công.";
} else {
    $_SESSION['error_message'] = "Xóa Tỉnh/Thành phố thất bại. Vui lòng thử lại.";
}

header("Location: list_provinces.php");
exit;
?>