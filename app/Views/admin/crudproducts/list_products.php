<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/CategoryLevel2Model.php'; 
include_once '../../../../app/models/CategoryModel.php';

$page_title = "Quản lý Danh mục Cấp 2 (Phân Loại)"; 
include_once '../templates/header.php';

$categoryLevel2Model = new CategoryLevel2Model($conn); 
$categoryModel = new CategoryModel($conn);

// Lấy danh mục cấp 1 để lọc
$all_categories = $categoryModel->getAll();

// Cấu hình phân trang
$limit = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Lấy tham số tìm lọc
$keyword = $_GET['keyword'] ?? '';
$category_id = $_GET['category_id'] ?? '';
$status = $_GET['status'] ?? '';

// Tổng số bản ghi
$total_records = $categoryLevel2Model->getTotal($keyword, $category_id, $status); 
$total_pages = ceil($total_records / $limit);

// Lấy dữ liệu cho trang hiện tại
$products = $categoryLevel2Model->searchAndFilter($keyword, $category_id, $status, $limit, $offset); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0"><?php echo $page_title; ?></h2>
        <p class="text-muted small">Danh sách toàn bộ danh mục cấp 2 (phân loại sản phẩm)</p>
    </div>
    <a href="add_product.php" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus me-2"></i>Thêm Danh mục Cấp 2
    </a>
</div>

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body">
        <form method="GET" action="">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="keyword" class="form-control" placeholder="Tìm theo SKU, tên danh mục..." value="<?php echo htmlspecialchars($keyword); ?>">
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-select">
                        <option value="">-- Lọc theo danh mục cấp 1 --</option>
                        <?php mysqli_data_seek($all_categories, 0); ?>
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
                        <th class="ps-4 py-3" style="width: 80px;">Ảnh</th> <th class="py-3">Tên Danh mục Cấp 2</th>
                        <th class="py-3">SKU</th>
                        <th class="py-3">Danh mục Cấp 1</th>
                        <th class="py-3 text-center">Trạng thái</th>
                        <th class="pe-4 py-3 text-end" style="width: 120px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($products->num_rows > 0): ?>
                        <?php while($row = $products->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-4">
                                <?php if (!empty($row['image'])): ?>
                                    <img src="../../../../<?php echo htmlspecialchars($row['image']); ?>" 
                                         alt="Category Image" 
                                         class="rounded shadow-sm" 
                                         style="width: 50px; height: 50px; object-fit: cover; border: 1px solid #eee;">
                                <?php else: ?>
                                    <div class="rounded bg-light d-flex align-items-center justify-content-center border" 
                                         style="width: 50px; height: 50px; font-size: 10px; color: #ccc;">
                                         No Img
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['name']); ?></div>
                            </td>
                            <td><code class="text-primary small"><?php echo htmlspecialchars($row['sku']); ?></code></td>
                            <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($row['category_name']); ?></span></td>
                            <td class="text-center">
                                <?php echo ($row['status']==1)?'<span class="badge bg-success-subtle text-success">Active</span>':'<span class="badge bg-danger-subtle text-danger">Inactive</span>'; ?>
                            </td>
                            <td class="pe-4 text-end">
                                <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-warning btn-sm"><i class="fas fa-edit"></i></a>
                                <a href="delete_product.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?')"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Không tìm thấy danh mục cấp 2 nào.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination pagination-sm justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>&keyword=<?php echo urlencode($keyword); ?>&category_id=<?php echo $category_id; ?>&status=<?php echo urlencode($status); ?>"><i class="fas fa-chevron-left"></i></a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>"><a class="page-link" href="?page=<?php echo $i; ?>&keyword=<?php echo urlencode($keyword); ?>&category_id=<?php echo $category_id; ?>&status=<?php echo urlencode($status); ?>"><?php echo $i; ?></a></li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>&keyword=<?php echo urlencode($keyword); ?>&category_id=<?php echo $category_id; ?>&status=<?php echo urlencode($status); ?>"><i class="fas fa-chevron-right"></i></a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<?php
include_once '../templates/footer.php';
?>