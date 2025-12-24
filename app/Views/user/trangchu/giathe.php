<?php 
// CSS cho component GIÁ THỂ
// CSS này đảm bảo hiển thị đúng cho card sản phẩm và hiệu ứng hover.
?>
<style>
    /* CSS CẦN THIẾT CHO PRODUCT CARD VÀ HOVER IMAGE (để đảm bảo hoạt động) */
    /* Lưu ý: Các style chung (.nnp-slider-nav-btn, .nnp-product-slider .col) đã được giữ trong index.php */
    .product-card {
        position: relative; 
        overflow: hidden;
        border: 1px solid #eee;
    }
    .product-image-container {
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 100%; /* This creates the square aspect ratio */
        margin-bottom: 8px;
        background-color: #f8f9fa;
        border: 1px solid #eee;
    }
    
    .product-main-img,
    .product-hover-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 5px; /* Add some internal padding */
        transition: opacity 0.3s ease;
    }

    .product-hover-img {
        opacity: 0; 
        z-index: 10;
    }

    .product-card:hover .product-main-img {
        opacity: 0; 
    }
    .product-card:hover .product-hover-img {
        opacity: 1; 
    }
    
    /* STYLE CHO TÙY CHỌN CHI NHÁNH/KHỐI LƯỢNG TRONG MODAL */
    .product-options-media .btn {
        border-radius: 5px !important;
        padding: 8px 15px;
        font-weight: bold;
    }
    .product-options-media .btn-check:checked + .btn {
        background-color: #238E46 !important;
        border-color: #238E46 !important;
        color: #fff;
    }
</style>

<section id="growing-media-list-section" class="mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold mb-0">GIÁ THỂ</h3>
        <?php if (isset($giathe_view_all_id)): ?>
        <a href="<?php echo BASE_URL; ?>/danhmuc.php?id=<?php echo $giathe_view_all_id; ?>" class="btn btn-outline-success btn-sm">
            Xem tất cả <i class="fas fa-arrow-right ms-1"></i>
        </a>
        <?php endif; ?>
    </div>
    
    <div class="nnp-product-slider-wrapper">
        
        <button class="nnp-slider-nav-btn nnp-prev-btn d-none d-lg-flex" onclick="scrollSlider('media-list-slider', 'prev')">
            <i class="fas fa-chevron-left"></i>
        </button>

        <div class="nnp-product-slider" id="media-list-slider">
            <?php if ($giathe_products && $giathe_products->num_rows > 0): ?>
                <?php while ($product = $giathe_products->fetch_assoc()): ?>
                    <div class="col">
                        <div class="card h-100 product-card border-0 shadow-sm product-card-data" 
                             data-id="<?php echo $product['id']; ?>"
                             data-name="<?php echo htmlspecialchars($product['name']); ?>"
                             data-sku="<?php echo htmlspecialchars(isset($product['sku']) ? $product['sku'] : 'N/A'); ?>"
                             data-price-formatted="<?php echo number_format($product['price'], 0, ',', '.'); ?>₫"
                             data-image="<?php echo htmlspecialchars(isset($product['thumbnail']) ? $product['thumbnail'] : 'public/uploads/default.png'); ?>">
                            
                            <div class="quick-view-badge" onclick="openQuickView(<?= htmlspecialchars(json_encode($product)); ?>)" 
                                 style="position:absolute; top:10px; right:10px; z-index:10; cursor:pointer; background:rgba(255,255,255,0.8); border-radius:50%; width:35px; height:35px; display:flex; align-items:center; justify-content:center;">
                                <i class="fas fa-eye text-success"></i>
                            </div>

                            <?php if (!empty($product['is_new'])): ?>
                                <span class="nnp-new-badge">Hàng mới về</span>
                            <?php endif; ?>

                            <div class="card-body p-2 text-center">
     
                                <a href="<?php echo BASE_URL; ?>/app/Views/user/sanpham/chitietsanpham.php?id=<?php echo $product['id']; ?>" class="product-image-container quick-view-trigger" style="cursor: pointer;">
                                    <img src="<?php echo BASE_URL . '/' . htmlspecialchars(isset($product['thumbnail']) ? $product['thumbnail'] : 'https://via.placeholder.com/150?text=No+Image'); ?>"
                                         class="img-fluid product-main-img"
                                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    <img src="<?php echo BASE_URL . '/' . htmlspecialchars(!empty($product['image_hover']) ? $product['image_hover'] : (isset($product['thumbnail']) ? $product['thumbnail'] : 'https://via.placeholder.com/150?text=No+Image')); ?>"
                                         class="img-fluid product-hover-img"
                                         alt="<?php echo htmlspecialchars($product['name']); ?> - Ảnh 2">
                                </a>
                                
   

                                <p class="card-text mb-1" style="font-size: 0.85rem;">
                                     <a href="<?php echo BASE_URL; ?>/app/Views/user/sanpham/chitietsanpham.php?id=<?php echo $product['id']; ?>" class="text-decoration-none text-dark">
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
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p>Chưa có sản phẩm nào trong danh mục này.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <button class="nnp-slider-nav-btn nnp-next-btn d-none d-lg-flex" onclick="scrollSlider('media-list-slider', 'next')">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
    
    <div class="text-center mt-4 d-lg-none"> 
        <button class="btn btn-outline-success">Xem tất cả <i class="fas fa-chevron-right"></i></button>
    </div>
</section>

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




