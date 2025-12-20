<?php
// FILE: /timkiem.php

// This file needs the header to define BASE_URL and start session
include_once __DIR__ . '/app/Views/user/header.php';
include_once __DIR__ . '/app/config/db.php';
include_once __DIR__ . '/app/models/ProductModel.php';

// Instantiate models
$productModel = new ProductModel($conn);

// Get keyword from URL
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

if (empty($keyword)) {
    header("Location: " . BASE_URL . "/index.php");
    exit();
}

$page_title = 'Kết quả tìm kiếm cho "' . htmlspecialchars($keyword) . '"';

// Fetch all active products matching the keyword
$all_products = $productModel->searchAndFilter($keyword, '', '', '', '1', '', null, null);

// --- HEADER ---
// Already included above

?>

<style>
/* --- CSS for Product Grid (copied from danhmuc.php) --- */
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
    height: 200px;
    margin-bottom: 8px;
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
    <h2 class="fw-bold mb-4"><?php echo $page_title; ?></h2>

    <!-- Products Section -->
    <section id="product-grid-section">
        <?php if ($all_products && $all_products->num_rows > 0): ?>
            <div class="product-grid-container">
                <?php while ($product = $all_products->fetch_assoc()): ?>
                    <a href="<?php echo BASE_URL; ?>/app/Views/user/sanpham/chitietsanpham.php?id=<?php echo $product['id']; ?>" class="text-decoration-none">
                    <div class="card h-100 product-card border-0 shadow-sm">
                        <div class="card-body p-2 text-center">
                             <div class="product-image-container">
                                <img src="<?php echo BASE_URL . '/' . htmlspecialchars($product['thumbnail'] ?? ''); ?>"
                                     class="img-fluid product-main-img"
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <img src="<?php echo BASE_URL . '/' . htmlspecialchars(!empty($product['image_hover']) ? $product['image_hover'] : ($product['thumbnail'] ?? '')); ?>"
                                     class="img-fluid product-hover-img"
                                     alt="<?php echo htmlspecialchars($product['name']); ?> - hover">
                            </div>
                            <p class="card-text mb-1" style="font-size: 0.9rem;"><?php echo htmlspecialchars($product['name']); ?></p>
                            <span class="text-danger fw-bold d-block"><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</span>
                        </div>
                         <div class="card-footer bg-white border-0 text-center p-2">
                            <button class="btn btn-sm btn-success" onclick="event.preventDefault(); event.stopPropagation();">Thêm vào giỏ</button>
                        </div>
                    </div>
                </a>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center p-5">
                <p>Không tìm thấy sản phẩm nào phù hợp với từ khóa của bạn.</p>
            </div>
        <?php endif; ?>
    </section>

</div>

<?php
// --- FOOTER ---
include __DIR__ . '/app/Views/user/footer.php';
?>
