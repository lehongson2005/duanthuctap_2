<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/CategoryModel.php';

$categoryModel = new CategoryModel($conn);
$errors = [];
$name = '';
$status = '';
$id = null;

// Logic to handle GET and POST requests must be before any HTML output
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: list_categories.php");
    exit();
}

$id = $_GET['id'];
$category = $categoryModel->getById($id);

if (!$category) {
    // We can show a simple error message without the full template
    die("Danh mục không tồn tại.");
}

// Initialize variables with existing data
$name = $category['name'];
$status = $category['status'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $status = isset($_POST['status']) ? 1 : 0;
    $id = $_POST['id'];

    if (empty($name)) {
        $errors['name'] = "Tên danh mục không được để trống.";
    }

    if (empty($errors)) {
        if ($categoryModel->update($id, $name, $status)) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['message'] = "Cập nhật danh mục thành công!";
            $_SESSION['message_type'] = "success";
            header("Location: list_categories.php");
            exit();
        } else {
            $errors['db'] = "Lỗi: Không thể cập nhật danh mục. Vui lòng thử lại.";
        }
    }
}

$page_title = "Chỉnh sửa Danh mục";
include_once '../templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0"><?php echo $page_title; ?></h2>
        <p class="text-muted small">Chỉnh sửa thông tin danh mục</p>
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

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $id; ?>" method="post">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            
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
                    <i class="fas fa-save me-2"></i> Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>

<?php
include_once '../templates/footer.php';
?>
