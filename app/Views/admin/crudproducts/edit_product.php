<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/CategoryLevel2Model.php'; 
include_once '../../../../app/models/CategoryModel.php';

$categoryLevel2Model = new CategoryLevel2Model($conn); 
$categoryModel = new CategoryModel($conn);
$errors = [];
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: list_products.php");
    exit();
}

$product = $categoryLevel2Model->getById($id); 
if (!$product) {
    die("Danh mục cấp 2 không tồn tại."); 
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = [
        'id' => $id,
        'category_id' => (int)$_POST['category_id'],
        'sku' => trim($_POST['sku']),
        'name' => trim($_POST['name']),
        'status' => isset($_POST['status']) ? 1 : 0,
        'image' => '' // Mặc định để trống, nếu không upload ảnh mới Model sẽ không update cột này
    ];

    if (empty($data['name'])) $errors['name'] = "Tên danh mục là bắt buộc.";
    if (empty($data['sku'])) $errors['sku'] = "SKU là bắt buộc.";
    if (empty($data['category_id'])) $errors['category_id'] = "Vui lòng chọn danh mục cấp 1.";

    // --- LOGIC XỬ LÝ UPLOAD ẢNH MỚI ---
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = "../../../../public/uploads/category_level2/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($file_extension, $allowed)) {
            $file_name = 'cat2_' . time() . '.' . $file_extension;
            $target_file = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // Xóa ảnh cũ vật lý trên server nếu tồn tại ảnh mới thành công
                if (!empty($product['image'])) {
                    $old_file_path = "../../../../" . $product['image'];
                    if (file_exists($old_file_path)) {
                        unlink($old_file_path);
                    }
                }
                $data['image'] = "public/uploads/category_level2/" . $file_name;
            } else {
                $errors['image'] = "Lỗi khi lưu file mới.";
            }
        } else {
            $errors['image'] = "Định dạng ảnh không hỗ trợ.";
        }
    }

    if (empty($errors)) {
        if ($categoryLevel2Model->update($data)) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['message'] = "Cập nhật danh mục cấp 2 thành công!";
            $_SESSION['message_type'] = "success";
            header("Location: list_products.php");
            exit();
        } else {
            $errors['db'] = "Lỗi: Không thể cập nhật danh mục cấp 2. Vui lòng thử lại.";
        }
    }
    // Gộp dữ liệu mới vào biến product để hiển thị lại form nếu có lỗi
    $product = array_merge($product, $data);
}

$page_title = "Chỉnh sửa Danh mục Cấp 2"; 
include_once '../templates/header.php';
$all_categories = $categoryModel->getAll();

?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0"><?php echo $page_title; ?></h2>
    <a href="list_products.php" class="btn btn-outline-secondary shadow-sm">
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
                        <label for="name" class="form-label fw-bold">Tên Danh mục Cấp 2</label>
                        <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php if (isset($errors['name'])) echo "<div class='invalid-feedback'>{$errors['name']}</div>"; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label fw-bold">Thay đổi ảnh đại diện</label>
                        <input type="file" class="form-control <?php echo isset($errors['image']) ? 'is-invalid' : ''; ?>" id="image" name="image" accept="image/*">
                        <?php if (isset($errors['image'])) echo "<div class='text-danger small'>{$errors['image']}</div>"; ?>
                        <div class="form-text">Để trống nếu muốn giữ nguyên ảnh cũ.</div>
                    </div>
                </div>

                <div class="col-md-4 border-start">
                    <div class="mb-3 text-center">
                        <label class="form-label fw-bold d-block text-start">Ảnh hiện tại</label>
                        <?php if (!empty($product['image'])): ?>
                            <img src="../../../../<?php echo $product['image']; ?>" class="img-thumbnail shadow-sm mb-2" style="max-height: 150px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-light border rounded d-flex align-items-center justify-content-center mb-2" style="height: 150px;">
                                <span class="text-muted small">Chưa có ảnh</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-bold">Danh mục Cấp 1</label>
                        <select class="form-select <?php echo isset($errors['category_id']) ? 'is-invalid' : ''; ?>" id="category_id" name="category_id">
                            <option value="">-- Chọn danh mục cấp 1 --</option>
                            <?php mysqli_data_seek($all_categories, 0); ?>
                            <?php while ($cat = $all_categories->fetch_assoc()): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <?php if (isset($errors['category_id'])) echo "<div class='invalid-feedback'>{$errors['category_id']}</div>"; ?>
                    </div>

                    <div class="mb-3">
                        <label for="sku" class="form-label fw-bold">Mã SKU</label>
                        <input type="text" class="form-control <?php echo isset($errors['sku']) ? 'is-invalid' : ''; ?>" id="sku" name="sku" value="<?php echo htmlspecialchars($product['sku']); ?>">
                        <?php if (isset($errors['sku'])) echo "<div class='invalid-feedback'>{$errors['sku']}</div>"; ?>
                    </div>

                    <div class="mb-3 form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1" <?php echo ($product['status'] == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="status">Kích hoạt trạng thái</label>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-4 border-top pt-3">
                <button type="submit" class="btn btn-primary px-4 shadow-sm">
                    <i class="fas fa-save me-2"></i> Cập nhật Danh mục Cấp 2
                </button>
            </div>
        </form>
    </div>
</div>

<?php
include_once '../templates/footer.php';
?>