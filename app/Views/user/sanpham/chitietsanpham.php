<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/ProductModel.php';
include_once '../../../../app/models/CategoryModel.php';
include_once '../../../../app/models/CategoryLevel2Model.php';
include_once '../../../../app/models/CategoryLevel3Model.php';

$productModel = new ProductModel($conn);
$categoryModel = new CategoryModel($conn);
$categoryLevel2Model = new CategoryLevel2Model($conn);
$categoryLevel3Model = new CategoryLevel3Model($conn);

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id === 0) {
    header("Location: sanpham.php"); // Redirect if no ID or invalid ID
    exit();
}

$product = $productModel->getById($product_id);

if (!$product) {
    header("Location: sanpham.php"); // Redirect if product not found
    exit();
}

// Fetch categories for breadcrumb
$category1 = $categoryModel->getById($product['category_level1_id']);
$category2 = null;
if ($product['category_level2_id']) {
    $category2 = $categoryLevel2Model->getById($product['category_level2_id']);
}
$category3 = null;
if ($product['category_level3_id']) {
    $category3 = $categoryLevel3Model->getById($product['category_level3_id']);
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
        /* CSS Tùy Chỉnh Cho Trang Chi Tiết Sản Phẩm */
        :root {
            --primary-color: #238E46; /* Màu xanh lá cây nổi bật */
            --secondary-color: #4CAF50;
            --danger-color: #dc3545; 
            --info-color: #007bff; /* Màu xanh dương cho Zalo */
            --text-dark: #333;
            --text-muted: #6c757d;
            --border-light: #eee;
            --zalo-color: #007bff; /* Màu xanh dương Zalo */
        }

        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 1200px;
        }

        /* --- Header/Footer Giả Định --- */
        .fake-header, .fake-footer {
            width: 100%;
            background-color: #333;
            color: white;
            padding: 15px 0;
            text-align: center;
            font-size: 0.9rem;
        }
        .fake-header {
            background-color: var(--primary-color);
        }
        
        /* --- Breadcrumb --- */
        .breadcrumb-item a {
            color: var(--primary-color) !important;
        }

        /* --- Cột Hình ảnh & Icon --- */
        .product-gallery img {
            border: 1px solid var(--border-light);
            border-radius: 6px;
        }
        .main-image {
            width: 100%;
            height: auto;
        }
        .thumbnail-list img {
            width: 70px;
            height: 70px;
            object-fit: contain;
            cursor: pointer;
        }
        .feature-icons {
            list-style: none;
            padding-left: 0;
        }
        .feature-icons li {
            font-size: 0.9rem;
            margin-bottom: 10px;
            color: var(--text-dark);
        }
        .feature-icons i {
            color: var(--primary-color);
            margin-right: 8px;
            font-size: 1.1rem;
        }

        /* --- Cột Thông tin mua hàng --- */
        .product-info h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 10px;
        }
        .product-price {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        .btn-buy-now {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            font-weight: bold;
            padding: 12px 20px;
            font-size: 1.1rem;
            transition: background-color 0.2s;
        }
        .btn-add-to-cart {
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            font-weight: bold;
            padding: 12px 20px;
            font-size: 1.1rem;
            margin-top: 10px;
            transition: background-color 0.2s;
        }

        /* --- Nút Đặt mua/Zalo --- */
        .btn-hotline {
            background-color: var(--danger-color);
            color: white;
            font-weight: bold;
            padding: 10px 0;
            transition: background-color 0.2s;
        }
        .btn-zalo {
            background-color: var(--info-color);
            color: white;
            font-weight: bold;
            padding: 10px 0;
            transition: background-color 0.2s;
        }

        /* --- Banner báo giá sỉ --- */
        .wholesale-banner {
            background-color: var(--primary-color);
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 6px;
            margin-top: 20px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
        }

        /* --- Cột Chính sách (Sidebar phải) --- */
        .policy-box {
            background-color: #f7fff7;
            border: 1px solid #d9edd9;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .policy-box p {
            font-size: 0.85rem;
            margin-bottom: 5px;
            color: var(--text-dark);
        }
        .policy-box i {
            color: var(--primary-color);
            margin-right: 5px;
        }
        
        /* --- Phần Chi tiết sản phẩm --- */
        .product-tabs .nav-link.active {
            background-color: var(--primary-color) !important;
            color: white !important;
            border-color: var(--primary-color) !important;
        }
        .product-tabs .nav-link {
            color: var(--text-dark);
            font-weight: 600;
        }
        .product-detail-content h2 {
            font-size: 1.5rem;
            color: var(--primary-color);
            font-weight: 700;
            margin-top: 25px;
            margin-bottom: 15px;
        }

        /* --- Sản phẩm tương tự/đã xem --- */
        .related-products h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 40px;
            margin-bottom: 20px;
            color: var(--text-dark);
            border-bottom: 1px solid var(--border-light);
            padding-bottom: 5px;
        }
        .related-product-card {
            border: 1px solid var(--border-light);
            border-radius: 8px;
            background-color: white;
            overflow: hidden;
            height: 100%;
            text-align: center;
        }
        .related-product-card img {
            height: 150px;
            object-fit: contain;
            width: 100%;
            padding: 10px;
        }
        .related-product-card .price {
            font-weight: bold;
            color: var(--primary-color);
        }
        .related-product-card .name {
            font-size: 0.9rem;
            min-height: 35px;
            margin-bottom: 5px;
        }
        .related-product-card .btn-add-cart {
             background-color: var(--primary-color);
             color: white;
             border-radius: 50%;
             width: 35px;
             height: 35px;
             display: flex;
             align-items: center;
             justify-content: center;
             border: none;
        }

        /* --- Style cho Modal Zalo (Pop-up) --- */
        .zalo-modal .modal-content {
            border-radius: 10px;
        }
        .zalo-header {
            display: flex;
            align-items: center;
            padding: 15px;
            padding-bottom: 0;
        }
        .zalo-logo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: contain;
            border: 1px solid #eee;
            margin-right: 15px;
        }
        .btn-zalo-message {
            background-color: var(--zalo-color);
            color: white;
            font-weight: bold;
            padding: 10px 0;
            border-radius: 6px;
            text-decoration: none;
            display: block;
            text-align: center;
        }
        .detail-info i {
            color: var(--primary-color);
            width: 18px; 
            text-align: center;
            margin-right: 5px;
        }
        .zalo-qr-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-left: 1px solid #eee;
            padding: 15px;
            min-height: 350px; 
        }
        .zalo-qr-section img {
            width: 150px;
            height: 150px;
            object-fit: contain;
        }
        .zalo-qr-section .caption {
            font-size: 0.8rem;
            text-align: center;
            color: #6c757d;
            margin-top: 5px;
        }
        .footer-text {
            font-size: 0.85rem;
            color: #6c757d;
            padding: 15px;
            border-top: 1px solid #eee;
        }
    </style>

