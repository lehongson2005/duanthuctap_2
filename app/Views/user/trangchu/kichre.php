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

                        <div class="quick-view-badge" onclick='openQuickView(<?= json_encode($product); ?>)' 
                             style="position:absolute; top:10px; right:10px; z-index:20; cursor:pointer; background:rgba(255,255,255,0.9); border-radius:50%; width:32px; height:32px; display:flex; align-items:center; justify-content:center; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                            <i class="fas fa-eye text-success" style="font-size: 0.8rem;"></i>
                        </div>

                        <div class="card-body p-2 text-center quick-view-trigger" 
     style="cursor: pointer;"
     data-id="<?= $product['id']; ?>"
     data-name="<?= htmlspecialchars($product['name']); ?>"
     data-price="<?= $product['price']; ?>"
     data-sku="<?= htmlspecialchars($product['sku'] ?? 'N/A'); ?>"
     data-img="<?= BASE_URL . '/' . ($product['thumbnail'] ?? 'public/uploads/default.png'); ?>">
    
    <small class="text-muted d-block" style="font-size: 0.7rem;">
        <?= htmlspecialchars($product['supplier'] ?? 'Nông Nghiệp Phố'); ?>
    </small>
    
                                <div class="product-image-container" onclick="openQuickView(<?= htmlspecialchars(json_encode($product)); ?>); event.preventDefault(); event.stopPropagation();">
                                    <img src="<?= BASE_URL . '/' . ($product['thumbnail'] ?? 'public/uploads/default.png'); ?>" class="product-main-img">
                                    <img src="<?= BASE_URL . '/' . ($product['image_hover'] ?? $product['thumbnail']); ?>" class="product-hover-img">
                                </div>

    <p class="product-name-limit fw-bold mb-1"><?= htmlspecialchars($product['name']); ?></p>
    <span class="text-danger fw-bold d-block"><?= number_format($product['price'], 0, ',', '.'); ?>₫</span>
</div>

                        <div class="card-footer bg-white border-0 pb-3">
                            <button class="btn btn-success w-100 rounded-pill btn-sm fw-bold" onclick="addToCart(<?= $product['id']; ?>, 1)">
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Hàm mở Modal xem nhanh
function openQuickView(product) {
    // 1. Tìm element Modal
    const modalElement = document.getElementById('quickViewModal');
    
    if (!modalElement) {
        console.error("Lỗi: Không tìm thấy HTML của Modal 'quickViewModal'. Hãy đảm bảo bạn đã copy đoạn <div class='modal'> vào file.");
        return;
    }

    // 2. Đổ dữ liệu vào Modal (Dùng đúng ID trong HTML bạn đã gửi)
    document.getElementById('quickViewName').innerText = product.name;
    document.getElementById('quickViewPrice').innerText = new Intl.NumberFormat('vi-VN').format(product.price) + '₫';
    document.getElementById('quickViewSku').innerText = product.sku || 'N/A';
    document.getElementById('quickViewImage').src = '<?= BASE_URL; ?>/' + (product.thumbnail || 'public/uploads/default.png');
    document.getElementById('quickViewProductId').value = product.id;
    document.getElementById('quickViewQuantity').value = 1;

    // 3. Kích hoạt Modal bằng Bootstrap
    let myModal = bootstrap.Modal.getInstance(modalElement); // Kiểm tra nếu đã khởi tạo
    if (!myModal) {
        myModal = new bootstrap.Modal(modalElement); // Nếu chưa thì tạo mới
    }
    myModal.show();
}

// Hàm thay đổi số lượng (+/-)
function changeQty(val) {
    const input = document.getElementById('quickViewQuantity');
    let current = parseInt(input.value) || 1;
    if (current + val >= 1) input.value = current + val;
}

// Đảm bảo Form trong Modal hoạt động
document.addEventListener('DOMContentLoaded', function() {
    const qvForm = document.getElementById('quickViewAddToCartForm');
    if (qvForm) {
        qvForm.onsubmit = function(e) {
            e.preventDefault();
            const pId = document.getElementById('quickViewProductId').value;
            const pQty = document.getElementById('quickViewQuantity').value;
            
            // Gọi hàm addToCart (Phải đảm bảo hàm này đã được định nghĩa)
            if (typeof addToCart === 'function') {
                addToCart(pId, pQty);
                const inst = bootstrap.Modal.getInstance(document.getElementById('quickViewModal'));
                if (inst) inst.hide();
            }
        };
    }
});
</script>