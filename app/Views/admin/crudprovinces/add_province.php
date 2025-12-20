<?php
// add_province.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: ../../auth/login.php");
    exit;
}
include_once '../../../../app/config/db.php';
require_once '../../../models/ProvinceModel.php';
$provinceModel = new ProvinceModel($conn);

$page_title = "Thêm Tỉnh/Thành phố mới";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $type = trim($_POST['type'] ?? '');

    if (empty($name) || empty($type)) {
        $_SESSION['error_message'] = "Vui lòng nhập đầy đủ tên và loại Tỉnh/Thành phố.";
    } else {
        if ($provinceModel->create($name, $type)) {
            $_SESSION['success_message'] = "Thêm Tỉnh/Thành phố thành công.";
            header("Location: list_provinces.php");
            exit;
        } else {
            $_SESSION['error_message'] = "Thêm Tỉnh/Thành phố thất bại. Vui lòng thử lại.";
        }
    }
}
?>

<?php include '../templates/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Thêm Tỉnh/Thành phố mới</h1>
    <p class="mb-4">Điền thông tin để thêm một Tỉnh/Thành phố mới vào hệ thống.</p>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form thêm Tỉnh/Thành phố</h6>
        </div>
        <div class="card-body">
            <form action="add_province.php" method="POST">
                <div class="form-group">
                    <label for="name">Tên Tỉnh/Thành phố:</label>
                    <input type="text" class="form-control" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="type">Loại:</label>
                    <select class="form-control" id="type" name="type" required>
                        <option value="">Chọn loại</option>
                        <option value="Tỉnh" <?= (($_POST['type'] ?? '') == 'Tỉnh') ? 'selected' : '' ?>>Tỉnh</option>
                        <option value="Thành phố" <?= (($_POST['type'] ?? '') == 'Thành phố') ? 'selected' : '' ?>>Thành phố</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Thêm</button>
                <a href="list_provinces.php" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