</head>
<body>

<?php include '../header.php'; ?>

<div class="container my-5">
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>" class="text-success">Trang chủ</a></li>
            <?php if ($category1): ?>
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/danhmuc.php?id=<?php echo $category1['id']; ?>" class="text-success"><?php echo htmlspecialchars($category1['name']); ?></a></li>
            <?php endif; ?>
            <?php if ($category2): ?>
                 <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($category2['name']); ?></li>
            <?php endif; ?>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars(substr($product['name'], 0, 30)) . '...'; ?></li>
        </ol>
    </nav>
    
    <div class="row">
        
        <div class="col-lg-5 mb-4">
            <div class="product-gallery">
                <img src="/<?php echo htmlspecialchars(isset($product['thumbnail']) ? $product['thumbnail'] : 'public/uploads/default.png'); ?>" class="main-image mb-3" alt="<?php echo htmlspecialchars($product['name']); ?>">
                
                <div class="d-flex thumbnail-list mb-4">
                    <img src="/<?php echo htmlspecialchars(isset($product['thumbnail']) ? $product['thumbnail'] : 'public/uploads/default.png'); ?>" class="me-2 active-thumb" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php if (!empty($product['image_hover'])): ?>
                    <img src="/<?php echo htmlspecialchars($product['image_hover']); ?>" class="me-2" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php endif; ?>
                </div>

                <ul class="feature-icons row">
                    <li class="col-md-6"><i class="fas fa-check-circle"></i> SẢN PHẨM CHÍNH HÃNG</li>
                    <li class="col-md-6"><i class="fas fa-users"></i> MIỄN PHÍ TƯ VẤN</li>
                    <li class="col-md-6"><i class="fas fa-award"></i> ĐẢM BẢO CHẤT LƯỢNG</li>
                    <li class="col-md-6"><i class="fas fa-box"></i> XUẤT VAT CÓ GIÁ SỈ</li>
                </ul>
            </div>
        </div>

        <div class="col-lg-4 mb-4 product-info">
            <form action="<?php echo BASE_URL; ?>/app/api/cart_actions.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="action" value="add">

                <h1 class="mb-2"><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="small text-muted mb-3">Mã sản phẩm: <span class="fw-bold text-dark"><?php echo htmlspecialchars($product['sku']); ?></span></p>
    
                <div class="product-price mb-3">
                    <?php if (isset($product['discount_price']) && $product['discount_price'] > 0 && $product['discount_price'] < $product['price']): ?>
                        <span class="text-muted text-decoration-line-through me-2"><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</span>
                        <span class="fs-3 fw-bold text-danger"><?php echo number_format($product['discount_price'], 0, ',', '.'); ?>₫</span>
                    <?php else: ?>
                        <span class="fs-3 fw-bold text-danger"><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</span>
                    <?php endif; ?>
                </div>
    
                <div class="d-flex align-items-center mb-4">
                    <span class="me-3">Số lượng:</span>
                    <div class="input-group" style="width: 150px;">
                        <button class="btn btn-outline-secondary" type="button" id="quantity-minus">-</button>
                        <input type="text" class="form-control text-center" value="1" id="quantity-input" name="quantity">
                        <button class="btn btn-outline-secondary" type="button" id="quantity-plus">+</button>
                    </div>
                </div>
    
                <div class="d-grid gap-2">
                    <button type="submit" name="buy_now" class="btn btn-buy-now"><i class="fas fa-shopping-cart"></i> MUA NGAY</button>
                    <button type="submit" name="add_to_cart" class="btn btn-add-to-cart btn-outline-success">THÊM VÀO GIỎ</button>
                </div>
            </form>

            <div class="row gx-2 mt-3">
                <div class="col-6 d-grid">
                    <button class="btn btn-hotline"><i class="fas fa-phone-alt"></i> Gọi Đặt Mua (8h - 17h)</button>
                </div>
                <div class="col-6 d-grid">
                    <button class="btn btn-zalo" data-bs-toggle="modal" data-bs-target="#zaloModal"><i class="fab fa-facebook-messenger"></i> Bấm Để Chat Zalo</button>
                </div>
            </div>

            <div class="wholesale-banner" data-bs-toggle="modal" data-bs-target="#zaloModal">
                <p class="mb-0">NHẬN NGAY BÁO GIÁ SỈ</p>
                <p class="mb-0 small fw-normal">Nhắn zalo hoặc Gọi hotline</p>
            </div>
        </div>
        
        <div class="col-lg-3">
             <div class="policy-box">
                <p><i class="fas fa-truck"></i> Miễn phí giao hàng tại Hà Nội và TPHCM cho đơn hàng trên 400.000₫, miễn phí giao hàng tại Đà Nẵng (chỉ áp dụng khu vực nội thành). <a href="#">Xem chính sách giao nhận tại đây</a></p>
            </div>
            <div class="policy-box">
                <p><i class="fas fa-sync-alt"></i> Đổi trả hàng trong 7 ngày sau khi nhận hàng nếu có lỗi kỹ thuật từ nhà sản xuất. <a href="#">Xem chính sách đổi trả hàng</a></p>
            </div>
        </div>
        
    </div>

    <div class="row mt-5">
        <div class="col-lg-9">
            
            <ul class="nav nav-tabs product-tabs" id="productTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail" type="button" role="tab">Mô tả chi tiết</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="review-tab" data-bs-toggle="tab" data-bs-target="#review" type="button" role="tab">Bình luận (1)</button>
                </li>
            </ul>

            <div class="tab-content border border-top-0 bg-white p-4 product-detail-content" id="productTabContent">
                
                <div class="tab-pane fade show active" id="detail" role="tabpanel">
                    <?php echo !empty($product['long_description']) ? $product['long_description'] : 'Chưa có mô tả chi tiết cho sản phẩm này.'; ?>
                </div>
                
                <div class="tab-pane fade" id="review" role="tabpanel">
                    <p>Tính năng bình luận đang được phát triển.</p>
                </div>
            </div>
        </div>
    </div>
     <div class="related-products">
        <h2 class="text-uppercase mt-5">Sản phẩm đã xem</h2>
    </div>
