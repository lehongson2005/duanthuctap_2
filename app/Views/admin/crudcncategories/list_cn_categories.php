<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/CamNangCategoryModel.php';

$page_title = "Quản lý Danh mục Cẩm nang";
include_once '../templates/header.php';

$cnCategoryModel = new CamNangCategoryModel($conn);

// Pagination settings
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get search and filter parameters
$keyword = $_GET['keyword'] ?? '';
$status = $_GET['status'] ?? '';

// Get total records for pagination
$total_records = $cnCategoryModel->getTotal($keyword, $status);
$total_pages = ceil($total_records / $limit);

// Get records for the current page
$items = $cnCategoryModel->searchAndFilter($keyword, $status, $limit, $offset);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0"><?php echo $page_title; ?></h2>
        <p class="text-muted small">Danh sách toàn bộ danh mục cẩm nang</p>
    </div>
    <a href="add_cn_category.php" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus me-2"></i>Thêm Danh mục
    </a>
</div>

<!-- Search and Filter Form -->
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body">
        <form method="GET" action="">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="keyword" class="form-control" placeholder="Tìm theo tên danh mục..." value="<?php echo htmlspecialchars($keyword); ?>">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">-- Lọc theo trạng thái --</option>
                        <option value="1" <?php echo ($status === '1') ? 'selected' : ''; ?>>Active</option>
                        <option value="0" <?php echo ($status === '0') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-search"></i> Lọc</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4 overflow-hidden">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 py-3">Tên Danh mục</th>
                        <th class="py-3">Slug</th>
                        <th class="py-3 text-center">Trạng thái</th>
                        <th class="pe-4 py-3 text-end" style="width: 120px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($items->num_rows > 0): ?>
                        <?php while($row = $items->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['name']); ?></div>
                            </td>
                            <td><?php echo htmlspecialchars($row['slug']); ?></td>
                            <td class="text-center">
                                <?php echo ($row['status']==1)?'<span class="badge bg-success">Active</span>':'<span class="badge bg-danger">Inactive</span>'; ?>
                            </td>
                            <td class="pe-4 text-end">
                                <a href="edit_cn_category.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                <a href="delete_cn_category.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa danh mục này? Việc này có thể ảnh hưởng đến các bài viết cẩm nang thuộc danh mục này.')"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">Không tìm thấy danh mục nào.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>&keyword=<?php echo urlencode($keyword); ?>&status=<?php echo urlencode($status); ?>">Previous</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>"><a class="page-link" href="?page=<?php echo $i; ?>&keyword=<?php echo urlencode($keyword); ?>&status=<?php echo urlencode($status); ?>"><?php echo $i; ?></a></li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>&keyword=<?php echo urlencode($keyword); ?>&status=<?php echo urlencode($status); ?>">Next</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>

<?php
include_once '../templates/footer.php';
?>
