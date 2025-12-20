<?php
include '../../../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = '';
$alertType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $username  = trim($_POST['username']);
    $email     = trim($_POST['email']);
    $password  = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $gender    = $_POST['gender'];
    $phone     = trim($_POST['phone']);
    $address   = trim($_POST['address']);

    if (!isset($conn)) {
        $message = "Lỗi kết nối cơ sở dữ liệu.";
        $alertType = "danger";
    } else {
        // kiểm tra trùng username / email
        $check = $conn->prepare("
            SELECT id FROM users 
            WHERE username = ? OR email = ?
            LIMIT 1
        ");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Username hoặc Email đã tồn tại.";
            $alertType = "danger";
        } else {
            $stmt = $conn->prepare("
                INSERT INTO users 
                (full_name, username, email, password, gender, phone, address, role, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, 0, 1, NOW())
            ");
            $stmt->bind_param(
                "sssssss",
                $full_name,
                $username,
                $email,
                $password,
                $gender,
                $phone,
                $address
            );

            if ($stmt->execute()) {
                $message = "Đăng ký thành công. Bạn có thể đăng nhập.";
                $alertType = "success";
            } else {
                $message = "Lỗi hệ thống.";
                $alertType = "danger";
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #71b7e6, #9b59b6);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-card {
            width: 100%;
            max-width: 500px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,.2);
        }
        .btn-register {
            background: #238E46;
            border: none;
        }
        .btn-register:hover {
            background: #1a6d35;
        }
    </style>
</head>
<body>

<div class="card register-card p-4 bg-white">
    <h3 class="text-center fw-bold mb-4 text-secondary">ĐĂNG KÝ</h3>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show">
            <?php echo $message; ?>
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Họ và tên</label>
            <input type="text" name="full_name" class="form-control" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Giới tính</label>
            <div class="d-flex gap-4">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" value="Nam" checked>
                    <label class="form-check-label">Nam</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" value="Nữ">
                    <label class="form-check-label">Nữ</label>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Số điện thoại</label>
            <input type="text" name="phone" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Địa chỉ</label>
            <textarea name="address" class="form-control" rows="2"></textarea>
        </div>

        <button type="submit" class="btn btn-register text-white w-100 fw-bold">
            Tạo tài khoản
        </button>
    </form>

    <div class="text-center mt-3">
        Đã có tài khoản?
        <a href="login.php" class="fw-bold text-decoration-none">Đăng nhập</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
`