</div>

<div class="modal fade zalo-modal" id="zaloModal" tabindex="-1" aria-labelledby="zaloModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body pt-0">
                 </div>
            
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Quantity adjustment
        const quantityInput = document.getElementById('quantity-input');
        const quantityMinus = document.getElementById('quantity-minus');
        const quantityPlus = document.getElementById('quantity-plus');

        if (quantityInput && quantityMinus && quantityPlus) {
            quantityMinus.addEventListener('click', function() {
                let currentVal = parseInt(quantityInput.value);
                if (currentVal > 1) {
                    quantityInput.value = currentVal - 1;
                }
            });

            quantityPlus.addEventListener('click', function() {
                let currentVal = parseInt(quantityInput.value);
                quantityInput.value = currentVal + 1;
            });
        }
        
        // Image gallery functionality
        const mainImage = document.querySelector('.main-image');
        const thumbnailList = document.querySelector('.thumbnail-list');

        if (mainImage && thumbnailList) {
            thumbnailList.addEventListener('click', function(e) {
                if (e.target.tagName === 'IMG' && !e.target.classList.contains('active-thumb')) {
                    const currentActive = thumbnailList.querySelector('.active-thumb');
                    if (currentActive) {
                        currentActive.classList.remove('active-thumb');
                    }
                    e.target.classList.add('active-thumb');
                    mainImage.src = e.target.src;
                }
            });
        }
    });
</script>

<?php include '../footer.php'; ?>