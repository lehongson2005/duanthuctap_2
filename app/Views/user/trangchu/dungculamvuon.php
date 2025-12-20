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
                            <?php while ($product = $chautrongrau_products->fetch_assoc()): ?>
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

                                        <a href="<?php echo BASE_URL; ?>/app/Views/user/sanpham/chitietsanpham.php?id=<?php echo $product['id']; ?>" class="text-decoration-none">
                                            <div class="card-body p-2 text-center">
                                                <small class="text-muted d-block" style="font-size: 0.7rem;"><?php echo htmlspecialchars(isset($product['supplier']) ? $product['supplier'] : 'N/A'); ?></small>
                                                <small class="text-success fw-bold d-block mb-2" style="font-size: 0.7rem;">SẢN PHẨM CHÍNH HÃNG</small>
                                                
                                                <div class="product-image-container quick-view-trigger" style="cursor: pointer;">
                                                    <img src="<?php echo BASE_URL . '/' . htmlspecialchars(isset($product['thumbnail']) ? $product['thumbnail'] : 'https://via.placeholder.com/150?text=No+Image'); ?>" class="img-fluid product-main-img" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                                    <img src="<?php echo BASE_URL . '/' . htmlspecialchars(!empty($product['image_hover']) ? $product['image_hover'] : (isset($product['thumbnail']) ? $product['thumbnail'] : 'https://via.placeholder.com/150?text=No+Image')); ?>" class="img-fluid product-hover-img" alt="<?php echo htmlspecialchars($product['name']); ?> - Ảnh 2">
                                                </div>
                                                
                                                <div class="d-flex justify-content-around mb-2">
                                                    <i class="fas fa-microchip text-success" title="Miễn phí tư vấn"></i>
                                                    <i class="fas fa-certificate text-success" title="Đảm bảo chất lượng"></i>
                                                    <i class="fas fa-seedling text-success" title="Dễ sử dụng"></i>
                                                </div>
                                                <p class="card-text mb-1" style="font-size: 0.85rem;"><?php echo htmlspecialchars($product['name']); ?></p>
                                                <span class="text-danger fw-bold d-block"><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</span>
                                            </div>
                                        </a>
                                        <div class="card-footer bg-white border-0 text-center p-2">
                                            <button class="btn btn-success w-100 rounded-pill btn-sm fw-bold" onclick="addToCart(<?php echo $product['id']; ?>)">
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
                            <?php while ($product = $dungcuchamsoc_products->fetch_assoc()): ?>
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

                                        <a href="<?php echo BASE_URL; ?>/app/Views/user/sanpham/chitietsanpham.php?id=<?php echo $product['id']; ?>" class="text-decoration-none">
                                            <div class="card-body p-2 text-center">
                                                <small class="text-muted d-block" style="font-size: 0.7rem;"><?php echo htmlspecialchars(isset($product['supplier']) ? $product['supplier'] : 'N/A'); ?></small>
                                                <small class="text-success fw-bold d-block mb-2" style="font-size: 0.7rem;">SẢN PHẨM CHÍNH HÃNG</small>
                                                
                                                <div class="product-image-container quick-view-trigger" style="cursor: pointer;">
                                                    <img src="<?php echo BASE_URL . '/' . htmlspecialchars(isset($product['thumbnail']) ? $product['thumbnail'] : 'https://via.placeholder.com/150?text=No+Image'); ?>" class="img-fluid product-main-img" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                                    <img src="<?php echo BASE_URL . '/' . htmlspecialchars(!empty($product['image_hover']) ? $product['image_hover'] : (isset($product['thumbnail']) ? $product['thumbnail'] : 'https://via.placeholder.com/150?text=No+Image')); ?>" class="img-fluid product-hover-img" alt="<?php echo htmlspecialchars($product['name']); ?> - Ảnh 2">
                                                </div>
                                                
                                                <div class="d-flex justify-content-around mb-2">
                                                    <i class="fas fa-microchip text-success" title="Miễn phí tư vấn"></i>
                                                    <i class="fas fa-certificate text-success" title="Đảm bảo chất lượng"></i>
                                                    <i class="fas fa-seedling text-success" title="Dễ sử dụng"></i>
                                                </div>
                                                <p class="card-text mb-1" style="font-size: 0.85rem;"><?php echo htmlspecialchars($product['name']); ?></p>
                                                <span class="text-danger fw-bold d-block"><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</span>
                                            </div>
                                        </a>
                                        <div class="card-footer bg-white border-0 text-center p-2">
                                            <button class="btn btn-success w-100 rounded-pill btn-sm fw-bold" onclick="addToCart(<?php echo $product['id']; ?>)">
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
                            <?php while ($product = $dungculamvuon_products->fetch_assoc()): ?>
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

                                        <a href="<?php echo BASE_URL; ?>/app/Views/user/sanpham/chitietsanpham.php?id=<?php echo $product['id']; ?>" class="text-decoration-none">
                                            <div class="card-body p-2 text-center">
                                                <small class="text-muted d-block" style="font-size: 0.7rem;"><?php echo htmlspecialchars(isset($product['supplier']) ? $product['supplier'] : 'N/A'); ?></small>
                                                <small class="text-success fw-bold d-block mb-2" style="font-size: 0.7rem;">SẢN PHẨM CHÍNH HÃNG</small>
                                                
                                                <div class="product-image-container quick-view-trigger" style="cursor: pointer;">
                                                    <img src="<?php echo BASE_URL . '/' . htmlspecialchars(isset($product['thumbnail']) ? $product['thumbnail'] : 'https://via.placeholder.com/150?text=No+Image'); ?>" class="img-fluid product-main-img" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                                    <img src="<?php echo BASE_URL . '/' . htmlspecialchars(!empty($product['image_hover']) ? $product['image_hover'] : (isset($product['thumbnail']) ? $product['thumbnail'] : 'https://via.placeholder.com/150?text=No+Image')); ?>" class="img-fluid product-hover-img" alt="<?php echo htmlspecialchars($product['name']); ?> - Ảnh 2">
                                                </div>
                                                
                                                <div class="d-flex justify-content-around mb-2">
                                                    <i class="fas fa-microchip text-success" title="Miễn phí tư vấn"></i>
                                                    <i class="fas fa-certificate text-success" title="Đảm bảo chất lượng"></i>
                                                    <i class="fas fa-seedling text-success" title="Dễ sử dụng"></i>
                                                </div>
                                                <p class="card-text mb-1" style="font-size: 0.85rem;"><?php echo htmlspecialchars($product['name']); ?></p>
                                                <span class="text-danger fw-bold d-block"><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</span>
                                            </div>
                                        </a>
                                        <div class="card-footer bg-white border-0 text-center p-2">
                                            <button class="btn btn-success w-100 rounded-pill btn-sm fw-bold" onclick="addToCart(<?php echo $product['id']; ?>)">
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

