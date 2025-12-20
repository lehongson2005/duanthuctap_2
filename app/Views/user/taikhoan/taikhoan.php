<?php
// C:\xampp1\htdocs\DuAnThucTap_2\app\Views\user\taikhoan\taikhoan.php
include "../../../config/db.php";
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/app/Views/admin/index.php");
    exit;
}


require_once "../../../config/db.php";
require_once "../../../models/UserModel.php";

$userModel = new UserModel($conn);
$user = $userModel->getById($_SESSION['user_id']);

if (!$user) {
    die("KHÔNG CÓ DỮ LIỆU");
}

include '../header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang tài khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container my-5">
    <h1 class="mb-4">TRANG TÀI KHOẢN</h1>

    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-3">
            <div class="border p-3 bg-white">
                <p>Xin chào, <b><?= htmlspecialchars($user['full_name']) ?></b></p>
                <a class="d-block mb-2" href="#">Thông tin tài khoản</a>
                <a class="d-block mb-2" href="#">Sổ địa chỉ</a>
                <a class="d-block text-danger" href="<?php echo BASE_URL; ?>/app/Views/admin/Auth/logout.php">Đăng xuất</a>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="col-md-9">
            <div class="border p-4 bg-white">
                <h3 class="mb-3">Thông tin cá nhân</h3>

                <p><b>Tên đăng nhập:</b> <?= htmlspecialchars($user['username']) ?></p>
                <p><b>Email:</b> <?= htmlspecialchars($user['email']) ?></p>
                <p><b>Điện thoại:</b> <?= htmlspecialchars($user['phone']) ?></p>
                <p><b>Địa chỉ:</b> <?= htmlspecialchars($user['address']) ?></p>

                <hr>

                <form method="post" action="capnhat.php">
                    <div class="mb-3">
                        <label>Họ và tên</label>
                        <input type="text" name="full_name" class="form-control"
                               value="<?= htmlspecialchars($user['full_name']) ?>">
                    </div>

                    <div class="mb-3">
                        <label>Số điện thoại</label>
                        <input type="text" name="phone" class="form-control"
                               value="<?= htmlspecialchars($user['phone']) ?>">
                    </div>

                    <div class="mb-3">
                        <label>Địa chỉ</label>
                        <input type="text" name="address" class="form-control"
                               value="<?= htmlspecialchars($user['address']) ?>">
                    </div>

                    <div class="mb-3">
                        <label>Giới tính</label><br>
                        <input type="radio" name="gender" value="1" <?= $user['gender']==1?'checked':'' ?>> Nam
                        <input type="radio" name="gender" value="0" <?= $user['gender']==0?'checked':'' ?>> Nữ
                    </div>

                    <button class="btn btn-success">Cập nhật</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>
</body>
</html>
