<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/CategoryLevel2Model.php'; 
include_once '../../../../app/models/CategoryModel.php';

$categoryLevel2Model = new CategoryLevel2Model($conn); 
$categoryModel = new CategoryModel($conn);

$errors = [];
$data = [
    'category_id' => '',
    'sku' => '',
    'name' => '',
    'image' => '', // Thêm trường image vào mảng data
    'status' => 1
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $data['category_id'] = (int)$_POST['category_id'];
    $data['sku'] = trim($_POST['sku']);
    $data['name'] = trim($_POST['name']);
    $data['status'] = isset($_POST['status']) ? 1 : 0;

    // Validate căn bản
    if (empty($data['name'])) $errors['name'] = "Tên danh mục là bắt buộc.";
    if (empty($data['sku'])) $errors['sku'] = "SKU là bắt buộc.";
    if (empty($data['category_id'])) $errors['category_id'] = "Vui lòng chọn danh mục cấp 1.";

    // --- LOGIC XỬ LÝ UPLOAD ẢNH ---
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = "../../../../public/uploads/category_level2/"; // Thư mục lưu ảnh
        
        // Tạo thư mục nếu chưa có
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($file_extension, $allowed_extensions)) {
            $errors['image'] = "Chỉ chấp nhận định dạng JPG, PNG, GIF, WEBP.";
        } else {
            // Đặt tên file mới để tránh trùng (ví dụ: cat2_17000000.jpg)
            $file_name = 'cat2_' . time() . '.' . $file_extension;
            $target_file = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // Lưu đường dẫn vào database (đường dẫn tương đối từ gốc web)
                $data['image'] = "public/uploads/category_level2/" . $file_name;
            } else {
                $errors['image'] = "Không thể lưu file vào hệ thống.";
            }
        }
    }
    // ----------------------------

    if (empty($errors)) {
        if ($categoryLevel2Model->create($data)) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['message'] = "Thêm danh mục cấp 2 thành công!";
            $_SESSION['message_type'] = "success";
            header("Location: list_products.php"); // Hoặc trang danh sách tương ứng
            exit();
        } else {
            $errors['db'] = "Lỗi: Không thể thêm danh mục cấp 2. Vui lòng thử lại.";
        }
    }
}

$page_title = "Thêm Danh mục Cấp 2";
include_once '../templates/header.php';
$all_categories = $categoryModel->getAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0"><?php echo $page_title; ?></h2>
        <p class="text-muted small">Thêm danh mục cấp 2 mới vào hệ thống</p>
    </div>
    <a href="list_products.php" class="btn btn-outline-secondary shadow-sm">
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
                        <label for="name" class="form-label fw-bold">Tên Danh mục Cấp 2</label>
                        <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo htmlspecialchars($data['name']); ?>">
                        <?php if (isset($errors['name'])) echo "<div class='invalid-feedback'>{$errors['name']}</div>"; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label fw-bold">Ảnh đại diện Danh mục</label>
                        <input type="file" class="form-control <?php echo isset($errors['image']) ? 'is-invalid' : ''; ?>" id="image" name="image" accept="image/*">
                        <?php if (isset($errors['image'])): ?>
                            <div class="text-danger small mt-1"><?php echo $errors['image']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-4 border-start">
                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-bold">Danh mục Cấp 1</label>
                        <select class="form-select <?php echo isset($errors['category_id']) ? 'is-invalid' : ''; ?>" id="category_id" name="category_id">
                            <option value="">-- Chọn danh mục cấp 1 --</option>
                            <?php while ($cat = $all_categories->fetch_assoc()): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($data['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <?php if (isset($errors['category_id'])) echo "<div class='invalid-feedback'>{$errors['category_id']}</div>"; ?>
                    </div>

                    <div class="mb-3">
                        <label for="sku" class="form-label fw-bold">Mã SKU</label>
                        <input type="text" class="form-control <?php echo isset($errors['sku']) ? 'is-invalid' : ''; ?>" id="sku" name="sku" value="<?php echo htmlspecialchars($data['sku']); ?>">
                        <?php if (isset($errors['sku'])) echo "<div class='invalid-feedback'>{$errors['sku']}</div>"; ?>
                    </div>

                    <div class="mb-3 form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1" <?php echo ($data['status'] == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="status">Kích hoạt trạng thái</label>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-4 border-top pt-3">
                <button type="submit" class="btn btn-primary px-4 shadow-sm">
                    <i class="fas fa-save me-2"></i> Lưu Danh mục Cấp 2
                </button>
            </div>
        </form>
    </div>
</div>

<?php include_once '../templates/footer.php'; ?>