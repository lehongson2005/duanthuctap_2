<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/BannerModel.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$bannerModel = new BannerModel($conn);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['message'] = "ID banner không hợp lệ!";
    $_SESSION['message_type'] = "danger";
    header("Location: list_banner.php");
    exit();
}

$data = $bannerModel->getById($id);
if (!$data) {
    $_SESSION['message'] = "Không tìm thấy banner!";
    $_SESSION['message_type'] = "danger";
    header("Location: list_banner.php");
    exit();
}

$errors = [];

function handleUpload($file_input_name, $upload_dir) {
    if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] == 0) {
        $filename = uniqid() . '-' . basename($_FILES[$file_input_name]['name']);
        $target_path = $upload_dir . $filename;
        if (move_uploaded_file($_FILES[$file_input_name]['tmp_name'], $target_path)) {
            return 'public/uploads/banners/' . $filename;
        }
    }
    return null;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $original_data = $data;

    $data['title'] = trim($_POST['title']);
    $data['link'] = trim($_POST['link']);
    $data['position'] = trim($_POST['position']);
    $data['sort_order'] = filter_var($_POST['sort_order'], FILTER_VALIDATE_INT, ['options' => ['default' => 0]]);
    $data['status'] = isset($_POST['status']) ? 1 : 0;
    $data['description'] = trim($_POST['description']);
    $data['start_date'] = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
    $data['end_date'] = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    $data['target'] = trim($_POST['target']);
    $data['device'] = trim($_POST['device']);
    $data['id'] = $id;

    if (empty($data['title'])) $errors['title'] = "Tiêu đề là bắt buộc.";
    if (empty($data['position'])) $errors['position'] = "Vị trí là bắt buộc.";

    $upload_dir = '../../../../public/uploads/banners/';
    $new_image_path = handleUpload('image', $upload_dir);
    if ($new_image_path) {
        $data['image'] = $new_image_path;
    } else {
        $data['image'] = $original_data['image'];
    }

    if (empty($errors)) {
        if ($bannerModel->update($data)) {
            if ($new_image_path && $new_image_path !== $original_data['image']) {
                 $old_image_full_path = '../../../../' . $original_data['image'];
                 if (file_exists($old_image_full_path)) {
                     unlink($old_image_full_path);
                 }
            }
            $_SESSION['message'] = "Cập nhật banner thành công!";
            $_SESSION['message_type'] = "success";
            header("Location: list_banner.php");
            exit();
        } else {
            $errors['db'] = "Lỗi: Không thể cập nhật banner. Vui lòng thử lại.";
        }
    }
}

$page_title = "Chỉnh sửa Banner";
include_once '../templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0"><?php echo $page_title; ?></h2>
        <p class="text-muted small">Cập nhật thông tin cho banner</p>
    </div>
    <a href="list_banner.php" class="btn btn-outline-secondary shadow-sm">
        <i class="fas fa-arrow-left me-2"></i> Quay lại
    </a>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
        <?php if (!empty($errors['db'])): ?>
            <div class="alert alert-danger"><?php echo $errors['db']; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
            <div class="row g-4">
                <!-- Left Column -->
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold">Tiêu đề</label>
                        <input type="text" class="form-control <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>" id="title" name="title" value="<?php echo htmlspecialchars($data['title']); ?>">
                        <?php if (isset($errors['title'])) echo "<div class='invalid-feedback'>{$errors['title']}</div>"; ?>
                    </div>

                    <div class="mb-3">
                        <label for="link" class="form-label fw-bold">Link</label>
                        <input type="text" class="form-control" id="link" name="link" value="<?php echo htmlspecialchars($data['link']); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Mô tả</label>
                        <textarea class="form-control" id="description" name="description" rows="5"><?php echo htmlspecialchars($data['description']); ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                             <label for="start_date" class="form-label fw-bold">Ngày bắt đầu</label>
                             <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($data['start_date']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label fw-bold">Ngày kết thúc</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($data['end_date']); ?>">
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-4">
                     <div class="mb-3">
                        <label for="position" class="form-label fw-bold">Vị trí</label>
                        <select class="form-select <?php echo isset($errors['position']) ? 'is-invalid' : ''; ?>" id="position" name="position">
                            <option value="main-slider" <?php echo ($data['position'] == 'main-slider') ? 'selected' : ''; ?>>Main Slider</option>
                            <option value="small-banners" <?php echo ($data['position'] == 'small-banners') ? 'selected' : ''; ?>>Small Banners</option>
                            <option value="hot-banner" <?php echo ($data['position'] == 'hot-banner') ? 'selected' : ''; ?>>Hot Banner</option>
                            <option value="custom" <?php echo ($data['position'] == 'custom') ? 'selected' : ''; ?>>Custom</option>
                        </select>
                        <?php if (isset($errors['position'])) echo "<div class='invalid-feedback'>{$errors['position']}</div>"; ?>
                    </div>
                    <div class="mb-3">
                        <label for="sort_order" class="form-label fw-bold">Thứ tự</label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order" value="<?php echo htmlspecialchars($data['sort_order']); ?>">
                    </div>
                     <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="target" class="form-label fw-bold">Target</label>
                             <select name="target" id="target" class="form-select">
                                <option value="_self" <?php echo ($data['target'] == '_self') ? 'selected' : ''; ?>>_self</option>
                                <option value="_blank" <?php echo ($data['target'] == '_blank') ? 'selected' : ''; ?>>_blank</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="device" class="form-label fw-bold">Thiết bị</label>
                             <select name="device" id="device" class="form-select">
                                <option value="all" <?php echo ($data['device'] == 'all') ? 'selected' : ''; ?>>All</option>
                                <option value="desktop" <?php echo ($data['device'] == 'desktop') ? 'selected' : ''; ?>>Desktop</option>
                                <option value="mobile" <?php echo ($data['device'] == 'mobile') ? 'selected' : ''; ?>>Mobile</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label fw-bold">Ảnh Banner (Để trống nếu không đổi)</label>
                        <input type="file" class="form-control" id="image" name="image">
                         <?php if (!empty($data['image'])): ?>
                            <div class="mt-2">
                                <img src="<?php echo BASE_URL . '/' . htmlspecialchars($data['image']); ?>" alt="Current Image" style="width: 100%; height: auto; object-fit: cover; border-radius: 4px;">
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
                    <i class="fas fa-save me-2"></i> Cập nhật Banner
                </button>
            </div>
        </form>
    </div>
</div>

<?php
include_once '../templates/footer.php';
?>
