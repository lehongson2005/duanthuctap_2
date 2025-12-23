<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/IconMenuModel.php';
include_once '../../../../app/models/CategoryModel.php';
include_once '../../../../app/models/ProductModel.php';

$iconMenuModel = new IconMenuModel($conn);
$categoryModel = new CategoryModel($conn);
$productModel = new ProductModel($conn);

$categories = $categoryModel->getAll()->fetch_all(MYSQLI_ASSOC);
$products = $productModel->getAll()->fetch_all(MYSQLI_ASSOC); // Assuming getAllSimple fetches ID and Name

$errors = [];
$data = [
    'title' => '',
    'slug' => '',
    'link' => '',
    'link_type' => 'custom', // Default to custom
    'link_target_id' => null, // Default to null
    'image' => '',
    'sort_order' => 0,
    'status' => 1
];

// Function to handle file uploads
function handleUpload($file_input_name, $upload_dir) {
    if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] == 0) {
        $filename = uniqid() . '-' . basename($_FILES[$file_input_name]['name']);
        $target_path = $upload_dir . $filename;
        if (move_uploaded_file($_FILES[$file_input_name]['tmp_name'], $target_path)) {
            // Return web-accessible path
            return 'public/uploads/icon_menu/' . $filename;
        }
    }
    return null;
}

// Function to create a URL-friendly slug
function createSlug($string) {
    $search = [
        '#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#',
        '#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#',
        '#(ì|í|ị|ỉ|ĩ)#',
        '#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#',
        '#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#',
        '#(ỳ|ý|ỵ|ỷ|ỹ)#',
        '#(đ)#',
        '#(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)#',
        '#(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)#',
        '#(Ì|Í|Ị|Ỉ|Ĩ)#',
        '#(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)#',
        '#(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)#',
        '#(Ỳ|Ý|Ỵ|Ỷ|Ỹ)#',
        '#(Đ)#',
        '/[^a-zA-Z0-9\-\_]/',
    ];
    $replace = [
        'a', 'e', 'i', 'o', 'u', 'y', 'd',
        'A', 'E', 'I', 'O', 'U', 'Y', 'D',
        '-',
    ];
    $string = preg_replace($search, $replace, $string);
    $string = preg_replace('/(-)+/', '-', $string);
    $string = strtolower($string);
    return $string;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $data['title'] = trim($_POST['title']);
    $data['link_type'] = trim($_POST['link_type']);
    $data['sort_order'] = filter_var($_POST['sort_order'], FILTER_VALIDATE_INT);
    $data['status'] = isset($_POST['status']) ? 1 : 0;
    $data['slug'] = !empty($_POST['slug']) ? trim($_POST['slug']) : createSlug($data['title']);

    // Handle link fields based on link_type
    if ($data['link_type'] == 'custom') {
        $data['link'] = trim($_POST['link']);
        $data['link_target_id'] = null; // Clear target ID if custom link
        if (empty($data['link'])) $errors['link'] = "Link là bắt buộc cho URL tùy chỉnh.";
    } else {
        $data['link_target_id'] = filter_var($_POST['link_target_id'], FILTER_VALIDATE_INT);
        $data['link'] = ''; // Clear custom link if not custom type
        if ($data['link_target_id'] === false || $data['link_target_id'] <= 0) $errors['link_target_id'] = "ID đích liên kết là bắt buộc cho loại này.";
    }

    if (empty($data['title'])) $errors['title'] = "Tiêu đề là bắt buộc.";
    if ($data['sort_order'] === false || $data['sort_order'] < 0) $errors['sort_order'] = "Thứ tự không hợp lệ.";

    // Handle file uploads
    $upload_dir = '../../../../public/uploads/icon_menu/';
    
    $image_path = handleUpload('image', $upload_dir);
    if ($image_path) {
        $data['image'] = $image_path;
    } else {
        $errors['image'] = "Vui lòng chọn ảnh icon.";
    }

    if (empty($errors)) {
        if ($iconMenuModel->create($data)) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['message'] = "Thêm mục thành công!";
            $_SESSION['message_type'] = "success";
            header("Location: list_icon_menu.php");
            exit();
        } else {
            $errors['db'] = "Lỗi: Không thể thêm mục. Vui lòng thử lại.";
        }
    }
}

