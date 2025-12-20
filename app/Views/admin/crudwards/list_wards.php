<?php
// list_wards.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: ../../auth/login.php");
    exit;
}
include_once '../../../../app/config/db.php';
require_once '../../../models/WardModel.php';
require_once '../../../models/ProvinceModel.php'; // Needed for province filter dropdown
$wardModel = new WardModel($conn);
$provinceModel = new ProvinceModel($conn);

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Filtering and Searching
$keyword = $_GET['keyword'] ?? '';
$province_filter = $_GET['province_id'] ?? '';

// Get total and items
$totalItems = $wardModel->getTotal($keyword, $province_filter);
$totalPages = ceil($totalItems / $limit);
$wards = $wardModel->searchAndFilter($keyword, $province_filter, $limit, $offset);

// Get all provinces for filter dropdown
$provinces_for_filter = $provinceModel->getAll();

$page_title = "Quản lý Xã/Phường";
?>

<?php include '../templates/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Quản lý Xã/Phường</h1>
    <p class="mb-4">Danh sách các Xã/Phường có trên hệ thống.</p>

    <!-- Search and Filter Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Bộ lọc và Tìm kiếm</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="list_wards.php">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="keyword">Tìm kiếm theo tên Xã/Phường hoặc loại</label>
                            <input type="text" class="form-control" id="keyword" name="keyword" placeholder="Nhập từ khóa..." value="<?= htmlspecialchars($keyword) ?>">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="province_id">Lọc theo Tỉnh/Thành phố</label>
                            <select class="form-control" id="province_id" name="province_id">
                                <option value="">Tất cả Tỉnh/Thành phố</option>
                                <?php while ($province = $provinces_for_filter->fetch_assoc()) : ?>
                                    <option value="<?= $province['id'] ?>" <?= ((string)$province_filter === (string)$province['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($province['name']) ?> (<?= htmlspecialchars($province['type']) ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Lọc</button>
                             <a href="list_wards.php" class="btn btn-secondary">Xóa lọc</a>
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
                Danh sách Xã/Phường
                <a href="add_ward.php" class="btn btn-primary btn-sm float-right">Thêm Xã/Phường mới</a>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên Xã/Phường</th>
                            <th>Loại</th>
                            <th>Tỉnh/Thành phố</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($wards->num_rows > 0) : ?>
                            <?php while ($ward = $wards->fetch_assoc()) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($ward['id']) ?></td>
                                    <td><?= htmlspecialchars($ward['name']) ?></td>
                                    <td><?= htmlspecialchars($ward['type']) ?></td>
                                    <td><?= htmlspecialchars($ward['province_name']) ?></td>
                                    <td>
                                        <a href="edit_ward.php?id=<?= $ward['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                                        <a href="delete_ward.php?id=<?= $ward['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa Xã/Phường này? Thao tác này sẽ xóa tất cả Cửa hàng thuộc Xã/Phường này!');">Xóa</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5" class="text-center">Không có Xã/Phường nào.</td>
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
                            <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>&keyword=<?= urlencode($keyword) ?>&province_id=<?= $province_filter ?>">Trước</a></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&keyword=<?= urlencode($keyword) ?>&province_id=<?= $province_filter ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages) : ?>
                            <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>&keyword=<?= urlencode($keyword) ?>&province_id=<?= $province_filter ?>">Sau</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
