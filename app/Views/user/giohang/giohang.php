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

// Kiểm tra nếu user đã đăng nhập - đọc từ database và đồng bộ vào session
if (isset($_SESSION['user_id'])) {
    include_once __DIR__ . '/../../../models/CartModel.php';
    include_once __DIR__ . '/../../../models/CartItemModel.php';
    
    $userId = $_SESSION['user_id'];
    $cartModel = new CartModel($conn);
    $cartItemModel = new CartItemModel($conn);
    
    // Lấy giỏ hàng của user từ database
    $cart = $cartModel->getOrCreateActiveCartByUserId($userId);
    if ($cart) {
        $cartId = $cart['id'];
        $items_result = $cartItemModel->getItemsByCartId($cartId);
        
        // Đồng bộ dữ liệu từ database vào session
        $_SESSION['cart'] = [];
        if ($items_result) {
            while ($item = $items_result->fetch_assoc()) {
                // Lưu vào session
                $_SESSION['cart'][$item['product_id']] = $item['quantity'];
                
                // Lấy thông tin sản phẩm đầy đủ
                $product = $productModel->getById($item['product_id']);
                if ($product) {
                    $price = $item['price']; // Dùng price từ cart_items (đã lưu khi thêm vào giỏ)
                    $sub_total = $price * $item['quantity'];
                    $grand_total += $sub_total;
                    
                    $product['quantity'] = $item['quantity'];
                    $product['sub_total'] = $sub_total;
                    $product['cart_item_id'] = $item['id']; // Lưu cart_item_id để dùng khi update
                    $cart_items[] = $product;
                }
            }
        }
    }
} else {
    // User chưa đăng nhập - đọc từ session
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
                    <?php 
                    $item_price = (isset($item['discount_price']) && $item['discount_price'] > 0) ? $item['discount_price'] : $item['price'];
                    ?>
                    <div class="cart-item-card" data-product-id="<?php echo $item['id']; ?>" data-price="<?php echo $item_price; ?>" <?php if (isset($item['cart_item_id'])): ?>data-cart-item-id="<?php echo $item['cart_item_id']; ?>"<?php endif; ?>>
                        <img src="<?php echo BASE_URL . '/' . htmlspecialchars($item['thumbnail']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-item-image">
                        <div class="cart-item-details">
                            <p class="cart-item-name mb-1">
                                <a href="<?php echo BASE_URL; ?>/app/Views/user/sanpham/chitietsanpham.php?id=<?php echo $item['id']; ?>">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </a>
                            </p>
                            <p class="cart-item-price mb-0"><?php echo number_format($item_price); ?>₫</p>
                        </div>
                        <div class="cart-item-actions">
                            <div class="input-group qty-input-group">
                                <input type="number" 
                                       class="form-control form-control-sm text-center cart-quantity-input" 
                                       value="<?php echo $item['quantity']; ?>" 
                                       min="1"
                                       data-product-id="<?php echo $item['id']; ?>"
                                       <?php if (isset($item['cart_item_id'])): ?>data-cart-item-id="<?php echo $item['cart_item_id']; ?>"<?php endif; ?>>
                            </div>
                            <div class="cart-item-subtotal" data-subtotal="<?php echo $item['sub_total']; ?>">
                                <?php echo number_format($item['sub_total']); ?>₫
                            </div>
                          <a href="#" 
                             class="btn-remove text-decoration-none" 
                             onclick="removeCartItem(event, <?php echo $item['id']; ?>, <?php echo isset($item['cart_item_id']) ? $item['cart_item_id'] : 0; ?>); return false;"
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
                        <span class="total-value" id="cart-subtotal"><?php echo number_format($grand_total); ?>₫</span>
                    </div>
                     <div class="total-row">
                        <span>Phí vận chuyển</span>
                        <span class="total-value">0₫</span>
                    </div>
                    <hr>
                    <div class="total-row grand-total">
                        <span>Tổng cộng</span>
                        <span class="total-value" id="cart-grand-total"><?php echo number_format($grand_total); ?>₫</span>
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
    const BASE_URL_CART = '<?php echo BASE_URL; ?>';
    let updateTimeout; // Biến dùng để quản lý thời gian chờ (Debounce)

    // 1. HÀM THÔNG BÁO THÔNG MINH: Xóa sạch cái cũ rồi mới hiện cái mới
    function showCartToast(message, isSuccess = true) {
        // Tìm và xóa tất cả thông báo đang có trên màn hình ngay lập tức
        const oldAlerts = document.querySelectorAll('.cart-temp-alert');
        oldAlerts.forEach(alert => alert.remove());

        // Tạo phần tử thông báo mới
        const alertDiv = document.createElement('div');
        alertDiv.className = `cart-temp-alert alert alert-${isSuccess ? 'success' : 'danger'} fade show`;
        
        // Dùng CSS Fixed để thông báo "bay" trên màn hình, không làm nhảy giao diện
        Object.assign(alertDiv.style, {
            position: 'fixed',
            top: '20px',
            right: '20px',
            zIndex: '9999',
            minWidth: '280px',
            boxShadow: '0 4px 15px rgba(0,0,0,0.2)',
            transition: 'all 0.4s ease'
        });

        alertDiv.innerHTML = `
            <div class="d-flex align-items-center justify-content-between">
                <span>
                    <i class="fas ${isSuccess ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                    ${message}
                </span>
                <button type="button" class="btn-close" style="font-size: 0.8rem" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;

        document.body.appendChild(alertDiv);
        
        // Tự động biến mất sau 2.5 giây
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.style.opacity = '0';
                alertDiv.style.transform = 'translateY(-10px)';
                setTimeout(() => alertDiv.remove(), 400);
            }
        }, 2500);
    }

    // 2. HÀM GỬI CẬP NHẬT LÊN SERVER (API)
    async function performUpdateCart(inputElement) {
        const productId = inputElement.dataset.productId;
        const cartItemId = inputElement.dataset.cartItemId || 0;
        const quantity = parseInt(inputElement.value) || 1;
        const oldValue = parseInt(inputElement.dataset.oldValue) || 1;

        const formData = new FormData();
        formData.append('action', 'update');
        formData.append('quantities[' + productId + ']', quantity);
        if (cartItemId > 0) {
            formData.append('cart_item_ids[' + productId + ']', cartItemId);
        }

        try {
            const response = await fetch(BASE_URL_CART + '/app/api/cart_actions.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result && result.success) {
                inputElement.dataset.oldValue = quantity;
                // Chỉ hiện thông báo KHI VÀ CHỈ KHI người dùng đã dừng thao tác và cập nhật thành công
                showCartToast(result.message || 'Đã cập nhật số lượng thành công!');
                if (typeof window.updateHeaderCart === 'function') window.updateHeaderCart();
            } else {
                inputElement.value = oldValue;
                updateCartTotals();
                showCartToast(result.message || 'Cập nhật thất bại', false);
            }
        } catch (error) {
            console.error('Lỗi:', error);
            showCartToast('Lỗi máy chủ, vui lòng thử lại sau', false);
        }
    }

    // 3. XỬ LÝ SỰ KIỆN TĂNG GIẢM SỐ LƯỢNG
    document.querySelectorAll('.cart-quantity-input').forEach(input => {
        // Sử dụng sự kiện 'input' thay vì 'change' để bắt thao tác ngay lập tức
        input.addEventListener('input', function() {
            // Bước A: Cập nhật tổng tiền tạm thời trên UI NGAY LẬP TỨC (cho mượt)
            updateCartTotals();

            // Bước B: Xóa thời gian chờ của lần click trước đó
            clearTimeout(updateTimeout);

            // Bước C: Thiết lập thời gian chờ 600ms. 
            // Nếu trong 0.6 giây người dùng không ấn nữa thì mới gửi API và hiện thông báo.
            updateTimeout = setTimeout(() => {
                performUpdateCart(this);
            }, 600); 
        });

        input.dataset.oldValue = input.value;
    });

    // 4. HÀM XÓA SẢN PHẨM (GIỮ NGUYÊN)
    async function removeCartItem(event, productId, cartItemId = 0) {
        if (!confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) return;
        const cartItemCard = document.querySelector(`.cart-item-card[data-product-id="${productId}"]`);
        try {
            const response = await fetch(`${BASE_URL_CART}/app/api/cart_actions.php?action=remove&id=${productId}`);
            const result = await response.json();
            if (result && result.success) {
                cartItemCard.style.opacity = '0';
                cartItemCard.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    cartItemCard.remove();
                    updateCartTotals();
                    if (document.querySelectorAll('.cart-item-card').length === 0) window.location.reload();
                    if (typeof window.updateHeaderCart === 'function') window.updateHeaderCart();
                }, 300);
                showCartToast('Đã xóa sản phẩm khỏi giỏ hàng');
            }
        } catch (error) {
            showCartToast('Không thể xóa sản phẩm', false);
        }
    }

    // 5. CÁC HÀM TIỆN ÍCH UI
    function formatMoney(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + '₫';
    }

    function updateCartTotals() {
        let grandTotal = 0;
        document.querySelectorAll('.cart-item-card').forEach(card => {
            const quantity = parseInt(card.querySelector('.cart-quantity-input').value) || 0;
            const price = parseFloat(card.dataset.price) || 0;
            const subtotal = quantity * price;
            const subtotalElement = card.querySelector('.cart-item-subtotal');
            if (subtotalElement) {
                subtotalElement.textContent = formatMoney(subtotal);
                subtotalElement.dataset.subtotal = subtotal;
            }
            grandTotal += subtotal;
        });
        const subtotalEl = document.getElementById('cart-subtotal');
        const grandTotalEl = document.getElementById('cart-grand-total');
        if (subtotalEl) subtotalEl.textContent = formatMoney(grandTotal);
        if (grandTotalEl) grandTotalEl.textContent = formatMoney(grandTotal);
    }

    document.addEventListener('DOMContentLoaded', updateCartTotals);
</script>
<?php include_once __DIR__ . '/../footer.php'; ?>