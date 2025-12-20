<?php
// edit_ward.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: ../../auth/login.php");
    exit;
}
include_once '../../../../app/config/db.php';
require_once '../../../models/WardModel.php';
require_once '../../../models/ProvinceModel.php'; // For province dropdown
$wardModel = new WardModel($conn);
$provinceModel = new ProvinceModel($conn);

$page_title = "Sửa Xã/Phường";

// Check for ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "ID Xã/Phường không hợp lệ.";
    header("Location: list_wards.php");
    exit;
}
$id = (int)$_GET['id'];

// Get all provinces for the dropdown
$provinces = $provinceModel->getAll();

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $province_id = (int)($_POST['province_id'] ?? 0);

    if (empty($name) || empty($type) || $province_id <= 0) {
        $_SESSION['error_message'] = "Vui lòng nhập đầy đủ tên, loại Xã/Phường và chọn Tỉnh/Thành phố.";
    } else {
        if ($wardModel->update($id, $name, $type, $province_id)) {
            $_SESSION['success_message'] = "Cập nhật Xã/Phường thành công.";
            header("Location: list_wards.php");
            exit;
        } else {
            $_SESSION['error_message'] = "Cập nhật Xã/Phường thất bại. Vui lòng thử lại.";
        }
    }
}

// Fetch ward data for GET request (or if POST failed)
$ward = $wardModel->getById($id);
if (!$ward) {
    $_SESSION['error_message'] = "Xã/Phường không tồn tại.";
    header("Location: list_wards.php");
    exit;
}
?>

<?php include '../templates/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Sửa Xã/Phường</h1>
    <p class="mb-4">Chỉnh sửa thông tin cho Xã/Phường có ID: <?= htmlspecialchars($ward['id']) ?>.</p>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form sửa Xã/Phường</h6>
        </div>
        <div class="card-body">
            <form action="edit_ward.php?id=<?= $id ?>" method="POST">
                <div class="form-group">
                    <label for="name">Tên Xã/Phường:</label>
                    <input type="text" class="form-control" id="name" name="name" required value="<?= htmlspecialchars($ward['name']) ?>">
                </div>
                <div class="form-group">
                    <label for="type">Loại:</label>
                    <select class="form-control" id="type" name="type" required>
                        <option value="">Chọn loại</option>
                        <option value="Xã" <?= ($ward['type'] == 'Xã') ? 'selected' : '' ?>>Xã</option>
                        <option value="Phường" <?= ($ward['type'] == 'Phường') ? 'selected' : '' ?>>Phường</option>
                        <option value="Thị trấn" <?= ($ward['type'] == 'Thị trấn') ? 'selected' : '' ?>>Thị trấn</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="province_id">Tỉnh/Thành phố:</label>
                    <select class="form-control" id="province_id" name="province_id" required>
                        <option value="">Chọn Tỉnh/Thành phố</option>
                        <?php $provinces->data_seek(0); // Reset result pointer ?>
                        <?php while ($province = $provinces->fetch_assoc()) : ?>
                            <option value="<?= $province['id'] ?>" <?= ((string)$ward['province_id'] === (string)$province['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($province['name']) ?> (<?= htmlspecialchars($province['type']) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="list_wards.php" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