<script>
// 1. Hàm thêm vào giỏ hàng ngay lập tức
function addToCart(productId) {
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('id', productId);
    formData.append('quantity', 1);
    formData.append('is_ajax', '1'); // ADD THIS LINE

    fetch('<?= BASE_URL; ?>/app/api/cart_actions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
         
            if (typeof refreshCartDisplay === 'function') {
                refreshCartDisplay();
            } else {
                console.warn('refreshCartDisplay function not found. Please ensure header.php is loaded correctly.');
                window.location.reload(); // Fallback in case function is not defined
            }
        } else {
            alert('Có lỗi xảy ra, vui lòng thử lại.');
        }
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
        alert('Có lỗi xảy ra trong quá trình thêm sản phẩm vào giỏ hàng.');
    });
}

// 2. Hàm mở Modal và đổ dữ liệu thật (Sửa lỗi dữ liệu giả)
function openQuickView(product) {
    const modal = document.getElementById('quickViewModal');
    
    // Đổ tên, giá, mã SP
    modal.querySelector('h2').innerText = product.name;
    modal.querySelector('.fs-3').innerText = new Intl.NumberFormat('vi-VN').format(product.price) + '₫';
    modal.querySelector('.product-meta small').innerText = 'Mã sản phẩm: ' + (product.sku || 'Đang cập nhật');
    
    // Đổ ảnh chính
    const mainImg = modal.querySelector('.product-image img');
    mainImg.src = '<?= BASE_URL; ?>/' + product.thumbnail;
    
    // Gán ID vào nút "Thêm vào giỏ" trong Modal
    const modalAddBtn = modal.querySelector('#quickViewAddToCartForm button[type="submit"]'); // Target the submit button in the form
    if (modalAddBtn) {
        modalAddBtn.onclick = function() { // Attach onclick to the button
            const quantity = modal.querySelector('#quickViewQuantity').value;
            addToCart(product.id, quantity);
        };
    }
    
    // Set product_id for the hidden input in the form
    modal.querySelector('#quickViewProductId').value = product.id;

    // Show modal
    var myModal = new bootstrap.Modal(modal);
    myModal.show();
}

// Ensure the quickViewModal in footer.php is also aware of the local addToCart
document.addEventListener('DOMContentLoaded', function() {
    const quickViewFormInFooter = document.getElementById('quickViewAddToCartForm');
    if (quickViewFormInFooter) {
        quickViewFormInFooter.addEventListener('submit', function(e) {
            e.preventDefault();
            const productId = document.getElementById('quickViewProductId').value;
            const quantity = document.getElementById('quickViewQuantity').value;
            // Use the local addToCart function
            addToCart(productId, quantity);
            // Hide the modal
            const quickViewModal = bootstrap.Modal.getInstance(document.getElementById('quickViewModal'));
            if (quickViewModal) {
                quickViewModal.hide();
            }
        });
    }
});
</script>