<?php
// FILE: /danhmuc.php

// This file needs the header to define BASE_URL and start session
include_once __DIR__ . '/app/Views/user/header.php';
include_once __DIR__ . '/app/config/db.php';
include_once __DIR__ . '/app/models/CategoryModel.php';
include_once __DIR__ . '/app/models/CategoryLevel2Model.php';
include_once __DIR__ . '/app/models/ProductModel.php';

// Instantiate models
$categoryModel = new CategoryModel($conn);
$categoryLevel2Model = new CategoryLevel2Model($conn);
$productModel = new ProductModel($conn);

// Get Category ID from URL
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sub_category_id = isset($_GET['sub_id']) ? (int)$_GET['sub_id'] : 0;

if ($category_id <= 0) {
    header("Location: " . BASE_URL . "/index.php");
    exit();
}

// Fetch main category details
$main_category = $categoryModel->getById($category_id);
if (!$main_category) {
    header("Location: " . BASE_URL . "/index.php");
    exit();
}
$page_title = $main_category['name'];

// Fetch Level 2 sub-categories for this main category
$sub_categories = $categoryLevel2Model->searchAndFilter('', $category_id, 1); // Get active L2 cats for this parent L1

// Fetch products based on main category and optionally sub-category
$all_products = $productModel->searchAndFilter(
    '',                      // keyword
    $category_id,            // category_level1_id
    $sub_category_id > 0 ? $sub_category_id : '', // category_level2_id (if sub_id is set)
    '',                      // category_level3_id
    '1',                     // status (active)
    '',                      // is_featured
    null, null               // limit, offset
);

// --- HEADER ---
// Already included above

?>

<style>
/* --- CSS for Sub-category icons (copied from icon.php) --- */
.nnp-icon-menu-wrapper {
    display: block;
    margin: 15px 0;
    padding: 10px 15px;
    background-color: #fff;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
}
.nnp-icon-menu-scroll {
    overflow-x: auto;
    white-space: nowrap;
    -webkit-overflow-scrolling: touch;
    padding: 0;
    min-width: 100%;
}
.nnp-icon-menu-scroll::-webkit-scrollbar { display: none; }
.nnp-icon-menu-scroll { -ms-overflow-style: none; scrollbar-width: none; }

