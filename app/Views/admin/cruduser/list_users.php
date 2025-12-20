<?php
include_once '../../../../app/models/UserModel.php';

$page_title = "Quản lý thành viên";
include_once '../templates/header.php';

$userModel = new UserModel($conn);

// Pagination settings
$limit = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get search and filter parameters
$keyword = $_GET['keyword'] ?? '';
$role = $_GET['role'] ?? '';
$status = $_GET['status'] ?? '';

// Get total records for pagination
$total_records = $userModel->getTotalUsers($keyword, $role, $status);
$total_pages = ceil($total_records / $limit);

// Get records for the current page
$users = $userModel->searchAndFilter($keyword, $role, $status, $limit, $offset);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0"><?php echo $page_title; ?></h2>
        <p class="text-muted small">Danh sách toàn bộ user trong hệ thống</p>
    </div>
    <a href="add_user.php" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus me-2"></i>Thêm User
    </a>
</div>

<!-- Search and Filter Form -->
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body">
        <form method="GET" action="">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="keyword" class="form-control" placeholder="Tìm theo username, email..." value="<?php echo htmlspecialchars($keyword); ?>">
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">-- Lọc theo vai trò --</option>
                        <option value="1" <?php echo ($role === '1') ? 'selected' : ''; ?>>Admin</option>
                        <option value="0" <?php echo ($role === '0') ? 'selected' : ''; ?>>User</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">-- Lọc theo trạng thái --</option>
                        <option value="1" <?php echo ($status === '1') ? 'selected' : ''; ?>>Active</option>
                        <option value="0" <?php echo ($status === '0') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-search"></i> Lọc</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4 overflow-hidden">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
               <thead class="table-light">
<tr>
    <th>ID</th>
    <th>User</th>
    <th>Email</th>
    <th>Họ tên</th>
    <th class="text-center">Giới tính</th>
    <th>SĐT</th>
    <th>Địa chỉ</th>
    <th class="text-center">Role</th>
    <th class="text-center">Status</th>
    <th class="text-center">Created</th>
    <th class="text-end">Action</th>
</tr>
</thead>

                <tbody>
                    <?php if($users->num_rows > 0): ?>
                        <?php while($row = $users->fetch_assoc()): ?>
                       <tr>
    <td><?php echo $row['id']; ?></td>

    <td>
        <div class="fw-bold"><?php echo htmlspecialchars($row['username']); ?></div>
    </td>

    <td><?php echo htmlspecialchars($row['email']); ?></td>

    <td><?php echo htmlspecialchars($row['full_name']); ?></td>

    <td class="text-center">
        <?php
        echo match($row['gender']) {
            'Nam' => '<span class="badge bg-primary">Nam</span>',
            'Nữ'  => '<span class="badge bg-danger">Nữ</span>',
            default => '<span class="badge bg-secondary">Khác</span>',
        };
        ?>
    </td>

    <td><?php echo htmlspecialchars($row['phone']); ?></td>

    <td style="max-width:200px;">
        <small><?php echo htmlspecialchars($row['address']); ?></small>
    </td>

    <td class="text-center">
        <?php echo ($row['role']==1)
            ? '<span class="badge bg-warning text-dark">Admin</span>'
            : '<span class="badge bg-secondary">User</span>'; ?>
    </td>

    <td class="text-center">
        <?php echo ($row['status']==1)
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-danger">Inactive</span>'; ?>
    </td>

    <td class="text-center small">
        <?php echo date('d/m/Y', strtotime($row['created_at'])); ?>
    </td>

    <td class="text-end">
        <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">
            <i class="fas fa-edit"></i>
        </a>
        <a href="delete_user.php?id=<?php echo $row['id']; ?>" 
           class="btn btn-danger btn-sm"
           onclick="return confirm('Bạn có chắc muốn xóa?')">
            <i class="fas fa-trash"></i>
        </a>
    </td>
</tr>

                        <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Không tìm thấy kết quả nào.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>&keyword=<?php echo urlencode($keyword); ?>&role=<?php echo urlencode($role); ?>&status=<?php echo urlencode($status); ?>">Previous</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>"><a class="page-link" href="?page=<?php echo $i; ?>&keyword=<?php echo urlencode($keyword); ?>&role=<?php echo urlencode($role); ?>&status=<?php echo urlencode($status); ?>"><?php echo $i; ?></a></li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>&keyword=<?php echo urlencode($keyword); ?>&role=<?php echo urlencode($role); ?>&status=<?php echo urlencode($status); ?>">Next</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>

<?php
include_once '../templates/footer.php';
?>