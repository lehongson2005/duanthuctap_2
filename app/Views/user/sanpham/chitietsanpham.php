<?php
include_once '../header.php'; 
include_once __DIR__ . '/../../../config/db.php';
include_once __DIR__ . '/../../../models/ProductModel.php';
include_once __DIR__ . '/../../../models/CategoryModel.php';
include_once __DIR__ . '/../../../models/CategoryLevel2Model.php';
include_once __DIR__ . '/../../../models/CategoryLevel3Model.php';

$productModel = new ProductModel($conn);
$categoryModel = new CategoryModel($conn);
$categoryLevel2Model = new CategoryLevel2Model($conn);
$categoryLevel3Model = new CategoryLevel3Model($conn);

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id === 0) {
    header("Location: sanpham.php");
    exit();
}

$product = $productModel->getById($product_id);

if (!$product) {
    header("Location: sanpham.php");
    exit();
}

// Fetch categories for breadcrumb
$category1 = $categoryModel->getById($product['category_level1_id']);
$category2 = null;
if ($product['category_level2_id']) {
    $category2 = $categoryLevel2Model->getById($product['category_level2_id']);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Chi tiết sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #238E46;
            --secondary-color: #4CAF50;
            --danger-color: #dc3545; 
            --info-color: #007bff;
            --text-dark: #333;
            --border-light: #eee;
        }

        body { background-color: #f8f9fa; }
        .container { max-width: 1200px; }
        
        /* --- Gallery Styles --- */
        .product-gallery .main-image-container {
            background: #fff;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 15px;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        #main-product-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            transition: opacity 0.3s ease-in-out;
        }

        .thumbnail-list .thumb-item {
            width: 70px;
            height: 70px;
            border: 2px solid transparent;
            border-radius: 4px;
            cursor: pointer;
            object-fit: cover;
            transition: all 0.2s;
            margin-right: 10px;
            padding: 2px;
            background: #fff;
        }

        .thumbnail-list .thumb-item:hover { border-color: var(--secondary-color); }
        .thumbnail-list .thumb-item.active { border-color: var(--primary-color); }

        /* --- UI Components --- */
        .feature-icons { list-style: none; padding-left: 0; font-size: 0.85rem; }
        .feature-icons i { color: var(--primary-color); margin-right: 5px; }
        .product-price { font-size: 2rem; color: var(--danger-color); font-weight: 700; }
        .btn-buy-now { background: var(--primary-color); color: #fff; font-weight: 700; padding: 12px; }
        .btn-add-to-cart { border: 2px solid var(--primary-color); color: var(--primary-color); font-weight: 700; padding: 12px; }
        .policy-box { background: #f7fff7; border: 1px solid #d9edd9; padding: 15px; border-radius: 6px; margin-bottom: 15px; font-size: 0.9rem; }
        .wholesale-banner { background: var(--primary-color); color: white; padding: 15px; text-align: center; border-radius: 6px; cursor: pointer; }
    </style>
</head>
<body>

<div class="container my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>" class="text-success">Trang chủ</a></li>
            <?php if ($category1): ?>
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/danhmuc.php?id=<?php echo $category1['id']; ?>" class="text-success"><?php echo htmlspecialchars($category1['name']); ?></a></li>
            <?php endif; ?>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars(mb_substr($product['name'], 0, 30)) . '...'; ?></li>
        </ol>
    </nav>
    
    <div class="row">
        <div class="col-lg-5 mb-4">
            <div class="product-gallery">
                <div class="main-image-container">
                    <img id="main-product-image" 
                         src="<?= BASE_URL . '/' . htmlspecialchars($product['thumbnail'] ?? 'public/uploads/default.png'); ?>" 
                         alt="<?= htmlspecialchars($product['name']); ?>">
                </div>
                
                <div class="d-flex thumbnail-list mb-4">
                    <img src="<?= BASE_URL . '/' . ($product['thumbnail'] ?? 'public/uploads/default.png'); ?>" 
                         class="thumb-item active" 
                         onclick="changeImage(this)">
                    
                    <?php if (!empty($product['image_hover'])): ?>
                    <img src="<?= BASE_URL . '/' . $product['image_hover']; ?>" 
                         class="thumb-item" 
                         onclick="changeImage(this)">
                    <?php endif; ?>
                </div>

                <ul class="feature-icons row mt-3 text-uppercase fw-bold">
                    <li class="col-6 mb-2"><i class="fas fa-check-circle"></i> Chính hãng</li>
                    <li class="col-6 mb-2"><i class="fas fa-users"></i> Tư vấn miễn phí</li>
                    <li class="col-6 mb-2"><i class="fas fa-award"></i> Chất lượng</li>
                    <li class="col-6 mb-2"><i class="fas fa-box"></i> Giá sỉ tốt</li>
                </ul>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <h1 class="h3 fw-bold"><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="text-muted small">Mã: <span class="text-dark fw-bold"><?php echo htmlspecialchars($product['sku']); ?></span></p>

            <div class="product-price mb-3">
                <?php if (isset($product['discount_price']) && $product['discount_price'] > 0): ?>
                    <small class="text-muted text-decoration-line-through fs-6 me-2"><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</small>
                    <span><?php echo number_format($product['discount_price'], 0, ',', '.'); ?>₫</span>
                <?php else: ?>
                    <span><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</span>
                <?php endif; ?>
            </div>

            <div class="d-flex align-items-center mb-4">
                <span class="me-3">Số lượng:</span>
                <div class="input-group" style="width: 130px;">
                    <button class="btn btn-outline-secondary" type="button" onclick="updateQty(-1)">-</button>
                    <input type="text" class="form-control text-center" value="1" id="quantity-input">
                    <button class="btn btn-outline-secondary" type="button" onclick="updateQty(1)">+</button>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="button" id="buy-now-btn" data-product-id="<?= $product['id']; ?>" class="btn btn-buy-now shadow-sm">MUA NGAY</button>
                <button type="button" id="add-to-cart-btn" data-product-id="<?= $product['id']; ?>" class="btn btn-add-to-cart">THÊM VÀO GIỎ</button>
            </div>

            <div class="row gx-2 mt-3">
                <div class="col-6 d-grid"><button class="btn btn-danger btn-sm fw-bold"><i class="fas fa-phone-alt"></i> GỌI ĐẶT HÀNG</button></div>
                <div class="col-6 d-grid"><button class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#zaloModal"><i class="fab fa-facebook-messenger"></i> CHAT ZALO</button></div>
            </div>

            <div class="wholesale-banner mt-3" data-bs-toggle="modal" data-bs-target="#zaloModal">
                <p class="mb-0 fw-bold">NHẬN BÁO GIÁ SỈ</p>
                <small>Nhắn Zalo hoặc Gọi hotline ngay</small>
            </div>
        </div>
        
        <div class="col-lg-3">
             <div class="policy-box">
                <p><i class="fas fa-truck"></i> Miễn phí giao hàng nội thành Hà Nội & TPHCM cho đơn từ 400k.</p>
            </div>
            <div class="policy-box">
                <p><i class="fas fa-sync-alt"></i> Đổi trả hàng trong 7 ngày nếu có lỗi kỹ thuật.</p>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-lg-9">
            <ul class="nav nav-tabs fw-bold" id="productTab">
                <li class="nav-item">
                    <button class="nav-link active text-success" data-bs-toggle="tab" data-bs-target="#detail">Mô tả sản phẩm</button>
                </li>
            </ul>
            <div class="tab-content border border-top-0 bg-white p-4">
                <div class="tab-pane fade show active" id="detail">
                    <?php echo !empty($product['long_description']) ? $product['long_description'] : 'Nội dung đang cập nhật...'; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="zaloModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0"><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body text-center pb-5">
                <i class="fab fa-facebook-messenger fa-3x text-primary mb-3"></i>
                <h5>Liên hệ tư vấn Zalo</h5>
                <p>Quý khách vui lòng quét mã QR hoặc nhấn vào nút bên dưới.</p>
                <a href="#" class="btn btn-primary px-4">Mở Zalo Ngay</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Hàm đổi ảnh chính khi click ảnh nhỏ
    function changeImage(element) {
        const mainImg = document.getElementById('main-product-image');
        
        // Hiệu ứng mờ dần
        mainImg.style.opacity = '0';
        
        setTimeout(() => {
            mainImg.src = element.src;
            mainImg.style.opacity = '1';
            
            // Đổi class active
            document.querySelectorAll('.thumb-item').forEach(img => img.classList.remove('active'));
            element.classList.add('active');
        }, 200);
    }

    // Hàm tăng giảm số lượng
    function updateQty(val) {
        const input = document.getElementById('quantity-input');
        let current = parseInt(input.value);
        if (isNaN(current)) current = 1;
        if (current + val >= 1) input.value = current + val;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const addToCartBtn = document.getElementById('add-to-cart-btn');
        const buyNowBtn = document.getElementById('buy-now-btn');

        // Sự kiện thêm vào giỏ hàng
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const qty = document.getElementById('quantity-input').value;
                if (window.handleAddToCart) {
                    window.handleAddToCart(productId, qty);
                } else {
                    alert('Đã thêm sản phẩm vào giỏ hàng!');
                }
            });
        }

        // Sự kiện mua ngay
        if (buyNowBtn) {
            buyNowBtn.addEventListener('click', async function() {
                const productId = this.dataset.productId;
                const qty = document.getElementById('quantity-input').value;
                if (window.handleAddToCart) {
                    await window.handleAddToCart(productId, qty);
                    window.location.href = '<?php echo rtrim(BASE_URL, "/"); ?>/giohang.php';
                }
            });
        }
    });
</script>

<?php include '../footer.php'; ?>
</body>
</html>