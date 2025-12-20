<?php
// add_store.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: ../../auth/login.php");
    exit;
}
include_once '../../../../app/config/db.php';
require_once '../../../models/StoreModel.php';
require_once '../../../models/ProvinceModel.php';
require_once '../../../models/WardModel.php';
$storeModel = new StoreModel($conn);
$provinceModel = new ProvinceModel($conn);
$wardModel = new WardModel($conn);

$page_title = "Thêm Cửa hàng mới";

// Get all provinces for the dropdown
$provinces = $provinceModel->getAll();

// Initialize selected values for form re-population
$selected_province_id = $_POST['province_id'] ?? '';
$selected_ward_id = $_POST['ward_id'] ?? '';
$wards_for_dropdown = null;

// If a province was selected (either via POST or GET for initial dynamic load)
if (!empty($selected_province_id)) {
    $wards_for_dropdown = $wardModel->getByProvinceId($selected_province_id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $province_id = (int)($_POST['province_id'] ?? 0);
    $ward_id = (int)($_POST['ward_id'] ?? 0);
    $phone_number = trim($_POST['phone_number'] ?? '');
    $map_link = trim($_POST['map_link'] ?? '');
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 0; // Default to 0 if not checked
    $latitude = trim($_POST['latitude'] ?? null);
    $longitude = trim($_POST['longitude'] ?? null);

    if (empty($name) || empty($address) || $province_id <= 0 || $ward_id <= 0) {
        $_SESSION['error_message'] = "Vui lòng nhập đầy đủ Tên, Địa chỉ và chọn Tỉnh/Thành phố, Xã/Phường.";
    } else {
        if ($storeModel->create($name, $address, $ward_id, $phone_number, $map_link, $status, $latitude, $longitude)) {
            $_SESSION['success_message'] = "Thêm Cửa hàng thành công.";
            header("Location: list_stores.php");
            exit;
        } else {
            $_SESSION['error_message'] = "Thêm Cửa hàng thất bại. Vui lòng thử lại.";
        }
    }
}
?>

<?php include '../templates/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Thêm Cửa hàng mới</h1>
    <p class="mb-4">Điền thông tin để thêm một cửa hàng mới vào hệ thống.</p>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form thêm Cửa hàng</h6>
        </div>
        <div class="card-body">
            <form action="add_store.php" method="POST">
                <div class="form-group">
                    <label for="name">Tên Cửa hàng:</label>
                    <input type="text" class="form-control" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="address">Địa chỉ (Số nhà, tên đường):</label>
                    <input type="text" class="form-control" id="address" name="address" required value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="province_id">Tỉnh/Thành phố:</label>
                    <select class="form-control" id="province_id" name="province_id" required onchange="this.form.submit()">
                        <option value="">Chọn Tỉnh/Thành phố</option>
                        <?php $provinces->data_seek(0); // Reset result pointer if already fetched ?>
                        <?php while ($province = $provinces->fetch_assoc()) : ?>
                            <option value="<?= $province['id'] ?>" <?= ((string)$selected_province_id === (string)$province['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($province['name']) ?> (<?= htmlspecialchars($province['type']) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="ward_id">Xã/Phường:</label>
                    <select class="form-control" id="ward_id" name="ward_id" required>
                        <option value="">Chọn Xã/Phường</option>
                        <?php if ($wards_for_dropdown) : ?>
                            <?php while ($ward = $wards_for_dropdown->fetch_assoc()) : ?>
                                <option value="<?= $ward['id'] ?>" <?= ((string)$selected_ward_id === (string)$ward['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ward['name']) ?> (<?= htmlspecialchars($ward['type']) ?>)
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="phone_number">Số điện thoại:</label>
                    <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= htmlspecialchars($_POST['phone_number'] ?? '') ?>">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="latitude">Vĩ độ (Latitude):</label>
                            <input type="text" class="form-control" id="latitude" name="latitude" value="<?= htmlspecialchars($_POST['latitude'] ?? '') ?>" placeholder="Ví dụ: 10.7769">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="longitude">Kinh độ (Longitude):</label>
                            <input type="text" class="form-control" id="longitude" name="longitude" value="<?= htmlspecialchars($_POST['longitude'] ?? '') ?>" placeholder="Ví dụ: 106.7009">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="map_link">Link Google Maps:</label>
                    <input type="url" class="form-control" id="map_link" name="map_link" value="<?= htmlspecialchars($_POST['map_link'] ?? '') ?>">
                    <small class="form-text text-muted">Ví dụ: https://goo.gl/maps/example</small>
                </div>
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="status" name="status" value="1" <?= (isset($_POST['status']) && $_POST['status'] == '1') ? 'checked' : '' ?>>
                    <label class="form-check-label" for="status">Đang hoạt động</label>
                </div>
                <button type="submit" class="btn btn-primary">Thêm Cửa hàng</button>
                <a href="list_stores.php" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