.nnp-icon-menu-item {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    width: 80px;
    text-align: center;
    font-size: 0.75rem;
    color: #333;
    white-space: normal;
    text-decoration: none;
    transition: color 0.2s ease;
    vertical-align: top;
    margin: 0 5px;
}
.nnp-icon-menu-item img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: 1px solid #ddd;
    object-fit: cover;
    margin-bottom: 6px;
    transition: transform 0.2s ease;
}
.nnp-icon-menu-item:hover { color: #238E46; }
.nnp-icon-menu-item:hover img { transform: scale(1.05); }

/* --- CSS for Product Grid --- */
.product-grid-container {
    display: grid;
    grid-template-columns: repeat(4, 1fr); /* 4 products per row on desktop */
    gap: 1.5rem;
}
.product-card {
    position: relative;
    overflow: hidden;
    text-decoration: none;
    color: #333;
    display: block;
}
.product-card:hover { color: #333; }
.product-image-container {
    position: relative;
    width: 100%;
    padding-bottom: 100%; /* Creates a square container (1:1 aspect ratio) */
    overflow: hidden;
    margin-bottom: 8px;
    background-color: #f8f9fa; /* Optional: background for smaller images */
    display: flex; /* For centering images */
    align-items: center; /* For centering images */
    justify-content: center; /* For centering images */
}
.product-main-img, .product-hover-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: opacity 0.3s ease;
    position: absolute;
    top: 0;
    left: 0;
}
.product-hover-img { opacity: 0; }
.product-card:hover .product-hover-img { opacity: 1; }
.product-card:hover .product-main-img { opacity: 0; }

@media (max-width: 991.98px) {
    .product-grid-container { grid-template-columns: repeat(2, 1fr); } /* 2 products on mobile */
}
</style>

<div class="container mt-4">
    <!-- Page Title -->
    <h2 class="fw-bold mb-3"><?php echo htmlspecialchars($page_title); ?></h2>

    <!-- Sub-categories Section -->
    <?php if ($sub_categories && $sub_categories->num_rows > 0): ?>
    <section id="sub-category-section" class="mb-4">
        <div class="nnp-icon-menu-wrapper">
            <div class="nnp-icon-menu-scroll">
                <?php while($sub_cat = $sub_categories->fetch_assoc()): ?>
                    <a href="<?php echo BASE_URL; ?>/danhmuc.php?id=<?php echo $category_id; ?>&sub_id=<?php echo $sub_cat['id']; ?>" class="nnp-icon-menu-item" title="<?php echo htmlspecialchars($sub_cat['name']); ?>">
                     <img 
  src="<?php echo BASE_URL . '/' . htmlspecialchars($sub_cat['image']); ?>"
  alt="<?php echo htmlspecialchars($sub_cat['name']); ?>"
>
            <span><?php echo htmlspecialchars($sub_cat['name']); ?></span>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>


    <!-- Products Section -->
    <section id="product-grid-section">
        <div id="add-to-cart-toast" class="toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 1100">
            <div class="d-flex">
                <div class="toast-body">
                    Sản phẩm đã được thêm vào giỏ hàng!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>

        <?php if ($all_products && $all_products->num_rows > 0): ?>
            <div class="product-grid-container">
                <?php while ($product = $all_products->fetch_assoc()): ?>
                    <div class="card h-100 product-card border-0 shadow-sm">
                        <div class="quick-view-badge" onclick="openQuickView(<?= htmlspecialchars(json_encode($product)); ?>)" 
                             style="position:absolute; top:10px; right:10px; z-index:10; cursor:pointer; background:rgba(255,255,255,0.8); border-radius:50%; width:35px; height:35px; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-eye text-success"></i>
                        </div>
                        <div class="card-body p-2 text-center">
                             <a href="<?php echo BASE_URL; ?>/app/Views/user/sanpham/chitietsanpham.php?id=<?php echo $product['id']; ?>" class="product-image-container">
                                <img src="<?php echo BASE_URL . '/' . htmlspecialchars($product['thumbnail'] ?? ''); ?>"
                                     class="img-fluid product-main-img"
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <img src="<?php echo BASE_URL . '/' . htmlspecialchars(!empty($product['image_hover']) ? $product['image_hover'] : ($product['thumbnail'] ?? '')); ?>"
                                     class="img-fluid product-hover-img"
                                     alt="<?php echo htmlspecialchars($product['name']); ?> - hover">
                            </a>
                            <p class="card-text mb-1 mt-2" style="font-size: 0.9rem;">
                                <a href="<?php echo BASE_URL; ?>/app/Views/user/sanpham/chitietsanpham.php?id=<?php echo $product['id']; ?>" class="text-decoration-none text-dark stretched-link">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                            </p>
                            <span class="text-danger fw-bold d-block"><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</span>
                        </div>
                         <div class="card-footer bg-white border-0 text-center p-2">
                            <button class="btn btn-success w-100 rounded-pill btn-sm fw-bold ajax-add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">
                                <i class="fas fa-cart-plus me-1"></i> MUA NGAY
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center p-5">
                <p>Không tìm thấy sản phẩm nào trong danh mục này.</p>
            </div>
        <?php endif; ?>
    </section>

</div>


<div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickViewModalLabel">Xem nhanh sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">
                        <img src="" id="quickViewImage" class="img-fluid rounded" alt="Product Image">
                    </div>
                    <div class="col-md-7">
                        <h3 id="quickViewName">Product Name</h3>
                        <p class="text-muted small">Mã sản phẩm: <span id="quickViewSku" class="fw-bold">N/A</span></p>
                        <p class="fs-4 fw-bold text-danger" id="quickViewPrice">0₫</p>
                        <form id="quickViewAddToCartForm">
                            <input type="hidden" name="product_id" id="quickViewProductId">
                            <input type="hidden" name="action" value="add">
                            <div class="mb-3">
                                <label for="quickViewQuantity" class="form-label">Số lượng:</label>
                                <div class="input-group" style="width: 150px;">
                                    <button class="btn btn-outline-secondary" type="button" onclick="this.nextElementSibling.stepDown()">-</button>
                                    <input type="number" id="quickViewQuantity" name="quantity" class="form-control text-center" value="1" min="1">
                                    <button class="btn btn-outline-secondary" type="button" onclick="this.previousElementSibling.stepUp()">+</button>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-lg w-100">
                                <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<?php
// --- FOOTER ---
include __DIR__ . '/app/Views/user/footer.php';
?>

