<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/CategoryModel.php';

$categoryModel = new CategoryModel($conn);
$errors = [];
$name = '';
$status = 1; // Default to Active

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $status = isset($_POST['status']) ? 1 : 0;

    if (empty($name)) {
        $errors['name'] = "Tên danh mục không được để trống.";
    }

    if (empty($errors)) {
        if ($categoryModel->create($name, $status)) {
            // Check if session is already started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['message'] = "Thêm danh mục thành công!";
            $_SESSION['message_type'] = "success";
            header("Location: list_categories.php");
            exit();
        } else {
            $errors['db'] = "Lỗi: Không thể thêm danh mục. Vui lòng thử lại.";
        }
    }
}

$page_title = "Thêm Danh mục";
include_once '../templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0"><?php echo $page_title; ?></h2>
        <p class="text-muted small">Thêm danh mục mới vào hệ thống</p>
    </div>
    <a href="list_categories.php" class="btn btn-outline-secondary shadow-sm">
        <i class="fas fa-arrow-left me-2"></i> Quay lại
    </a>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
        <?php if (!empty($errors['db'])): ?>
            <div class="alert alert-danger"><?php echo $errors['db']; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-3">
                <label for="name" class="form-label fw-bold">Tên danh mục</label>
                <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                <?php if (isset($errors['name'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="mb-3 form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1" <?php echo ($status == 1) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="status">Kích hoạt</label>
            </div>
            
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Lưu
                </button>
            </div>
        </form>
    </div>
</div>

<?php
include_once '../templates/footer.php';
?>
