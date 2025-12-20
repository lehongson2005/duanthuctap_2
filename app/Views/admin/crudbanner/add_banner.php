<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/BannerModel.php';

$bannerModel = new BannerModel($conn);

$errors = [];
$data = [
    'title' => '',
    'image' => '',
    'link' => '#',
    'position' => 'main-slider',
    'sort_order' => 0,
    'status' => 1,
    'description' => '',
    'start_date' => '',
    'end_date' => '',
    'target' => '_blank',
    'device' => 'all',
];

// Function to handle file uploads
function handleUpload($file_input_name, $upload_dir) {
    if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] == 0) {
        $filename = uniqid() . '-' . basename($_FILES[$file_input_name]['name']);
        $target_path = $upload_dir . $filename;
        if (move_uploaded_file($_FILES[$file_input_name]['tmp_name'], $target_path)) {
            // Return web-accessible path
            return 'public/uploads/banners/' . $filename;
        }
    }
    return null;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
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

    if (empty($data['title'])) $errors['title'] = "Tiêu đề là bắt buộc.";
    if (empty($data['position'])) $errors['position'] = "Vị trí là bắt buộc.";


    // Handle file uploads
    $upload_dir = '../../../../public/uploads/banners/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $image_path = handleUpload('image', $upload_dir);
    if ($image_path) {
        $data['image'] = $image_path;
    } else {
        $errors['image'] = "Vui lòng chọn ảnh banner.";
    }

    if (empty($errors)) {
        if ($bannerModel->create($data)) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['message'] = "Thêm banner thành công!";
            $_SESSION['message_type'] = "success";
            header("Location: list_banner.php");
            exit();
        } else {
            $errors['db'] = "Lỗi: Không thể thêm banner. Vui lòng thử lại.";
        }
    }
}

$page_title = "Thêm Banner";
include_once '../templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0"><?php echo $page_title; ?></h2>
        <p class="text-muted small">Thêm banner mới vào hệ thống</p>
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

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
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
                        <label for="image" class="form-label fw-bold">Ảnh Banner</label>
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
                    <i class="fas fa-save me-2"></i> Lưu Banner
                </button>
            </div>
        </form>
    </div>
</div>

<?php
include_once '../templates/footer.php';
?>
