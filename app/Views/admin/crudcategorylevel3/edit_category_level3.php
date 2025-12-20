<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/CategoryLevel3Model.php';
include_once '../../../../app/models/CategoryLevel2Model.php'; // Updated model

$categoryLevel3Model = new CategoryLevel3Model($conn);
$categoryLevel2Model = new CategoryLevel2Model($conn); // Updated model instantiation
$errors = [];
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: list_category_level3.php");
    exit();
}

$item = $categoryLevel3Model->getById($id);
if (!$item) {
    die("Danh mục cấp 3 không tồn tại."); // Updated message
}

// No need for handleUpload function as images are removed from category_level3

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = [
        'id' => $id,
        'category_level2_id' => (int)$_POST['category_level2_id'],
        'sku' => trim($_POST['sku']),
        'name' => trim($_POST['name']),
        'status' => isset($_POST['status']) ? 1 : 0
    ];

    if (empty($data['name'])) $errors['name'] = "Tên danh mục là bắt buộc.";
    if (empty($data['sku'])) $errors['sku'] = "SKU là bắt buộc.";
    if (empty($data['category_level2_id'])) $errors['category_level2_id'] = "Vui lòng chọn danh mục cấp 2.";

    if (empty($errors)) {
        if ($categoryLevel3Model->update($data)) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['message'] = "Cập nhật danh mục cấp 3 thành công!";
            $_SESSION['message_type'] = "success";
            header("Location: list_category_level3.php");
            exit();
        } else {
            $errors['db'] = "Lỗi: Không thể cập nhật danh mục cấp 3. Vui lòng thử lại.";
        }
    }
    $item = array_merge($item, $data);
}

$page_title = "Chỉnh sửa Danh mục Cấp 3"; // Updated title
include_once '../templates/header.php';
$all_categories_level2 = $categoryLevel2Model->searchAndFilter();

?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0"><?php echo $page_title; ?></h2>
    <a href="list_category_level3.php" class="btn btn-outline-secondary shadow-sm">
        <i class="fas fa-arrow-left me-2"></i> Quay lại
    </a>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
        <?php if (!empty($errors['db'])): ?>
            <div class="alert alert-danger"><?php echo $errors['db']; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $id; ?>" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Tên Danh mục Cấp 3</label>
                        <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo htmlspecialchars($item['name']); ?>">
                        <?php if (isset($errors['name'])) echo "<div class='invalid-feedback'>{$errors['name']}</div>"; ?>
                    </div>
                    <!-- Removed Description, Price, Quantity -->
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="category_level2_id" class="form-label fw-bold">Danh mục Cấp 2</label>
                        <select class="form-select <?php echo isset($errors['category_level2_id']) ? 'is-invalid' : ''; ?>" id="category_level2_id" name="category_level2_id">
                            <option value="">-- Chọn danh mục cấp 2 --</option>
                            <?php 
                            mysqli_data_seek($all_categories_level2, 0); // Reset pointer
                            while ($cat = $all_categories_level2->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($item['category_level2_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <?php if (isset($errors['category_level2_id'])) echo "<div class='invalid-feedback'>{$errors['category_level2_id']}</div>"; ?>
                    </div>
                    <div class="mb-3">
                        <label for="sku" class="form-label fw-bold">Mã SKU</label>
                        <input type="text" class="form-control <?php echo isset($errors['sku']) ? 'is-invalid' : ''; ?>" id="sku" name="sku" value="<?php echo htmlspecialchars($item['sku']); ?>">
                        <?php if (isset($errors['sku'])) echo "<div class='invalid-feedback'>{$errors['sku']}</div>"; ?>
                    </div>
                    <!-- Removed Image and Image Hover -->
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1" <?php echo ($item['status'] == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="status">Kích hoạt</label>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Cập nhật Danh mục Cấp 3
                </button>
            </div>
        </form>
    </div>
</div>

<?php
include_once '../templates/footer.php';
?>