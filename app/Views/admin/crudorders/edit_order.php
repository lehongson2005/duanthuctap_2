<?php
// edit_order.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: ../../auth/login.php");
    exit;
}

include_once '../../../../app/config/db.php';
require_once '../../../models/OrderModel.php';

$orderModel = new OrderModel($conn);
$page_title = "Cập nhật Trạng thái Đơn hàng";

// Check for ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "ID đơn hàng không hợp lệ.";
    header("Location: list_orders.php");
    exit;
}
$id = (int)$_GET['id'];

// All possible statuses
$order_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? '';

    // Validate status
    if (in_array($status, $order_statuses)) {
        if ($orderModel->updateStatus($id, $status)) {
            $_SESSION['success_message'] = "Cập nhật trạng thái đơn hàng thành công.";
        } else {
            $_SESSION['error_message'] = "Cập nhật trạng thái thất bại.";
        }
    } else {
        $_SESSION['error_message'] = "Trạng thái không hợp lệ.";
    }
    header("Location: list_orders.php");
    exit;
}

// Handle GET request
$order = $orderModel->getById($id);
if (!$order) {
    $_SESSION['error_message'] = "Đơn hàng không tồn tại.";
    header("Location: list_orders.php");
    exit;
}

?>

<?php include '../templates/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Cập nhật Trạng thái Đơn hàng #<?= $id ?></h1>
    <p class="mb-4">Thay đổi trạng thái xử lý cho đơn hàng.</p>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Thông tin Đơn hàng</h6>
        </div>
        <div class="card-body">
            <p><strong>Tên khách hàng:</strong> <?= htmlspecialchars($order['full_name']) ?></p>
            <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></p>
            <p><strong>Tổng tiền:</strong> <?= number_format($order['total_money'], 0, ',', '.') ?>đ</p>
            
            <form action="edit_order.php?id=<?= $id ?>" method="POST">
                <div class="form-group">
                    <label for="status">Trạng thái đơn hàng</label>
                    <select class="form-control" id="status" name="status">
                        <?php foreach ($order_statuses as $status) : ?>
                            <option value="<?= $status ?>" <?= ($order['status'] == $status) ? 'selected' : '' ?>>
                                <?= ucfirst($status) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="list_orders.php" class="btn btn-secondary">Quay lại</a>
            </form>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
