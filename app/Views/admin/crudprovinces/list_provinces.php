<?php
// list_provinces.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: ../../auth/login.php");
    exit;
}
include_once '../../../../app/config/db.php';
require_once '../../../models/ProvinceModel.php';
$provinceModel = new ProvinceModel($conn);

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Filtering and Searching
$keyword = $_GET['keyword'] ?? '';

// Get total and items
$totalItems = $provinceModel->getTotal($keyword);
$totalPages = ceil($totalItems / $limit);
$provinces = $provinceModel->searchAndFilter($keyword, $limit, $offset);

$page_title = "Quản lý Tỉnh/Thành phố";
?>

<?php include '../templates/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Quản lý Tỉnh/Thành phố</h1>
    <p class="mb-4">Danh sách các Tỉnh/Thành phố có trên hệ thống.</p>

    <!-- Search Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tìm kiếm</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="list_provinces.php">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="keyword">Tìm kiếm theo tên Tỉnh/Thành phố hoặc loại</label>
                            <input type="text" class="form-control" id="keyword" name="keyword" placeholder="Nhập từ khóa..." value="<?= htmlspecialchars($keyword) ?>">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                             <a href="list_provinces.php" class="btn btn-secondary">Xóa lọc</a>
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
                Danh sách Tỉnh/Thành phố
                <a href="add_province.php" class="btn btn-primary btn-sm float-right">Thêm Tỉnh/Thành phố mới</a>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên Tỉnh/Thành phố</th>
                            <th>Loại</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($provinces->num_rows > 0) : ?>
                            <?php while ($province = $provinces->fetch_assoc()) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($province['id']) ?></td>
                                    <td><?= htmlspecialchars($province['name']) ?></td>
                                    <td><?= htmlspecialchars($province['type']) ?></td>
                                    <td>
                                        <a href="edit_province.php?id=<?= $province['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                                        <a href="delete_province.php?id=<?= $province['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa Tỉnh/Thành phố này? Thao tác này sẽ xóa tất cả Xã/Phường và Cửa hàng thuộc Tỉnh này!');">Xóa</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="4" class="text-center">Không có Tỉnh/Thành phố nào.</td>
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
                            <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>&keyword=<?= urlencode($keyword) ?>">Trước</a></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&keyword=<?= urlencode($keyword) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages) : ?>
                            <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>&keyword=<?= urlencode($keyword) ?>">Sau</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
