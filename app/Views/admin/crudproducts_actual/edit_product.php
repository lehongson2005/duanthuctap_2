<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/ProductModel.php';
include_once '../../../../app/models/CategoryModel.php';
include_once '../../../../app/models/CategoryLevel2Model.php';
include_once '../../../../app/models/CategoryLevel3Model.php';

$productModel = new ProductModel($conn);
$categoryModel = new CategoryModel($conn);
$categoryLevel2Model = new CategoryLevel2Model($conn);
$categoryLevel3Model = new CategoryLevel3Model($conn);

if (session_status() === PHP_SESSION_NONE) session_start();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['message'] = "ID sản phẩm không hợp lệ!";
    $_SESSION['message_type'] = "danger";
    header("Location: list_products.php");
    exit();
}

$data = $productModel->getById($id);
if (!$data) {
    $_SESSION['message'] = "Không tìm thấy sản phẩm!";
    $_SESSION['message_type'] = "danger";
    header("Location: list_products.php");
    exit();
}

// Convert null values to empty strings for form fields
foreach ($data as $key => $value) {
    if ($value === null) {
        $data[$key] = '';
    }
}

$errors = [];

function handleUpload($file_input_name, $upload_dir) {
    if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] == 0) {
        $filename = uniqid() . '-' . basename($_FILES[$file_input_name]['name']);
        $target_path = $upload_dir . $filename;
        if (move_uploaded_file($_FILES[$file_input_name]['tmp_name'], $target_path)) {
            return 'public/uploads/products_actual/' . $filename;
        }
    }
    return null;
}

function createSlug($string) {
    $search = ['#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#', '#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#', '#(ì|í|ị|ỉ|ĩ)#', '#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#', '#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#', '#(ỳ|ý|ỵ|ỷ|ỹ)#', '#(đ)#', '#(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)#', '#(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)#', '#(Ì|Í|Ị|Ỉ|Ĩ)#', '#(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)#', '#(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)#', '#(Ỳ|Ý|Ỵ|Ỷ|Ỹ)#', '#(Đ)#', '/[^a-zA-Z0-9\-\_]/'];
    $replace = ['a', 'e', 'i', 'o', 'u', 'y', 'd', 'A', 'E', 'I', 'O', 'U', 'Y', 'D', '-'];
    $string = preg_replace($search, $replace, $string);
    $string = preg_replace('/(-)+/', '-', $string);
    return strtolower($string);
}

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $original_data = $data; // Keep original data for image comparison
        $data = array_merge($data, $_POST); // Merge POST data with existing data
        
        $data['category_level1_id'] = (int)$_POST['category_level1_id'];
        $data['category_level2_id'] = !empty($_POST['category_level2_id']) ? (int)$_POST['category_level2_id'] : null;
        $data['category_level3_id'] = !empty($_POST['category_level3_id']) ? (int)$_POST['category_level3_id'] : null;
        $data['name'] = trim($_POST['name']);
        $data['slug'] = !empty(trim($_POST['slug'])) ? trim($_POST['slug']) : createSlug($data['name']);
        $data['price'] = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
        $data['discount_price'] = !empty($_POST['discount_price']) ? filter_var($_POST['discount_price'], FILTER_VALIDATE_FLOAT) : null;
        $data['stock_quantity'] = filter_var($_POST['stock_quantity'], FILTER_VALIDATE_INT);
        $data['weight'] = !empty($_POST['weight']) ? filter_var($_POST['weight'], FILTER_VALIDATE_FLOAT) : null;
        $data['status'] = isset($_POST['status']) ? 1 : 0;
        $data['is_featured'] = isset($_POST['is_featured']) ? 1 : 0;
        $data['is_new'] = isset($_POST['is_new']) ? 1 : 0;
        $data['id'] = $id;

        if (empty($data['name'])) $errors['name'] = "Tên sản phẩm là bắt buộc.";
        if (empty($data['sku'])) $errors['sku'] = "SKU là bắt buộc.";
        if ($data['price'] === false || $data['price'] < 0) $errors['price'] = "Giá không hợp lệ.";
        if ($data['stock_quantity'] === false || $data['stock_quantity'] < 0) $errors['stock_quantity'] = "Số lượng tồn kho không hợp lệ.";
        if (empty($data['category_level1_id'])) $errors['category_level1_id'] = "Danh mục cấp 1 là bắt buộc.";

        $upload_dir = '../../../../public/uploads/products_actual/';
        
        $new_thumbnail_path = handleUpload('thumbnail', $upload_dir);
        if ($new_thumbnail_path) {
            $data['thumbnail'] = $new_thumbnail_path;
        } else {
            $data['thumbnail'] = $original_data['thumbnail']; // Keep old thumbnail if no new one
        }

        $new_image_hover_path = handleUpload('image_hover', $upload_dir);
        if ($new_image_hover_path) {
            $data['image_hover'] = $new_image_hover_path;
        } else {
            $data['image_hover'] = $original_data['image_hover']; // Keep old image_hover if no new one
        }

        if (empty($errors)) {
            if ($productModel->update($data)) {
                if ($new_thumbnail_path && $new_thumbnail_path !== $original_data['thumbnail']) {
                    $old_image_full_path = '../../../../' . $original_data['thumbnail'];
                    if (file_exists($old_image_full_path)) {
                        unlink($old_image_full_path);
                    }
                }
                if ($new_image_hover_path && $new_image_hover_path !== $original_data['image_hover']) {
                    $old_image_hover_full_path = '../../../../' . $original_data['image_hover'];
                    if (file_exists($old_image_hover_full_path)) {
                        unlink($old_image_hover_full_path);
                    }
                }
                $_SESSION['message'] = "Cập nhật sản phẩm thành công!";
                $_SESSION['message_type'] = "success";
                header("Location: list_products.php");
                exit();
            } else {
                $errors['db'] = "Lỗi: Không thể cập nhật sản phẩm. Vui lòng thử lại.";
            }
        }
    }
