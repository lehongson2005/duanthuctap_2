<?php 
// Component: Kích Rễ - Tối ưu Grid 5 cột và Quick View Modal
?>
<style>
    /* CSS Card Sản phẩm */
    .product-card {
        position: relative; 
        overflow: hidden;
        border: 1px solid #eee;
        transition: all 0.3s ease;
        border-radius: 8px;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
    .product-image-container {
        position: relative;
        width: 100%;
        padding-bottom: 100%;
        margin-bottom: 10px;
        background-color: #f8f9fa;
        cursor: pointer;
    }
    .product-main-img, .product-hover-img {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        object-fit: contain;
        padding: 10px;
        transition: opacity 0.3s ease;
    }
    .product-hover-img { opacity: 0; z-index: 10; }
    .product-card:hover .product-main-img { opacity: 0; }
    .product-card:hover .product-hover-img { opacity: 1; }

    .product-name-limit {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 2.5rem;
        font-size: 0.85rem;
        line-height: 1.25rem;
    }
    
    .nnp-new-badge {
        position: absolute;
        top: 5px; left: 5px;
        background-color: #ffc107;
        font-size: 0.65rem;
        font-weight: bold;
        padding: 3px 7px;
        border-radius: 3px;
        z-index: 15;
    }
</style>

<section id="kichre-section" class="mt-4 container">
    <div id="add-to-cart-toast" class="toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 1100">
        <div class="d-flex">
            <div class="toast-body">
                Sản phẩm đã được thêm vào giỏ hàng!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold mb-0" style="color: #238E46;">KÍCH RỄ</h3>
        <a href="<?= BASE_URL; ?>/danhmuc.php?id=1" class="btn btn-outline-success btn-sm rounded-pill px-3">
            Xem tất cả <i class="fas fa-arrow-right ms-1"></i>
        </a>
    </div>
    
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
        <?php if ($kichre_products && $kichre_products->num_rows > 0): ?>
            <?php while ($product = $kichre_products->fetch_assoc()): ?>
                <div class="col">
                    <div class="card h-100 product-card border-0 shadow-sm">
                        <?php if (!empty($product['is_new'])): ?>
                            <span class="nnp-new-badge">HÀNG MỚI</span>
                        <?php endif; ?>



                        <div class="card-body p-2 text-center quick-view-trigger" 
                             style="cursor: pointer;"
                             onclick="window.location.href='<?= BASE_URL; ?>/app/Views/user/sanpham/chitietsanpham.php?id=<?= $product['id']; ?>'">
    
                
                            
                            <div class="product-image-container">
                                <img src="<?= BASE_URL . '/' . ($product['thumbnail'] ?? 'public/uploads/default.png'); ?>" class="product-main-img">
                                <img src="<?= BASE_URL . '/' . ($product['image_hover'] ?? $product['thumbnail']); ?>" class="product-hover-img">
                            </div>

                            <p class="product-name-limit fw-bold mb-1"><?= htmlspecialchars($product['name']); ?></p>
                            <span class="text-danger fw-bold d-block"><?= number_format($product['price'], 0, ',', '.'); ?>₫</span>
                        </div>

                        <div class="card-footer bg-white border-0 pb-3">
                            <button class="btn btn-success w-100 rounded-pill btn-sm fw-bold ajax-add-to-cart-btn" data-product-id="<?= $product['id']; ?>">
                                <i class="fas fa-cart-plus me-1"></i> MUA NGAY
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</section>

<div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold text-success" id="quickViewModalLabel">Thông tin sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5 mb-3 mb-md-0">
                        <img src="" id="quickViewImage" class="img-fluid rounded border" alt="Product Image">
                    </div>
                    <div class="col-md-7">
                        <h3 id="quickViewName" class="fw-bold mb-2">Tên sản phẩm</h3>
                        <p class="text-muted small mb-2">Mã sản phẩm: <span id="quickViewSku" class="fw-bold text-dark">N/A</span></p>
                        <p class="fs-3 fw-bold text-danger mb-3" id="quickViewPrice">0₫</p>
                        <hr>
                        <form id="quickViewAddToCartForm">
                            <input type="hidden" name="product_id" id="quickViewProductId">
                            <div class="mb-4">
                                <label for="quickViewQuantity" class="form-label fw-bold">Số lượng:</label>
                                <div class="input-group" style="width: 140px;">
                                    <button class="btn btn-outline-secondary" type="button" onclick="changeQty(-1)">-</button>
                                    <input type="number" id="quickViewQuantity" name="quantity" class="form-control text-center" value="1" min="1">
                                    <button class="btn btn-outline-secondary" type="button" onclick="changeQty(1)">+</button>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill fw-bold">
                                <i class="fas fa-cart-plus me-2"></i> THÊM VÀO GIỎ HÀNG
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>