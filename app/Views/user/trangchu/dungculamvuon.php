<?php 
// CSS cho component DỤNG CỤ LÀM VƯỜN
?>
<style>
    /* CSS CẦN THIẾT CHO PRODUCT CARD VÀ HOVER IMAGE (để đảm bảo hoạt động) */
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
    
    /* STYLE CHO TÙY CHỌN TRONG MODAL */
    .product-options-tools .btn {
        border-radius: 5px !important;
        padding: 8px 15px;
        font-weight: bold;
    }
    .product-options-tools .btn-check:checked + .btn {
        background-color: #238E46 !important;
        border-color: #238E46 !important;
        color: #fff;
    }

    /* CSS cho Tabs */
    #tools-tab-menu .nav-link {
        font-weight: bold;
        color: #333;
        border: none;
        border-bottom: 2px solid transparent;
        transition: all 0.3s ease;
    }
    #tools-tab-menu .nav-link.active {
        color: #238E46;
        border-bottom: 2px solid #238E46;
        background-color: transparent;
    }

    /* Điều chỉnh tiêu đề */
    .nnp-tools-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 15px;
    }
</style>

<section id="tools-section" class="mt-4">
    <div class="nnp-tools-header">
        <h3 class="fw-bold mb-0">DỤNG CỤ LÀM VƯỜN</h3>

        <?php if (isset($dungculamvuon_parent_category_id) && $dungculamvuon_parent_category_id): ?>
            <a href="<?php echo BASE_URL; ?>/danhmuc.php?id=<?php echo $dungculamvuon_parent_category_id; ?>" class="btn btn-outline-success btn-sm">
                Xem tất cả <i class="fas fa-arrow-right ms-1"></i>
            </a>
        <?php endif; ?>
        
        <ul class="nav nav-tabs border-0" id="tools-tab-menu" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pots-tab" data-bs-toggle="tab" data-bs-target="#pots-pane" type="button" role="tab" aria-controls="pots-pane" aria-selected="true">Chậu trồng rau</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="care-tab" data-bs-toggle="tab" data-bs-target="#care-pane" type="button" role="tab" aria-controls="care-pane" aria-selected="false">Dụng cụ chăm sóc cây</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="general-tab" data-bs-toggle="tab" data-bs-target="#general-pane" type="button" role="tab" aria-controls="general-pane" aria-selected="false">Dụng cụ làm vườn</button>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="tools-tab-content">
            
            <div class="tab-pane fade show active" id="pots-pane" role="tabpanel" aria-labelledby="pots-tab">
                <div class="nnp-product-slider-wrapper">
                    <button class="nnp-slider-nav-btn nnp-prev-btn d-none d-lg-flex" onclick="scrollSlider('pots-list-slider', 'prev')"><i class="fas fa-chevron-left"></i></button>
                    <div class="nnp-product-slider" id="pots-list-slider">
                        <?php if ($chautrongrau_products && $chautrongrau_products->num_rows > 0): ?>
                            <?php mysqli_data_seek($chautrongrau_products, 0); ?>
                            <?php while ($product = $chautrongrau_products->fetch_assoc()): ?>
                                <div class="col">
                                    <div class="card h-100 product-card border-0 shadow-sm">
                                        <div class="quick-view-badge" onclick="openQuickView(<?= htmlspecialchars(json_encode($product)); ?>)" style="position:absolute; top:10px; right:10px; z-index:10; cursor:pointer; background:rgba(255,255,255,0.8); border-radius:50%; width:35px; height:35px; display:flex; align-items:center; justify-content:center;"><i class="fas fa-eye text-success"></i></div>
                                        <div class="card-body p-2 text-center">
                                            <small class="text-muted d-block" style="font-size: 0.7rem;"><?= htmlspecialchars($product['supplier'] ?? 'N/A'); ?></small>
                                            <small class="text-success fw-bold d-block mb-2" style="font-size: 0.7rem;">SẢN PHẨM CHÍNH HÃNG</small>
                                            <a href="<?= BASE_URL; ?>/app/Views/user/sanpham/chitietsanpham.php?id=<?= $product['id']; ?>" class="product-image-container quick-view-trigger" style="cursor: pointer;">
                                                <img src="<?= BASE_URL . '/' . htmlspecialchars($product['thumbnail'] ?? 'https://via.placeholder.com/150?text=No+Image'); ?>" class="img-fluid product-main-img" alt="<?= htmlspecialchars($product['name']); ?>">
                                                <img src="<?= BASE_URL . '/' . htmlspecialchars($product['image_hover'] ?? $product['thumbnail'] ?? 'https://via.placeholder.com/150?text=No+Image'); ?>" class="img-fluid product-hover-img" alt="<?= htmlspecialchars($product['name']); ?> - Ảnh 2">
                                            </a>
                                            <div class="d-flex justify-content-around mb-2"><i class="fas fa-microchip text-success" title="Miễn phí tư vấn"></i><i class="fas fa-certificate text-success" title="Đảm bảo chất lượng"></i><i class="fas fa-seedling text-success" title="Dễ sử dụng"></i></div>
                                            <p class="card-text mb-1" style="font-size: 0.85rem;"><a href="<?= BASE_URL; ?>/app/Views/user/sanpham/chitietsanpham.php?id=<?= $product['id']; ?>" class="text-decoration-none text-dark"><?= htmlspecialchars($product['name']); ?></a></p>
                                            <span class="text-danger fw-bold d-block"><?= number_format($product['price'], 0, ',', '.'); ?>₫</span>
                                        </div>
                                        <div class="card-footer bg-white border-0 text-center p-2">
                                            <button class="btn btn-success w-100 rounded-pill btn-sm fw-bold ajax-add-to-cart-btn" data-product-id="<?= $product['id']; ?>">
                                                <i class="fas fa-cart-plus me-1"></i> MUA NGAY
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12 text-center"><p>Chưa có sản phẩm nào trong danh mục này.</p></div>
                        <?php endif; ?>
                    </div>
                    <button class="nnp-slider-nav-btn nnp-next-btn d-none d-lg-flex" onclick="scrollSlider('pots-list-slider', 'next')"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
    
            <div class="tab-pane fade" id="care-pane" role="tabpanel" aria-labelledby="care-tab">
                <div class="nnp-product-slider-wrapper">
                    <button class="nnp-slider-nav-btn nnp-prev-btn d-none d-lg-flex" onclick="scrollSlider('care-list-slider', 'prev')"><i class="fas fa-chevron-left"></i></button>
                    <div class="nnp-product-slider" id="care-list-slider">
                        <?php if ($dungcuchamsoc_products && $dungcuchamsoc_products->num_rows > 0): ?>
                             <?php mysqli_data_seek($dungcuchamsoc_products, 0); ?>
                            <?php while ($product = $dungcuchamsoc_products->fetch_assoc()): ?>
                                <div class="col">
                                    <div class="card h-100 product-card border-0 shadow-sm">
                                        <div class="quick-view-badge" onclick="openQuickView(<?= htmlspecialchars(json_encode($product)); ?>)" style="position:absolute; top:10px; right:10px; z-index:10; cursor:pointer; background:rgba(255,255,255,0.8); border-radius:50%; width:35px; height:35px; display:flex; align-items:center; justify-content:center;"><i class="fas fa-eye text-success"></i></div>
                                        <div class="card-body p-2 text-center">
                                            <small class="text-muted d-block" style="font-size: 0.7rem;"><?= htmlspecialchars($product['supplier'] ?? 'N/A'); ?></small>
                                            <small class="text-success fw-bold d-block mb-2" style="font-size: 0.7rem;">SẢN PHẨM CHÍNH HÃNG</small>
                                            <a href="<?= BASE_URL; ?>/app/Views/user/sanpham/chitietsanpham.php?id=<?= $product['id']; ?>" class="product-image-container quick-view-trigger" style="cursor: pointer;">
                                                <img src="<?= BASE_URL . '/' . htmlspecialchars($product['thumbnail'] ?? 'https://via.placeholder.com/150?text=No+Image'); ?>" class="img-fluid product-main-img" alt="<?= htmlspecialchars($product['name']); ?>">
                                                <img src="<?= BASE_URL . '/' . htmlspecialchars($product['image_hover'] ?? $product['thumbnail'] ?? 'https://via.placeholder.com/150?text=No+Image'); ?>" class="img-fluid product-hover-img" alt="<?= htmlspecialchars($product['name']); ?> - Ảnh 2">
                                            </a>
                                            <div class="d-flex justify-content-around mb-2"><i class="fas fa-microchip text-success" title="Miễn phí tư vấn"></i><i class="fas fa-certificate text-success" title="Đảm bảo chất lượng"></i><i class="fas fa-seedling text-success" title="Dễ sử dụng"></i></div>
                                            <p class="card-text mb-1" style="font-size: 0.85rem;"><a href="<?= BASE_URL; ?>/app/Views/user/sanpham/chitietsanpham.php?id=<?= $product['id']; ?>" class="text-decoration-none text-dark"><?= htmlspecialchars($product['name']); ?></a></p>
                                            <span class="text-danger fw-bold d-block"><?= number_format($product['price'], 0, ',', '.'); ?>₫</span>
                                        </div>
                                        <div class="card-footer bg-white border-0 text-center p-2">
                                            <button class="btn btn-success w-100 rounded-pill btn-sm fw-bold ajax-add-to-cart-btn" data-product-id="<?= $product['id']; ?>">
                                                <i class="fas fa-cart-plus me-1"></i> MUA NGAY
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12 text-center"><p>Chưa có sản phẩm nào trong danh mục này.</p></div>
                        <?php endif; ?>
                    </div>
                    <button class="nnp-slider-nav-btn nnp-next-btn d-none d-lg-flex" onclick="scrollSlider('care-list-slider', 'next')"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
    
            <div class="tab-pane fade" id="general-pane" role="tabpanel" aria-labelledby="general-tab">
                <div class="nnp-product-slider-wrapper">
                    <button class="nnp-slider-nav-btn nnp-prev-btn d-none d-lg-flex" onclick="scrollSlider('general-list-slider', 'prev')"><i class="fas fa-chevron-left"></i></button>
                    <div class="nnp-product-slider" id="general-list-slider">
                         <?php if ($dungculamvuon_products && $dungculamvuon_products->num_rows > 0): ?>
                            <?php mysqli_data_seek($dungculamvuon_products, 0); ?>
                            <?php while ($product = $dungculamvuon_products->fetch_assoc()): ?>
                                <div class="col">
                                    <div class="card h-100 product-card border-0 shadow-sm">
                                        <div class="quick-view-badge" onclick="openQuickView(<?= htmlspecialchars(json_encode($product)); ?>)" style="position:absolute; top:10px; right:10px; z-index:10; cursor:pointer; background:rgba(255,255,255,0.8); border-radius:50%; width:35px; height:35px; display:flex; align-items:center; justify-content:center;"><i class="fas fa-eye text-success"></i></div>
                                        <div class="card-body p-2 text-center">
                                            <small class="text-muted d-block" style="font-size: 0.7rem;"><?= htmlspecialchars($product['supplier'] ?? 'N/A'); ?></small>
                                            <small class="text-success fw-bold d-block mb-2" style="font-size: 0.7rem;">SẢN PHẨM CHÍNH HÃNG</small>
                                            <a href="<?= BASE_URL; ?>/app/Views/user/sanpham/chitietsanpham.php?id=<?= $product['id']; ?>" class="product-image-container quick-view-trigger" style="cursor: pointer;">
                                                <img src="<?= BASE_URL . '/' . htmlspecialchars($product['thumbnail'] ?? 'https://via.placeholder.com/150?text=No+Image'); ?>" class="img-fluid product-main-img" alt="<?= htmlspecialchars($product['name']); ?>">
                                                <img src="<?= BASE_URL . '/' . htmlspecialchars($product['image_hover'] ?? $product['thumbnail'] ?? 'https://via.placeholder.com/150?text=No+Image'); ?>" class="img-fluid product-hover-img" alt="<?= htmlspecialchars($product['name']); ?> - Ảnh 2">
                                            </a>
                                            <div class="d-flex justify-content-around mb-2"><i class="fas fa-microchip text-success" title="Miễn phí tư vấn"></i><i class="fas fa-certificate text-success" title="Đảm bảo chất lượng"></i><i class="fas fa-seedling text-success" title="Dễ sử dụng"></i></div>
                                            <p class="card-text mb-1" style="font-size: 0.85rem;"><a href="<?= BASE_URL; ?>/app/Views/user/sanpham/chitietsanpham.php?id=<?= $product['id']; ?>" class="text-decoration-none text-dark"><?= htmlspecialchars($product['name']); ?></a></p>
                                            <span class="text-danger fw-bold d-block"><?= number_format($product['price'], 0, ',', '.'); ?>₫</span>
                                        </div>
                                        <div class="card-footer bg-white border-0 text-center p-2">
                                            <button class="btn btn-success w-100 rounded-pill btn-sm fw-bold ajax-add-to-cart-btn" data-product-id="<?= $product['id']; ?>">
                                                <i class="fas fa-cart-plus me-1"></i> MUA NGAY
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12 text-center"><p>Chưa có sản phẩm nào trong danh mục này.</p></div>
                        <?php endif; ?>
                    </div>
                    <button class="nnp-slider-nav-btn nnp-next-btn d-none d-lg-flex" onclick="scrollSlider('general-list-slider', 'next')"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
    
        </div></section>

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
