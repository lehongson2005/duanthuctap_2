<?php
include '../../../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = '';
$alertType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!isset($conn)) {
        $message = "Lỗi kết nối cơ sở dữ liệu.";
        $alertType = "danger";
    } else {
        $stmt = $conn->prepare("
            SELECT id, username, password, role, status 
            FROM users 
            WHERE username = ? OR email = ?
            LIMIT 1
        ");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $message = "Tài khoản không tồn tại.";
            $alertType = "danger";
        } else {
            $stmt->bind_result($id, $user, $hash, $role, $status);
            $stmt->fetch();

            if (!password_verify($password, $hash)) {
                $message = "Mật khẩu không đúng.";
                $alertType = "danger";
            } elseif ($status != 1) {
                $message = "Tài khoản đã bị khóa hoặc chưa kích hoạt.";
                $alertType = "danger";
            } else {
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $user;
                $_SESSION['role'] = $role;

                if ($role == 1) {
                    header("Location: ../index.php");
                } else {
                    header("Location: ../../../../index.php");
                }
                exit;
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
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
        .login-card {
            width: 100%;
            max-width: 400px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,.2);
        }
        .btn-login {
            background: #9b59b6;
            border: none;
        }
        .btn-login:hover {
            background: #8e44ad;
        }
    </style>
</head>
<body>

<div class="card login-card p-4 bg-white">
    <h3 class="text-center fw-bold mb-4 text-secondary">ĐĂNG NHẬP</h3>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show">
            <?php echo $message; ?>
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Tên đăng nhập hoặc Email</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-login text-white w-100 fw-bold">
            Đăng nhập
        </button>
    </form>

    <div class="text-center mt-3">
        Chưa có tài khoản?
        <a href="register.php" class="fw-bold text-decoration-none">Đăng ký</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