$page_title = "Chỉnh sửa Sản phẩm";
include_once '../templates/header.php';

$all_categories_level1 = $categoryModel->getAll();
// These will be fetched dynamically via AJAX
$all_categories_level2 = []; 
$all_categories_level3 = [];

?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0"><?php echo $page_title; ?></h2>
    <a href="list_products.php" class="btn btn-outline-secondary shadow-sm"><i class="fas fa-arrow-left me-2"></i> Quay lại</a>
</div>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <?php if (!empty($errors['db'])): ?>
                        <div class="alert alert-danger"><?php echo $errors['db']; ?></div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Tên sản phẩm</label>
                        <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo htmlspecialchars($data['name']); ?>">
                        <?php if (isset($errors['name'])) echo "<div class='invalid-feedback'>{$errors['name']}</div>"; ?>
                    </div>
                    <div class="mb-3">
                        <label for="slug" class="form-label fw-bold">Slug (URL)</label>
                        <input type="text" class="form-control" id="slug" name="slug" value="<?php echo htmlspecialchars($data['slug']); ?>" placeholder="Để trống để tự tạo từ tên">
                    </div>
                    <div class="mb-3">
                        <label for="short_description" class="form-label fw-bold">Mô tả ngắn</label>
                        <textarea class="form-control" id="short_description" name="short_description" rows="3"><?php echo htmlspecialchars($data['short_description']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="long_description" class="form-label fw-bold">Mô tả chi tiết</label>
                        <textarea class="form-control" id="long_description" name="long_description" rows="10"><?php echo htmlspecialchars($data['long_description']); ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label fw-bold">Giá</label>
                            <input type="number" step="0.01" class="form-control <?php echo isset($errors['price']) ? 'is-invalid' : ''; ?>" id="price" name="price" value="<?php echo htmlspecialchars($data['price']); ?>">
                            <?php if (isset($errors['price'])) echo "<div class='invalid-feedback'>{$errors['price']}</div>"; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="discount_price" class="form-label fw-bold">Giá khuyến mãi (tùy chọn)</label>
                            <input type="number" step="0.01" class="form-control" id="discount_price" name="discount_price" value="<?php echo htmlspecialchars($data['discount_price']); ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="stock_quantity" class="form-label fw-bold">Số lượng tồn kho</label>
                        <input type="number" class="form-control <?php echo isset($errors['stock_quantity']) ? 'is-invalid' : ''; ?>" id="stock_quantity" name="stock_quantity" value="<?php echo htmlspecialchars($data['stock_quantity']); ?>">
                        <?php if (isset($errors['stock_quantity'])) echo "<div class='invalid-feedback'>{$errors['stock_quantity']}</div>"; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-light py-3"><h5 class="mb-0">Phân loại</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="category_level1_id" class="form-label fw-bold">Danh mục Cấp 1</label>
                        <select class="form-select <?php echo isset($errors['category_level1_id']) ? 'is-invalid' : ''; ?>" id="category_level1_id" name="category_level1_id">
                            <option value="">-- Chọn cấp 1 --</option>
                            <?php mysqli_data_seek($all_categories_level1, 0); ?>
                            <?php while ($cat1 = $all_categories_level1->fetch_assoc()): ?>
                                <option value="<?php echo $cat1['id']; ?>" <?php echo ($data['category_level1_id'] == $cat1['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat1['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <?php if (isset($errors['category_level1_id'])) echo "<div class='invalid-feedback'>{$errors['category_level1_id']}</div>"; ?>
                    </div>
                    <div class="mb-3">
                        <label for="category_level2_id" class="form-label fw-bold">Danh mục Cấp 2 (tùy chọn)</label>
                        <select class="form-select" id="category_level2_id" name="category_level2_id">
                            <option value="">-- Chọn cấp 2 --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="category_level3_id" class="form-label fw-bold">Danh mục Cấp 3 (tùy chọn)</label>
                        <select class="form-select" id="category_level3_id" name="category_level3_id">
                            <option value="">-- Chọn cấp 3 --</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-light py-3"><h5 class="mb-0">Thông tin khác</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="sku" class="form-label fw-bold">Mã SKU</label>
                        <input type="text" class="form-control <?php echo isset($errors['sku']) ? 'is-invalid' : ''; ?>" id="sku" name="sku" value="<?php echo htmlspecialchars($data['sku']); ?>">
                        <?php if (isset($errors['sku'])) echo "<div class='invalid-feedback'>{$errors['sku']}</div>"; ?>
                    </div>
                     <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="weight" class="form-label fw-bold">Trọng lượng</label>
                            <input type="number" step="0.01" class="form-control" id="weight" name="weight" value="<?php echo htmlspecialchars($data['weight']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="weight_unit" class="form-label fw-bold">Đơn vị</label>
                            <input type="text" class="form-control" id="weight_unit" name="weight_unit" value="<?php echo htmlspecialchars($data['weight_unit']); ?>" placeholder="VD: kg, gói, lít">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mb-4">
                 <div class="card-header bg-light py-3"><h5 class="mb-0">Ảnh Thumbnail</h5></div>
                <div class="card-body">
                    <input type="file" class="form-control" id="thumbnail" name="thumbnail">
                    <?php if (!empty($data['thumbnail'])): ?>
                        <div class="mt-2">
                            <img src="<?php echo BASE_URL . '/' . htmlspecialchars($data['thumbnail']); ?>" alt="Current Thumbnail" style="width: 100%; height: auto; border-radius: 4px;">
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mb-4">
                 <div class="card-header bg-light py-3"><h5 class="mb-0">Ảnh Hover</h5></div>
                <div class="card-body">
                    <input type="file" class="form-control" id="image_hover" name="image_hover">
                    <?php if (!empty($data['image_hover'])): ?>
                        <div class="mt-2">
                            <img src="<?php echo BASE_URL . '/' . htmlspecialchars($data['image_hover']); ?>" alt="Current Image Hover" style="width: 100%; height: auto; border-radius: 4px;">
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-light py-3"><h5 class="mb-0">Trạng thái & Nổi bật</h5></div>
                <div class="card-body">
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1" <?php echo ($data['status'] == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="status">Kích hoạt</label>
                    </div>
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_featured" name="is_featured" value="1" <?php echo ($data['is_featured'] == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_featured">Sản phẩm nổi bật</label>
                    </div>
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_new" name="is_new" value="1" <?php echo ($data['is_new'] == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_new">Sản phẩm mới</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-end mt-4">
        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save me-2"></i> Cập nhật Sản phẩm</button>
    </div>
</form>

<?php include_once '../templates/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoryLevel1Select = document.getElementById('category_level1_id');
        const categoryLevel2Select = document.getElementById('category_level2_id');
        const categoryLevel3Select = document.getElementById('category_level3_id');

        function loadCategories(level, parentId, targetSelect, selectedValue = '') {
            if (!parentId) {
                targetSelect.innerHTML = '<option value="">-- Chọn cấp ' + level + ' --</option>';
                if (level === 2) categoryLevel3Select.innerHTML = '<option value="">-- Chọn cấp 3 --</option>';
                return;
            }

            fetch('../../../../app/api/get_categories.php?level=' + level + '&parent_id=' + parentId)
                .then(response => response.json())
                .then(data => {
                    let oldHTML = '<option value="">-- Chọn cấp ' + level + ' --</option>';
                    targetSelect.innerHTML = oldHTML;
                    data.forEach(cat => {
                        const option = document.createElement('option');
                        option.value = cat.id;
                        option.textContent = cat.name;
                        if (selectedValue && selectedValue == cat.id) {
                            option.selected = true;
                        }
                        targetSelect.appendChild(option);
                    });
                    // If Level 2 has just loaded and a value was selected, try to load Level 3
                    if (level === 2 && selectedValue) {
                         loadCategories(3, selectedValue, categoryLevel3Select, '<?php echo $data['category_level3_id']; ?>');
                    }
                })
                .catch(error => console.error('Error loading categories:', error));
        }

        categoryLevel1Select.addEventListener('change', function() {
            loadCategories(2, this.value, categoryLevel2Select);
             // Reset level 3 when level 1 changes
            categoryLevel3Select.innerHTML = '<option value="">-- Chọn cấp 3 --</option>';
        });

        categoryLevel2Select.addEventListener('change', function() {
            loadCategories(3, this.value, categoryLevel3Select);
        });

        // Initial load for edit mode
        if (categoryLevel1Select.value) {
            loadCategories(2, categoryLevel1Select.value, categoryLevel2Select, '<?php echo $data['category_level2_id']; ?>');
        }
    });
</script>