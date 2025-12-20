<?php
// view_order.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: ../../auth/login.php");
    exit;
}

include_once '../../../../app/config/db.php';
require_once '../../../models/OrderModel.php';
require_once '../../../models/OrderItemModel.php';

$page_title = "Chi tiết Đơn hàng";

// Check for ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "ID đơn hàng không hợp lệ.";
    header("Location: list_orders.php");
    exit;
}
$id = (int)$_GET['id'];

// Fetch order details
$orderModel = new OrderModel($conn);
$order = $orderModel->getById($id);

if (!$order) {
    $_SESSION['error_message'] = "Đơn hàng không tồn tại.";
    header("Location: list_orders.php");
    exit;
}

// Fetch order items
$orderItemModel = new OrderItemModel($conn);
$items = $orderItemModel->getByOrderId($id);

?>

<?php include '../templates/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Chi tiết Đơn hàng #<?= htmlspecialchars($order['id']) ?></h1>
    
    <div class="row">
        <!-- Customer and Order Info -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin Khách hàng & Vận chuyển</h6>
                </div>
                <div class="card-body">
                    <p><strong>Tên khách hàng:</strong> <?= htmlspecialchars($order['full_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                    <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['phone_number']) ?></p>
                    <p><strong>Địa chỉ giao hàng:</strong><br><?= nl2br(htmlspecialchars($order['address'])) ?></p>
                    <?php if(!empty($order['note'])): ?>
                        <p><strong>Ghi chú của khách:</strong><br><?= nl2br(htmlspecialchars($order['note'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin Đơn hàng</h6>
                </div>
                <div class="card-body">
                    <p><strong>Ngày đặt hàng:</strong> <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></p>
                    <p><strong>Phương thức vận chuyển:</strong> <?= htmlspecialchars($order['shipping_method']) ?></p>
                    <p><strong>Phương thức thanh toán:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
                    <p><strong>Trạng thái:</strong> 
                        <span class="badge badge-lg
                            <?php
                            switch ($order['status']) {
                                case 'pending': echo 'badge-warning'; break;
                                case 'processing': echo 'badge-info'; break;
                                case 'shipped': echo 'badge-primary'; break;
                                case 'delivered': echo 'badge-success'; break;
                                case 'cancelled': echo 'badge-danger'; break;
                                default: echo 'badge-secondary';
                            }
                            ?>">
                            <?= ucfirst(htmlspecialchars($order['status'])) ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Các sản phẩm trong đơn</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Số lượng</th>
                                    <th>Đơn giá</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $subtotal = 0; ?>
                                <?php if ($items->num_rows > 0) : ?>
                                    <?php while ($item = $items->fetch_assoc()) : ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['product_name'] ?? 'Sản phẩm đã bị xóa') ?></td>
                                            <td><?= $item['quantity'] ?></td>
                                            <td><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
                                            <td><?= number_format($item['total_money'], 0, ',', '.') ?>đ</td>
                                        </tr>
                                        <?php $subtotal += $item['total_money']; ?>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center">Không có sản phẩm nào.</td></tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-right">Tổng phụ:</th>
                                    <th><?= number_format($subtotal, 0, ',', '.') ?>đ</th>
                                </tr>
                                <!-- Shipping/Taxes can be added here if needed -->
                                <tr>
                                    <th colspan="3" class="text-right">Tổng cộng:</th>
                                    <th><?= number_format($order['total_money'], 0, ',', '.') ?>đ</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <a href="list_orders.php" class="btn btn-secondary mb-4">
        <i class="fas fa-arrow-left"></i> Quay lại Danh sách
    </a>
</div>

<?php include '../templates/footer.php'; ?>
