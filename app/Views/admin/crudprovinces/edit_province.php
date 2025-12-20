<?php
// edit_province.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: ../../auth/login.php");
    exit;
}
include_once '../../../../app/config/db.php';
require_once '../../../models/ProvinceModel.php';
$provinceModel = new ProvinceModel($conn);

$page_title = "Sửa Tỉnh/Thành phố";

// Check for ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "ID Tỉnh/Thành phố không hợp lệ.";
    header("Location: list_provinces.php");
    exit;
}
$id = (int)$_GET['id'];

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $type = trim($_POST['type'] ?? '');

    if (empty($name) || empty($type)) {
        $_SESSION['error_message'] = "Vui lòng nhập đầy đủ tên và loại Tỉnh/Thành phố.";
    } else {
        if ($provinceModel->update($id, $name, $type)) {
            $_SESSION['success_message'] = "Cập nhật Tỉnh/Thành phố thành công.";
            header("Location: list_provinces.php");
            exit;
        } else {
            $_SESSION['error_message'] = "Cập nhật Tỉnh/Thành phố thất bại. Vui lòng thử lại.";
        }
    }
}

// Fetch province data for GET request (or if POST failed)
$province = $provinceModel->getById($id);
if (!$province) {
    $_SESSION['error_message'] = "Tỉnh/Thành phố không tồn tại.";
    header("Location: list_provinces.php");
    exit;
}
?>

<?php include '../templates/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Sửa Tỉnh/Thành phố</h1>
    <p class="mb-4">Chỉnh sửa thông tin cho Tỉnh/Thành phố có ID: <?= htmlspecialchars($province['id']) ?>.</p>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form sửa Tỉnh/Thành phố</h6>
        </div>
        <div class="card-body">
            <form action="edit_province.php?id=<?= $id ?>" method="POST">
                <div class="form-group">
                    <label for="name">Tên Tỉnh/Thành phố:</label>
                    <input type="text" class="form-control" id="name" name="name" required value="<?= htmlspecialchars($province['name']) ?>">
                </div>
                <div class="form-group">
                    <label for="type">Loại:</label>
                    <select class="form-control" id="type" name="type" required>
                        <option value="">Chọn loại</option>
                        <option value="Tỉnh" <?= ($province['type'] == 'Tỉnh') ? 'selected' : '' ?>>Tỉnh</option>
                        <option value="Thành phố" <?= ($province['type'] == 'Thành phố') ? 'selected' : '' ?>>Thành phố</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="list_provinces.php" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
