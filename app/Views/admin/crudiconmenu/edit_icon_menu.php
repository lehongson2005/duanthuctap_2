<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/IconMenuModel.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$iconMenuModel = new IconMenuModel($conn);

// Get ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['message'] = "ID không hợp lệ!";
    $_SESSION['message_type'] = "danger";
    header("Location: list_icon_menu.php");
    exit();
}

// Fetch existing data
$data = $iconMenuModel->getById($id);
if (!$data) {
    $_SESSION['message'] = "Không tìm thấy mục!";
    $_SESSION['message_type'] = "danger";
    header("Location: list_icon_menu.php");
    exit();
}

$errors = [];

// Function to handle file uploads
function handleUpload($file_input_name, $upload_dir) {
    if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] == 0) {
        $filename = uniqid() . '-' . basename($_FILES[$file_input_name]['name']);
        $target_path = $upload_dir . $filename;
        if (move_uploaded_file($_FILES[$file_input_name]['tmp_name'], $target_path)) {
            return 'public/uploads/icon_menu/' . $filename;
        }
    }
    return null;
}

// Function to create a URL-friendly slug
function createSlug($string) {
    // (Same slug function as in add_icon_menu.php)
    $search = ['#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#', '#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#', '#(ì|í|ị|ỉ|ĩ)#', '#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#', '#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#', '#(ỳ|ý|ỵ|ỷ|ỹ)#', '#(đ)#', '#(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)#', '#(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)#', '#(Ì|Í|Ị|Ỉ|Ĩ)#', '#(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)#', '#(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)#', '#(Ỳ|Ý|Ỵ|Ỷ|Ỹ)#', '#(Đ)#', '/[^a-zA-Z0-9\-\_]/'];
    $replace = ['a', 'e', 'i', 'o', 'u', 'y', 'd', 'A', 'E', 'I', 'O', 'U', 'Y', 'D', '-'];
    $string = preg_replace($search, $replace, $string);
    $string = preg_replace('/(-)+/', '-', $string);
    $string = strtolower($string);
    return $string;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get original data to compare
    $original_data = $data;

    // Sanitize and validate input
    $data['title'] = trim($_POST['title']);
    $data['link'] = trim($_POST['link']);
    $data['sort_order'] = filter_var($_POST['sort_order'], FILTER_VALIDATE_INT);
    $data['status'] = isset($_POST['status']) ? 1 : 0;
    $data['slug'] = !empty($_POST['slug']) ? trim($_POST['slug']) : createSlug($data['title']);
    $data['id'] = $id;

    if (empty($data['title'])) $errors['title'] = "Tiêu đề là bắt buộc.";
    if ($data['sort_order'] === false || $data['sort_order'] < 0) $errors['sort_order'] = "Thứ tự không hợp lệ.";

    // Handle file uploads
    $upload_dir = '../../../../public/uploads/icon_menu/';
    
    $new_image_path = handleUpload('image', $upload_dir);
    if ($new_image_path) {
        // If new image is uploaded, set it and delete the old one later
        $data['image'] = $new_image_path;
    } else {
        // Keep the old image if no new one is uploaded
        $data['image'] = $original_data['image'];
    }

    if (empty($errors)) {
        if ($iconMenuModel->update($data)) {
            // Delete old image if a new one was uploaded and is different
            if ($new_image_path && $new_image_path !== $original_data['image']) {
                 $old_image_full_path = '../../../../' . $original_data['image'];
                 if (file_exists($old_image_full_path)) {
                     unlink($old_image_full_path);
                 }
            }
            $_SESSION['message'] = "Cập nhật mục thành công!";
            $_SESSION['message_type'] = "success";
            header("Location: list_icon_menu.php");
            exit();
        } else {
            $errors['db'] = "Lỗi: Không thể cập nhật mục. Vui lòng thử lại.";
        }
    }
}

$page_title = "Chỉnh sửa Icon Menu";
include_once '../templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0"><?php echo $page_title; ?></h2>
        <p class="text-muted small">Cập nhật thông tin cho icon menu</p>
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

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
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
                        <label for="link" class="form-label fw-bold">Link</label>
                        <input type="text" class="form-control" id="link" name="link" value="<?php echo htmlspecialchars($data['link']); ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="sort_order" class="form-label fw-bold">Thứ tự sắp xếp</label>
                        <input type="number" class="form-control <?php echo isset($errors['sort_order']) ? 'is-invalid' : ''; ?>" id="sort_order" name="sort_order" value="<?php echo htmlspecialchars($data['sort_order']); ?>">
                        <?php if (isset($errors['sort_order'])) echo "<div class='invalid-feedback'>{$errors['sort_order']}</div>"; ?>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label fw-bold">Ảnh Icon (Để trống nếu không muốn thay đổi)</label>
                        <input type="file" class="form-control <?php echo isset($errors['image']) ? 'is-invalid' : ''; ?>" id="image" name="image">
                        <?php if (isset($errors['image'])) echo "<div class='invalid-feedback'>{$errors['image']}</div>"; ?>
                        <?php if (!empty($data['image'])): ?>
                            <div class="mt-2">
                                <img src="<?php echo BASE_URL . '/' . htmlspecialchars($data['image']); ?>" alt="Current Image" style="width: 80px; height: 80px; object-fit: cover;">
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1" <?php echo ($data['status'] == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="status">Kích hoạt</label>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-4">
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
