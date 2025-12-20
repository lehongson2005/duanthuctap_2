<?php
include_once '../../../config/db.php';
include_once '../../../../app/models/UserModel.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userModel = new UserModel($conn);

/* ===== INIT ===== */
$errors = [];
$username = $email = $full_name = $phone = $address = '';
$gender = '';
$role = 0;
$status = 1;

/* ===== HANDLE POST ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username   = trim($_POST['username']);
    $email      = trim($_POST['email']);
    $password   = $_POST['password'];
    $full_name  = trim($_POST['full_name']);
    $gender     = $_POST['gender'] ?? '';
    $phone      = trim($_POST['phone']);
    $address    = trim($_POST['address']);
    $role       = (int)$_POST['role'];
    $status     = (int)$_POST['status'];

    /* ===== VALIDATION ===== */
    if ($username === '') {
        $errors['username'] = 'Username bắt buộc';
    } elseif ($userModel->isUsernameExists($username)) {
        $errors['username'] = 'Username đã tồn tại';
    }

    if ($email === '') {
        $errors['email'] = 'Email bắt buộc';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email không hợp lệ';
    } elseif ($userModel->isEmailExists($email)) {
        $errors['email'] = 'Email đã tồn tại';
    }

    if ($password === '' || strlen($password) < 6) {
        $errors['password'] = 'Mật khẩu tối thiểu 6 ký tự';
    }

    if ($full_name === '') {
        $errors['full_name'] = 'Họ tên bắt buộc';
    }

    if ($phone !== '' && !preg_match('/^[0-9]{9,11}$/', $phone)) {
        $errors['phone'] = 'SĐT không hợp lệ';
    }

    /* ===== CREATE ===== */
    if (empty($errors)) {
        $ok = $userModel->create(
            $username,
            $email,
            $password,
            $full_name,
            $gender,
            $phone,
            $address,
            $role,
            $status
        );

        if ($ok) {
            header('Location: list_users.php?success=1');
            exit;
        }

        $errors['general'] = 'Lỗi khi thêm user';
    }
}

$page_title = "Thêm thành viên";
include_once '../templates/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card shadow-sm">
                <div class="card-header fw-bold">THÊM USER</div>
                <div class="card-body">

                    <?php if(isset($errors['general'])): ?>
                        <div class="alert alert-danger"><?= $errors['general'] ?></div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label>Username</label>
                            <input name="username" class="form-control <?= isset($errors['username'])?'is-invalid':'' ?>" value="<?= htmlspecialchars($username) ?>">
                            <div class="invalid-feedback"><?= $errors['username'] ?? '' ?></div>
                        </div>

                        <div class="mb-3">
                            <label>Email</label>
                            <input name="email" class="form-control <?= isset($errors['email'])?'is-invalid':'' ?>" value="<?= htmlspecialchars($email) ?>">
                            <div class="invalid-feedback"><?= $errors['email'] ?? '' ?></div>
                        </div>

                        <div class="mb-3">
                            <label>Mật khẩu</label>
                            <input type="password" name="password" class="form-control <?= isset($errors['password'])?'is-invalid':'' ?>">
                            <div class="invalid-feedback"><?= $errors['password'] ?? '' ?></div>
                        </div>

                        <div class="mb-3">
                            <label>Họ tên</label>
                            <input name="full_name" class="form-control <?= isset($errors['full_name'])?'is-invalid':'' ?>" value="<?= htmlspecialchars($full_name) ?>">
                            <div class="invalid-feedback"><?= $errors['full_name'] ?? '' ?></div>
                        </div>

                        <div class="mb-3">
                            <label>Giới tính</label>
                            <select name="gender" class="form-select">
                                <option value="">-- Chọn --</option>
                                <option value="male" <?= $gender=='male'?'selected':'' ?>>Nam</option>
                                <option value="female" <?= $gender=='female'?'selected':'' ?>>Nữ</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Số điện thoại</label>
                            <input name="phone" class="form-control <?= isset($errors['phone'])?'is-invalid':'' ?>" value="<?= htmlspecialchars($phone) ?>">
                            <div class="invalid-feedback"><?= $errors['phone'] ?? '' ?></div>
                        </div>

                        <div class="mb-3">
                            <label>Địa chỉ</label>
                            <textarea name="address" class="form-control"><?= htmlspecialchars($address) ?></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label>Role</label>
                                <select name="role" class="form-select">
                                    <option value="0">User</option>
                                    <option value="1">Admin</option>
                                </select>
                            </div>
                            <div class="col">
                                <label>Status</label>
                                <select name="status" class="form-select">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="list_users.php" class="btn btn-secondary">Quay lại</a>
                            <button class="btn btn-primary">Lưu</button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<?php include_once '../templates/footer.php'; ?>
