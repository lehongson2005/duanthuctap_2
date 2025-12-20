<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/CategoryLevel3Model.php';
include_once '../../../../app/models/CategoryLevel2Model.php'; // Updated model

$categoryLevel3Model = new CategoryLevel3Model($conn);
$categoryLevel2Model = new CategoryLevel2Model($conn); // Updated model instantiation

$errors = [];
$data = [
    'category_level2_id' => '',
    'sku' => '',
    'name' => '',
    'status' => 1
];

// No need for handleUpload function as images are removed from category_level3

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $data['category_level2_id'] = (int)$_POST['category_level2_id'];
    $data['sku'] = trim($_POST['sku']);
    $data['name'] = trim($_POST['name']);
    $data['status'] = isset($_POST['status']) ? 1 : 0;

    if (empty($data['name'])) $errors['name'] = "Tên danh mục là bắt buộc.";
    if (empty($data['sku'])) $errors['sku'] = "SKU là bắt buộc.";
    if (empty($data['category_level2_id'])) $errors['category_level2_id'] = "Vui lòng chọn danh mục cấp 2.";

    if (empty($errors)) {
        if ($categoryLevel3Model->create($data)) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['message'] = "Thêm danh mục cấp 3 thành công!";
            $_SESSION['message_type'] = "success";
            header("Location: list_category_level3.php");
            exit();
        } else {
            $errors['db'] = "Lỗi: Không thể thêm danh mục cấp 3. Vui lòng thử lại.";
        }
    }
}

$page_title = "Thêm Danh mục Cấp 3";
include_once '../templates/header.php';

$all_categories_level2 = $categoryLevel2Model->searchAndFilter();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0"><?php echo $page_title; ?></h2>
        <p class="text-muted small">Thêm danh mục cấp 3 mới vào hệ thống</p>
    </div>
    <a href="list_category_level3.php" class="btn btn-outline-secondary shadow-sm">
        <i class="fas fa-arrow-left me-2"></i> Quay lại
    </a>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
        <?php if (!empty($errors['db'])): ?>
            <div class="alert alert-danger"><?php echo $errors['db']; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Tên Danh mục Cấp 3</label>
                        <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo htmlspecialchars($data['name']); ?>">
                        <?php if (isset($errors['name'])) echo "<div class='invalid-feedback'>{$errors['name']}</div>"; ?>
                    </div>
                    <!-- Removed Description, Price, Quantity -->
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="category_level2_id" class="form-label fw-bold">Danh mục Cấp 2</label>
                        <select class="form-select <?php echo isset($errors['category_level2_id']) ? 'is-invalid' : ''; ?>" id="category_level2_id" name="category_level2_id">
                            <option value="">-- Chọn danh mục cấp 2 --</option>
                            <?php while ($cat = $all_categories_level2->fetch_assoc()): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($data['category_level2_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <?php if (isset($errors['category_level2_id'])) echo "<div class='invalid-feedback'>{$errors['category_level2_id']}</div>"; ?>
                    </div>
                    <div class="mb-3">
                        <label for="sku" class="form-label fw-bold">Mã SKU</label>
                        <input type="text" class="form-control <?php echo isset($errors['sku']) ? 'is-invalid' : ''; ?>" id="sku" name="sku" value="<?php echo htmlspecialchars($data['sku']); ?>">
                        <?php if (isset($errors['sku'])) echo "<div class='invalid-feedback'>{$errors['sku']}</div>"; ?>
                    </div>
                    <!-- Removed Image and Image Hover -->
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1" <?php echo ($data['status'] == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="status">Kích hoạt</label>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Lưu Danh mục Cấp 3
                </button>
            </div>
        </form>
    </div>
</div>

<?php
include_once '../templates/footer.php';
?>