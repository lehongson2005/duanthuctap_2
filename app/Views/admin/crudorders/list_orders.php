<?php
// list_orders.php

// 1. BOOTSTRAP
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: ../../auth/login.php");
    exit;
}
include_once '../../../../app/config/db.php';
require_once '../../../models/OrderModel.php';
$orderModel = new OrderModel($conn);

// 2. LOGIC
// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Filtering and Searching
$keyword = $_GET['keyword'] ?? '';
$status_filter = $_GET['status'] ?? '';
$order_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

// Get total and items
$totalItems = $orderModel->getTotal($keyword, $status_filter);
$totalPages = ceil($totalItems / $limit);
$orders = $orderModel->searchAndFilter($keyword, $status_filter, $limit, $offset);

$page_title = "Quản lý Đơn hàng";
?>

<?php include '../templates/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Quản lý Đơn hàng</h1>
    <p class="mb-4">Danh sách các đơn hàng có trên hệ thống.</p>

    <!-- Search and Filter Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Bộ lọc và Tìm kiếm</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="list_orders.php">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="keyword">Tìm kiếm</label>
                            <input type="text" class="form-control" id="keyword" name="keyword" placeholder="Nhập tên, email, SĐT khách hàng..." value="<?= htmlspecialchars($keyword) ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Lọc theo trạng thái</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">Tất cả trạng thái</option>
                                <?php foreach ($order_statuses as $status) : ?>
                                    <option value="<?= $status ?>" <?= ($status_filter == $status) ? 'selected' : '' ?>>
                                        <?= ucfirst($status) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Lọc</button>
                             <a href="list_orders.php" class="btn btn-secondary">Xóa lọc</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách Đơn hàng</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Mã ĐH</th>
                            <th>Tên Khách Hàng</th>
                            <th>Tổng Tiền</th>
                            <th>Ngày Đặt</th>
                            <th>Trạng Thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($orders->num_rows > 0) : ?>
                            <?php while ($order = $orders->fetch_assoc()) : ?>
                                <tr>
                                    <td>#<?= htmlspecialchars($order['id']) ?></td>
                                    <td><?= htmlspecialchars($order['full_name']) ?></td>
                                    <td><?= number_format($order['total_money'], 0, ',', '.') ?>đ</td>
                                    <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                                    <td>
                                        <span class="badge 
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
                                    </td>
                                    <td>
                                        <a href="view_order.php?id=<?= $order['id'] ?>" class="btn btn-info btn-sm">Xem</a>
                                        <a href="edit_order.php?id=<?= $order['id'] ?>" class="btn btn-warning btn-sm">Sửa TT</a>
                                        <a href="delete_order.php?id=<?= $order['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?');">Xóa</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="6" class="text-center">Không có đơn hàng nào.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1) : ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1) : ?>
                            <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>&keyword=<?= urlencode($keyword) ?>&status=<?= $status_filter ?>">Trước</a></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&keyword=<?= urlencode($keyword) ?>&status=<?= $status_filter ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages) : ?>
                            <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>&keyword=<?= urlencode($keyword) ?>&status=<?= $status_filter ?>">Sau</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        </div>
    </div>
</div>
<!-- /.container-fluid -->

<?php include '../templates/footer.php'; ?>
