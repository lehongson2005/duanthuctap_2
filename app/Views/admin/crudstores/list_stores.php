<?php
// list_stores.php
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

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Filtering and Searching
$keyword = $_GET['keyword'] ?? '';
$province_filter = $_GET['province_id'] ?? '';
$ward_filter = $_GET['ward_id'] ?? '';
$status_filter = $_GET['status'] ?? '';

// Get total and items
$totalItems = $storeModel->getTotal($keyword, $province_filter, $ward_filter, $status_filter);
$totalPages = ceil($totalItems / $limit);
$stores = $storeModel->searchAndFilter($keyword, $province_filter, $ward_filter, $status_filter, $limit, $offset);

// Get all provinces for filter dropdown
$provinces_for_filter = $provinceModel->getAll();
// Get wards for filter dropdown (if a province is selected)
$wards_for_filter = null;
if (!empty($province_filter)) {
    $wards_for_filter = $wardModel->getByProvinceId($province_filter);
}

$page_title = "Quản lý Cửa hàng";
?>

<?php include '../templates/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Quản lý Cửa hàng</h1>
    <p class="mb-4">Danh sách các Cửa hàng có trên hệ thống.</p>

    <!-- Search and Filter Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Bộ lọc và Tìm kiếm</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="list_stores.php">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="keyword">Tìm kiếm</label>
                            <input type="text" class="form-control" id="keyword" name="keyword" placeholder="Tên, địa chỉ, SĐT..." value="<?= htmlspecialchars($keyword) ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="province_id">Tỉnh/Thành phố</label>
                            <select class="form-control" id="province_id" name="province_id" onchange="this.form.submit()">
                                <option value="">Tất cả Tỉnh/Thành phố</option>
                                <?php while ($province = $provinces_for_filter->fetch_assoc()) : ?>
                                    <option value="<?= $province['id'] ?>" <?= ((string)$province_filter === (string)$province['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($province['name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ward_id">Xã/Phường</label>
                            <select class="form-control" id="ward_id" name="ward_id">
                                <option value="">Tất cả Xã/Phường</option>
                                <?php if ($wards_for_filter && $wards_for_filter->num_rows > 0) : ?>
                                    <?php while ($ward = $wards_for_filter->fetch_assoc()) : ?>
                                        <option value="<?= $ward['id'] ?>" <?= ((string)$ward_filter === (string)$ward['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($ward['name']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                     <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Trạng thái</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">Tất cả</option>
                                <option value="1" <?= ((string)$status_filter === '1') ? 'selected' : '' ?>>Đang hoạt động</option>
                                <option value="0" <?= ((string)$status_filter === '0') ? 'selected' : '' ?>>Ngừng hoạt động</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12 d-flex justify-content-end">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Lọc</button>
                             <a href="list_stores.php" class="btn btn-secondary">Xóa lọc</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Danh sách Cửa hàng
                <a href="add_store.php" class="btn btn-primary btn-sm float-right">Thêm Cửa hàng mới</a>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên Cửa hàng</th>
                            <th>Địa chỉ</th>
                            <th>Điện thoại</th>
                            <th>Tỉnh/Thành phố</th>
                            <th>Xã/Phường</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($stores->num_rows > 0) : ?>
                            <?php while ($store = $stores->fetch_assoc()) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($store['id']) ?></td>
                                    <td><?= htmlspecialchars($store['name']) ?></td>
                                    <td><?= htmlspecialchars($store['address']) ?></td>
                                    <td><?= htmlspecialchars($store['phone_number']) ?></td>
                                    <td><?= htmlspecialchars($store['province_name']) ?></td>
                                    <td><?= htmlspecialchars($store['ward_name']) ?></td>
                                    <td>
                                        <span class="badge <?= $store['status'] ? 'badge-success' : 'badge-danger' ?>">
                                            <?= $store['status'] ? 'Đang hoạt động' : 'Ngừng hoạt động' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= htmlspecialchars($store['map_link']) ?>" target="_blank" class="btn btn-info btn-sm" title="Xem trên bản đồ"><i class="fas fa-map-marked-alt"></i></a>
                                        <a href="edit_store.php?id=<?= $store['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                                        <a href="delete_store.php?id=<?= $store['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa Cửa hàng này?');">Xóa</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="8" class="text-center">Không có Cửa hàng nào.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1) : ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1) : ?>
                            <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>&keyword=<?= urlencode($keyword) ?>&province_id=<?= $province_filter ?>&ward_id=<?= $ward_filter ?>&status=<?= $status_filter ?>">Trước</a></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&keyword=<?= urlencode($keyword) ?>&province_id=<?= $province_filter ?>&ward_id=<?= $ward_filter ?>&status=<?= $status_filter ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages) : ?>
                            <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>&keyword=<?= urlencode($keyword) ?>&province_id=<?= $province_filter ?>&ward_id=<?= $ward_filter ?>&status=<?= $status_filter ?>">Sau</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
