<?php
// FILE: /timkiem.php

include_once __DIR__ . '/app/Views/user/header.php';
include_once __DIR__ . '/app/config/db.php';
include_once __DIR__ . '/app/models/ProductModel.php';

$productModel = new ProductModel($conn);

// Lấy từ khóa
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
if ($keyword === '') {
    header("Location: " . BASE_URL . "/index.php");
    exit();
}

$page_title = 'Kết quả tìm kiếm cho "' . htmlspecialchars($keyword) . '"';

// Lấy sản phẩm
$all_products = $productModel->searchAndFilter($keyword, '', '', '', '1', '', null, null);
?>

<style>
/* ===== SEARCH PAGE ===== */
.search-title {
    font-size: 1.6rem;
    font-weight: 700;
}

/* ===== PRODUCT GRID ===== */
.product-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
}

/* ===== PRODUCT CARD ===== */
.product-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    transition: all .3s ease;
    box-shadow: 0 8px 24px rgba(0,0,0,.08);
}
.product-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 36px rgba(0,0,0,.14);
}

/* ===== IMAGE ===== */
.product-image {
    position: relative;
    height: 220px;
    background: #f6f6f6;
}
.product-image img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: opacity .35s ease;
}
.product-image img.hover { opacity: 0; }
.product-card:hover img.hover { opacity: 1; }
.product-card:hover img.main { opacity: 0; }

/* ===== CONTENT ===== */
.product-name {
    font-size: .95rem;
    font-weight: 600;
    min-height: 44px;
    line-height: 1.4;
    color: #333;
}
.product-price {
    font-size: 1.1rem;
    font-weight: 700;
    color: #e53935;
}

/* ===== BUTTON ===== */
.btn-cart {
    border-radius: 999px;
    padding: 6px 20px;
    font-size: .8rem;
    transition: all .25s ease;
}
.btn-cart:hover {
    transform: scale(1.05);
}

/* ===== EMPTY ===== */
.empty-result {
    padding: 80px 0;
    color: #777;
    font-size: 1.05rem;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 992px) {
    .product-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 576px) {
    .product-grid { grid-template-columns: 1fr; }
}
</style>

<div class="container mt-4 mb-5">
    <h2 class="search-title mb-4"><?= $page_title ?></h2>

    <?php if ($all_products && $all_products->num_rows > 0): ?>
        <div class="product-grid">
            <?php while ($product = $all_products->fetch_assoc()): ?>
                <a href="<?= BASE_URL ?>/app/Views/user/sanpham/chitietsanpham.php?id=<?= $product['id'] ?>"
                   class="text-decoration-none">

                    <div class="product-card h-100">
                        <div class="product-image">
                            <img src="<?= BASE_URL . '/' . htmlspecialchars($product['thumbnail']) ?>"
                                 class="main"
                                 alt="<?= htmlspecialchars($product['name']) ?>">
                            <img src="<?= BASE_URL . '/' . htmlspecialchars($product['image_hover'] ?? $product['thumbnail']) ?>"
                                 class="hover"
                                 alt="<?= htmlspecialchars($product['name']) ?>">
                        </div>

                        <div class="p-3 text-center">
                            <div class="product-name mb-2">
                                <?= htmlspecialchars($product['name']) ?>
                            </div>
                            <div class="product-price mb-3">
                                <?= number_format($product['price'], 0, ',', '.') ?>₫
                            </div>
                            <button class="btn btn-success btn-sm btn-cart"
                                    onclick="event.preventDefault();event.stopPropagation();">
                                Thêm vào giỏ
                            </button>
                        </div>
                    </div>

                </a>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center empty-result">
            Không tìm thấy sản phẩm phù hợp với từ khóa.
        </div>
    <?php endif; ?>
</div>

<?php
include __DIR__ . '/app/Views/user/footer.php';
?>
