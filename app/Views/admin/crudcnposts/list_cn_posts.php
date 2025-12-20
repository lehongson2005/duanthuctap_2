<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/CamNangPostModel.php';
include_once '../../../../app/models/CamNangCategoryModel.php';

$page_title = "Quản lý Bài viết Cẩm nang";
include_once '../templates/header.php';

$postModel = new CamNangPostModel($conn);
$categoryModel = new CamNangCategoryModel($conn);

$all_categories = $categoryModel->getAll();

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$keyword = $_GET['keyword'] ?? '';
$category_id = $_GET['category_id'] ?? '';
$status = $_GET['status'] ?? '';

// Get total records for pagination
$total_records = $postModel->getTotal($keyword, $category_id, $status);


$total_pages = ceil($total_records / $limit);

// Get records for the current page
$items = $postModel->searchAndFilter($keyword, $category_id, $status, null, $limit, $offset);

?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0"><?php echo $page_title; ?></h2>
        <p class="text-muted small">Danh sách toàn bộ bài viết cẩm nang</p>
    </div>
    <a href="add_cn_post.php" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus me-2"></i>Thêm Bài viết
    </a>
</div>

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body">
        <form method="GET" action="">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="keyword" class="form-control" placeholder="Tìm theo tiêu đề..." value="<?php echo htmlspecialchars($keyword); ?>">
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-select">
                        <option value="">-- Lọc theo danh mục --</option>
                        <?php while ($cat = $all_categories->fetch_assoc()): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($category_id == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">-- Lọc theo trạng thái --</option>
                        <option value="1" <?php echo ($status === '1') ? 'selected' : ''; ?>>Published</option>
                        <option value="0" <?php echo ($status === '0') ? 'selected' : ''; ?>>Draft</option>
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
                        <th class="ps-4 py-3">Bài viết</th>
                        <th class="py-3">Danh mục</th>
                        <th class="py-3">Trạng thái</th>
                        <th class="py-3">Ngày đăng</th>
                        <th class="pe-4 py-3 text-end" style="width: 120px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($items->num_rows > 0): ?>
                        <?php while($row = $items->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo BASE_URL . '/' . htmlspecialchars($row['thumbnail'] ?? 'public/uploads/default.png'); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="me-3 rounded-3" style="width: 80px; height: 60px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['title']); ?></div>
                                        <?php if($row['is_featured']==1): ?><span class="badge bg-warning me-1">Nổi bật</span><?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td>
                                <?php echo ($row['status']==1)?'<span class="badge bg-success">Published</span>':'<span class="badge bg-secondary">Draft</span>'; ?>
                            </td>
                            <td><?php echo $row['published_at'] ? date('d/m/Y H:i', strtotime($row['published_at'])) : 'Chưa đăng'; ?></td>
                            <td class="pe-4 text-end">
                                <a href="edit_cn_post.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                <a href="delete_cn_post.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa bài viết này?')"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Không tìm thấy bài viết nào.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>&keyword=<?php echo urlencode($keyword); ?>&category_id=<?php echo $category_id; ?>&status=<?php echo urlencode($status); ?>">Previous</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>"><a class="page-link" href="?page=<?php echo $i; ?>&keyword=<?php echo urlencode($keyword); ?>&category_id=<?php echo $category_id; ?>&status=<?php echo urlencode($status); ?>"><?php echo $i; ?></a></li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>&keyword=<?php echo urlencode($keyword); ?>&category_id=<?php echo $category_id; ?>&status=<?php echo urlencode($status); ?>">Next</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>

<?php
include_once '../templates/footer.php';
?>
