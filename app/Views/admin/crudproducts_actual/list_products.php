<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/ProductModel.php'; // The NEW ProductModel
include_once '../../../../app/models/CategoryModel.php';
include_once '../../../../app/models/CategoryLevel2Model.php';
include_once '../../../../app/models/CategoryLevel3Model.php';

$page_title = "Quản lý Sản phẩm";
include_once '../templates/header.php';

$productModel = new ProductModel($conn);
$categoryModel = new CategoryModel($conn);
$categoryLevel2Model = new CategoryLevel2Model($conn);
$categoryLevel3Model = new CategoryLevel3Model($conn);

// Get all categories for filter dropdowns
$all_categories_level1 = $categoryModel->getAll();
$all_categories_level2 = $categoryLevel2Model->searchAndFilter();
$all_categories_level3 = $categoryLevel3Model->searchAndFilter();

// Pagination settings
$limit = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get search and filter parameters
$keyword = $_GET['keyword'] ?? '';
$category_level1_id = $_GET['category_level1_id'] ?? '';
$category_level2_id = $_GET['category_level2_id'] ?? '';
$category_level3_id = $_GET['category_level3_id'] ?? '';
$status = $_GET['status'] ?? '';

// Get total records for pagination
$total_records = $productModel->getTotal($keyword, $category_level1_id, $category_level2_id, $category_level3_id, $status);
$total_pages = ceil($total_records / $limit);

// Get records for the current page
$products = $productModel->searchAndFilter($keyword, $category_level1_id, $category_level2_id, $category_level3_id, $status, '', 'newest', $limit, $offset);

?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0"><?php echo $page_title; ?></h2>
        <p class="text-muted small">Danh sách toàn bộ sản phẩm trong hệ thống</p>
    </div>
    <a href="add_product.php" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus me-2"></i>Thêm Sản phẩm
    </a>
</div>

<!-- Search and Filter Form -->
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body">
        <form method="GET" action="">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="keyword" class="form-control" placeholder="Tìm theo SKU, tên sản phẩm..." value="<?php echo htmlspecialchars($keyword); ?>">
                </div>
                <div class="col-md-2">
                    <select name="category_level1_id" class="form-select">
                        <option value="">-- Cấp 1 --</option>
                        <?php mysqli_data_seek($all_categories_level1, 0); // Reset pointer ?>
                        <?php while ($cat = $all_categories_level1->fetch_assoc()): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($category_level1_id == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="category_level2_id" class="form-select">
                        <option value="">-- Cấp 2 --</option>
                        <?php mysqli_data_seek($all_categories_level2, 0); // Reset pointer ?>
                        <?php while ($cat = $all_categories_level2->fetch_assoc()): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($category_level2_id == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="category_level3_id" class="form-select">
                        <option value="">-- Cấp 3 --</option>
                        <?php mysqli_data_seek($all_categories_level3, 0); // Reset pointer ?>
                        <?php while ($cat = $all_categories_level3->fetch_assoc()): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($category_level3_id == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-1">
                    <select name="status" class="form-select">
                        <option value="">-- Trạng thái --</option>
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
                        <th class="ps-4 py-3">Sản phẩm</th>
                        <th class="py-3">SKU</th>
                        <th class="py-3">Giá</th>
                        <th class="py-3 text-center">Tồn kho</th>
                        <th class="py-3">Danh mục</th>
                        <th class="py-3 text-center">Trạng thái</th>
                        <th class="pe-4 py-3 text-end" style="width: 120px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($products->num_rows > 0): ?>
                        <?php while($row = $products->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo BASE_URL . '/' . htmlspecialchars($row['thumbnail'] ?? 'public/uploads/default.png'); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="me-3 rounded-3" style="width: 60px; height: 60px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['name']); ?></div>
                                        <div class="d-flex small mt-1">
                                            <?php if($row['is_featured']==1): ?><span class="badge bg-warning me-1">Nổi bật</span><?php endif; ?>
                                            <?php if($row['is_new']==1): ?><span class="badge bg-info">Mới</span><?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($row['sku']); ?></td>
                            <td><?php echo number_format($row['price'], 0, ',', '.'); ?>đ</td>
                            <td class="text-center"><?php echo $row['stock_quantity']; ?></td>
                            <td>
                                <?php if($row['category_level1_name']): ?><small class="d-block"><?php echo htmlspecialchars($row['category_level1_name']); ?></small><?php endif; ?>
                                <?php if($row['category_level2_name']): ?><small class="d-block text-muted ps-2">- <?php echo htmlspecialchars($row['category_level2_name']); ?></small><?php endif; ?>
                                <?php if($row['category_level3_name']): ?><small class="d-block text-muted ps-4">-- <?php echo htmlspecialchars($row['category_level3_name']); ?></small><?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php echo ($row['status']==1)?'<span class="badge bg-success">Active</span>':'<span class="badge bg-danger">Inactive</span>'; ?>
                            </td>
                            <td class="pe-4 text-end">
                                <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                <a href="delete_product.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Không tìm thấy sản phẩm nào.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>&keyword=<?php echo urlencode($keyword); ?>&category_level1_id=<?php echo $category_level1_id; ?>&category_level2_id=<?php echo $category_level2_id; ?>&category_level3_id=<?php echo $category_level3_id; ?>&status=<?php echo urlencode($status); ?>">Previous</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>"><a class="page-link" href="?page=<?php echo $i; ?>&keyword=<?php echo urlencode($keyword); ?>&category_level1_id=<?php echo $category_level1_id; ?>&category_level2_id=<?php echo $category_level2_id; ?>&category_level3_id=<?php echo $category_level3_id; ?>&status=<?php echo urlencode($status); ?>"><?php echo $i; ?></a></li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>&keyword=<?php echo urlencode($keyword); ?>&category_level1_id=<?php echo $category_level1_id; ?>&category_level2_id=<?php echo $category_level2_id; ?>&category_level3_id=<?php echo $category_level3_id; ?>&status=<?php echo urlencode($status); ?>">Next</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>

<?php
include_once '../templates/footer.php';
?>