$page_title = "Thêm Icon Menu";
include_once '../templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0"><?php echo $page_title; ?></h2>
        <p class="text-muted small">Thêm icon menu mới vào hệ thống</p>
    </div>
    <a href="list_icon_menu.php" class="btn btn-outline-secondary shadow-sm">
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
                        <label for="title" class="form-label fw-bold">Tiêu đề</label>
                        <input type="text" class="form-control <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>" id="title" name="title" value="<?php echo htmlspecialchars($data['title']); ?>">
                        <?php if (isset($errors['title'])) echo "<div class='invalid-feedback'>{$errors['title']}</div>"; ?>
                    </div>
                     <div class="mb-3">
                        <label for="slug" class="form-label fw-bold">Slug (URL thân thiện)</label>
                        <input type="text" class="form-control" id="slug" name="slug" value="<?php echo htmlspecialchars($data['slug']); ?>" placeholder="Để trống để tự động tạo từ tiêu đề">
                    </div>
                     <div class="mb-3">
                        <label for="link_type" class="form-label fw-bold">Loại liên kết</label>
                        <select class="form-select" id="link_type" name="link_type">
                            <option value="custom" <?php echo ($data['link_type'] == 'custom') ? 'selected' : ''; ?>>URL Tùy chỉnh</option>
                            <option value="category" <?php echo ($data['link_type'] == 'category') ? 'selected' : ''; ?>>Danh mục</option>
                            <option value="product" <?php echo ($data['link_type'] == 'product') ? 'selected' : ''; ?>>Sản phẩm</option>
                            <!-- Add other types if necessary, e.g., 'post', 'page' -->
                        </select>
                    </div>

                    <div class="mb-3" id="link_target_id_group" style="<?php echo ($data['link_type'] == 'custom') ? 'display: none;' : ''; ?>">
                        <label for="link_target_id" class="form-label fw-bold">ID đích liên kết (Category/Product)</label>
                        <select class="form-select" id="link_target_id" name="link_target_id">
                            <option value="">Chọn...</option>
                            <optgroup label="Danh mục">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" data-type="category" <?php echo ($data['link_type'] == 'category' && $data['link_target_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?> (ID: <?php echo $cat['id']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                            <optgroup label="Sản phẩm">
                                <?php foreach ($products as $prod): ?>
                                    <option value="<?php echo $prod['id']; ?>" data-type="product" <?php echo ($data['link_type'] == 'product' && $data['link_target_id'] == $prod['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($prod['name']); ?> (ID: <?php echo $prod['id']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        </select>
                        <div class="form-text">Chọn ID của danh mục hoặc sản phẩm để liên kết.</div>
                    </div>

                     <div class="mb-3" id="link_group" style="<?php echo ($data['link_type'] != 'custom') ? 'display: none;' : ''; ?>">
                        <label for="link" class="form-label fw-bold">Link URL Tùy chỉnh</label>
                        <input type="text" class="form-control" id="link" name="link" value="<?php echo htmlspecialchars($data['link']); ?>">
                        <div class="form-text">Ví dụ: <?php echo BASE_URL; ?>/your-custom-page.php hoặc https://external.com</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="sort_order" class="form-label fw-bold">Thứ tự sắp xếp</label>
                        <input type="number" class="form-control <?php echo isset($errors['sort_order']) ? 'is-invalid' : ''; ?>" id="sort_order" name="sort_order" value="<?php echo htmlspecialchars($data['sort_order']); ?>">
                        <?php if (isset($errors['sort_order'])) echo "<div class='invalid-feedback'>{$errors['sort_order']}</div>"; ?>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label fw-bold">Ảnh Icon</label>
                        <input type="file" class="form-control <?php echo isset($errors['image']) ? 'is-invalid' : ''; ?>" id="image" name="image">
                        <?php if (isset($errors['image'])) echo "<div class='invalid-feedback'>{$errors['image']}</div>"; ?>
                    </div>
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1" <?php echo ($data['status'] == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="status">Kích hoạt</label>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Lưu
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const linkTypeSelect = document.getElementById('link_type');
    const linkTargetIdGroup = document.getElementById('link_target_id_group');
    const linkGroup = document.getElementById('link_group');
    const linkTargetIdSelect = document.getElementById('link_target_id');

    function toggleLinkFields() {
        const selectedLinkType = linkTypeSelect.value;

        if (selectedLinkType === 'custom') {
            linkGroup.style.display = 'block';
            linkTargetIdGroup.style.display = 'none';
        } else {
            linkGroup.style.display = 'none';
            linkTargetIdGroup.style.display = 'block';

            // Filter link_target_id options based on link type
            Array.from(linkTargetIdSelect.options).forEach(option => {
                const optionType = option.dataset.type;
                if (option.value === "") { // "Choose..." option
                    option.style.display = ''; 
                    return;
                }
                if (optionType === selectedLinkType) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });

            // If selected option is not valid for the new type, reset it
            const currentSelectedOption = linkTargetIdSelect.options[linkTargetIdSelect.selectedIndex];
            if (currentSelectedOption && currentSelectedOption.dataset.type !== selectedLinkType && selectedLinkType !== 'custom') {
                linkTargetIdSelect.value = ''; // Reset selection
            }
        }
    }

    linkTypeSelect.addEventListener('change', toggleLinkFields);

    // Initial call to set correct visibility based on initial value
    toggleLinkFields();
});
</script>

<?php
include_once '../templates/footer.php';
?>
