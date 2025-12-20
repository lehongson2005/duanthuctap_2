<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/IconMenuModel.php';

$page_title = "Quản lý Icon Menu";
include_once '../templates/header.php';

$iconMenuModel = new IconMenuModel($conn);

// Pagination settings
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get search and filter parameters
$keyword = $_GET['keyword'] ?? '';
$status = $_GET['status'] ?? '';

// Get total records for pagination
$total_records = $iconMenuModel->getTotal($keyword, $status);
$total_pages = ceil($total_records / $limit);

// Get records for the current page
$items = $iconMenuModel->searchAndFilter($keyword, $status, $limit, $offset);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0"><?php echo $page_title; ?></h2>
        <p class="text-muted small">Danh sách toàn bộ icon menu trong hệ thống</p>
    </div>
    <a href="add_icon_menu.php" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus me-2"></i>Thêm Icon Menu
    </a>
</div>

<!-- Search and Filter Form -->
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body">
        <form method="GET" action="">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="keyword" class="form-control" placeholder="Tìm theo tiêu đề..." value="<?php echo htmlspecialchars($keyword); ?>">
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
                        <th class="ps-4 py-3">Icon</th>
                        <th class="py-3">Tiêu đề</th>
                        <th class="py-3">Link</th>
                        <th class="py-3 text-center">Thứ tự</th>
                        <th class="py-3 text-center">Trạng thái</th>
                        <th class="pe-4 py-3 text-end" style="width: 120px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($items->num_rows > 0): ?>
                        <?php while($row = $items->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-4">
                                <img src="<?php echo BASE_URL . '/' . htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="me-3 rounded-3" style="width: 40px; height: 40px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['title']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($row['slug']); ?></small>
                            </td>
                            <td><a href="<?php echo htmlspecialchars($row['link']); ?>" target="_blank"><?php echo htmlspecialchars($row['link']); ?></a></td>
                            <td class="text-center"><?php echo $row['sort_order']; ?></td>
                            <td class="text-center">
                                <?php echo ($row['status']==1)?'<span class="badge bg-success">Active</span>':'<span class="badge bg-danger">Inactive</span>'; ?>
                            </td>
                            <td class="pe-4 text-end">
                                <a href="edit_icon_menu.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                <a href="delete_icon_menu.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa mục này?')"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Không tìm thấy mục nào.</td>
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
