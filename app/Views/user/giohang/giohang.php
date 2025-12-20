<?php
// FILE: /app/Views/user/giohang/giohang.php

// This file needs the header to define BASE_URL and start session
include_once __DIR__ . '/../header.php';
include_once __DIR__ . '/../../../../app/config/db.php';
include_once __DIR__ . '/../../../../app/models/ProductModel.php';

$productModel = new ProductModel($conn);

// Fetch cart items from session and database
$cart_items = [];
$grand_total = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $product = $productModel->getById($product_id);
        if ($product) {
            $price = (isset($product['discount_price']) && $product['discount_price'] > 0) ? $product['discount_price'] : $product['price'];
            $sub_total = $price * $quantity;
            $grand_total += $sub_total;
            
            $product['quantity'] = $quantity;
            $product['sub_total'] = $sub_total;
            $cart_items[] = $product;
        }
    }
}
?>
<style>
    .cart-item-card {
        display: flex;
        align-items: center;
        background-color: #fff;
        padding: 1.5rem;
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        transition: box-shadow 0.2s ease-in-out;
    }
    .cart-item-card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.075);
    }
    .cart-item-image {
        width: 100px;
        height: 100px;
        object-fit: contain;
        margin-right: 1.5rem;
    }
    .cart-item-details {
        flex-grow: 1;
    }
    .cart-item-name {
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 0.25rem;
    }
    .cart-item-name a {
        color: #212529;
        text-decoration: none;
    }
    .cart-item-name a:hover {
        color: var(--nnp-green);
    }
    .cart-item-price {
        font-weight: 500;
        color: #6c757d;
        font-size: 0.9rem;
    }
    .cart-item-actions {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-left: 1.5rem;
    }
    .qty-input-group {
        width: 120px;
    }
    .cart-item-subtotal {
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--nnp-green);
        min-width: 120px;
        text-align: right;
    }
    .btn-remove {
        color: #6c757d;
        font-size: 1.2rem;
    }
    .btn-remove:hover {
        color: #dc3545;
    }
    .checkout-sidebar {
        background-color: #fff;
        padding: 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.05);
        position: sticky;
        top: 20px;
    }
    .checkout-sidebar h4 {
        font-weight: 600;
        border-bottom: 1px solid #eee;
        padding-bottom: 0.75rem;
        margin-bottom: 1rem;
    }
    .total-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        font-size: 1rem;
    }
    .grand-total .total-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--nnp-green);
    }
    .btn-checkout {
        background-color: var(--nnp-green);
        border-color: var(--nnp-green);
        font-size: 1.1rem;
        font-weight: 600;
    }
    .btn-checkout:hover {
        background-color: #1a6834;
        border-color: #1a6834;
    }
</style>

<div class="container my-5">
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/" class="text-success">Trang chủ</a></li>
            <li class="breadcrumb-item active">Giỏ hàng</li>
        </ol>
    </nav>
    
    <h1 class="mb-4">Giỏ hàng của bạn</h1>

    <?php if (!empty($cart_items)): ?>
    <form action="<?php echo BASE_URL; ?>/app/api/cart_actions.php" method="POST">
        <input type="hidden" name="action" value="update">
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="cart-item-list">
                    <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item-card">
                        <img src="<?php echo BASE_URL . '/' . htmlspecialchars($item['thumbnail']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-item-image">
                        <div class="cart-item-details">
                            <p class="cart-item-name mb-1">
                                <a href="<?php echo BASE_URL; ?>/app/Views/user/sanpham/chitietsanpham.php?id=<?php echo $item['id']; ?>">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </a>
                            </p>
                            <p class="cart-item-price mb-0"><?php echo number_format((isset($item['discount_price']) && $item['discount_price'] > 0) ? $item['discount_price'] : $item['price']); ?>₫</p>
                        </div>
                        <div class="cart-item-actions">
                            <div class="input-group qty-input-group">
                                <input type="number" class="form-control form-control-sm text-center" name="quantities[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" min="1">
                            </div>
                            <div class="cart-item-subtotal">
                                <?php echo number_format($item['sub_total']); ?>₫
                            </div>
                          <a href="<?php echo BASE_URL; ?>/app/api/cart_actions.php?action=remove&id=<?php echo $item['id']; ?>" 
   class="btn-remove text-decoration-none" 
   onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')"
   title="Xóa sản phẩm">
    <i class="fas fa-trash"></i>
</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                 <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="<?php echo BASE_URL; ?>/" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i> Tiếp tục mua sắm</a>

                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="checkout-sidebar">
                    <h4>Tóm tắt đơn hàng</h4>
                    <div class="total-row">
                        <span>Tạm tính</span>
                        <span class="total-value"><?php echo number_format($grand_total); ?>₫</span>
                    </div>
                     <div class="total-row">
                        <span>Phí vận chuyển</span>
                        <span class="total-value">0₫</span>
                    </div>
                    <hr>
                    <div class="total-row grand-total">
                        <span>Tổng cộng</span>
                        <span class="total-value"><?php echo number_format($grand_total); ?>₫</span>
                    </div>
                    <div class="d-grid mb-2 mt-4">
                        <a href="#" class="btn btn-lg btn-success btn-checkout">Tiến hành Thanh Toán</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php else: ?>
        <div class="text-center p-5 bg-white rounded shadow-sm">
            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
            <h3 class="mb-3">Giỏ hàng của bạn đang trống</h3>
            <p class="text-muted">Hãy quay lại và chọn cho mình những sản phẩm ưng ý nhé!</p>
            <a href="<?php echo BASE_URL; ?>/" class="btn btn-success mt-3">Tiếp tục mua sắm</a>
        </div>
    <?php endif; ?>
</div>

<script>
    // Tự động submit form khi người dùng thay đổi số lượng trong ô input
    document.querySelectorAll('.qty-input-group input').forEach(input => {
        input.addEventListener('change', function() {
            // Tìm thẻ form gần nhất và submit
            this.closest('form').submit();
        });
    });
</script>
<?php include_once __DIR__ . '/../footer.php'; ?>