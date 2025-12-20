<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/CamNangCategoryModel.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$cnCategoryModel = new CamNangCategoryModel($conn);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['message'] = "ID không hợp lệ!";
    $_SESSION['message_type'] = "danger";
    header("Location: list_cn_categories.php");
    exit();
}

$data = $cnCategoryModel->getById($id);
if (!$data) {
    $_SESSION['message'] = "Không tìm thấy danh mục!";
    $_SESSION['message_type'] = "danger";
    header("Location: list_cn_categories.php");
    exit();
}

$errors = [];

function createSlug($string) {
    $search = ['#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#', '#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#', '#(ì|í|ị|ỉ|ĩ)#', '#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#', '#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#', '#(ỳ|ý|ỵ|ỷ|ỹ)#', '#(đ)#', '#(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)#', '#(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)#', '#(Ì|Í|Ị|Ỉ|Ĩ)#', '#(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)#', '#(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)#', '#(Ỳ|Ý|Ỵ|Ỷ|Ỹ)#', '#(Đ)#', '/[^a-zA-Z0-9\-\_]/'];
    $replace = ['a', 'e', 'i', 'o', 'u', 'y', 'd', 'A', 'E', 'I', 'O', 'U', 'Y', 'D', '-'];
    $string = preg_replace($search, $replace, $string);
    $string = preg_replace('/(-)+/', '-', $string);
    return strtolower($string);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data['name'] = trim($_POST['name']);
    $data['slug'] = !empty(trim($_POST['slug'])) ? trim($_POST['slug']) : createSlug($data['name']);
    $data['status'] = isset($_POST['status']) ? 1 : 0;
    $data['id'] = $id;

    if (empty($data['name'])) $errors['name'] = "Tên danh mục là bắt buộc.";

    if (empty($errors)) {
        if ($cnCategoryModel->update($data)) {
            $_SESSION['message'] = "Cập nhật danh mục thành công!";
            $_SESSION['message_type'] = "success";
            header("Location: list_cn_categories.php");
            exit();
        } else {
            $errors['db'] = "Lỗi: Không thể cập nhật danh mục.";
        }
    }
}

$page_title = "Chỉnh sửa Danh mục Cẩm nang";
include_once '../templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0"><?php echo $page_title; ?></h2>
    <a href="list_cn_categories.php" class="btn btn-outline-secondary shadow-sm">
        <i class="fas fa-arrow-left me-2"></i> Quay lại
    </a>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
        <?php if (!empty($errors['db'])): ?>
            <div class="alert alert-danger"><?php echo $errors['db']; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?id=<?php echo $id; ?>" method="post">
            <div class="mb-3">
                <label for="name" class="form-label fw-bold">Tên Danh mục</label>
                <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo htmlspecialchars($data['name']); ?>">
                <?php if (isset($errors['name'])) echo "<div class='invalid-feedback'>{$errors['name']}</div>"; ?>
            </div>
             <div class="mb-3">
                <label for="slug" class="form-label fw-bold">Slug (URL)</label>
                <input type="text" class="form-control" id="slug" name="slug" value="<?php echo htmlspecialchars($data['slug']); ?>" placeholder="Để trống để tự tạo từ tên">
            </div>
            <div class="mb-3 form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1" <?php echo ($data['status'] == 1) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="status">Kích hoạt</label>
            </div>
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i> Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<?php include_once '../templates/footer.php'; ?>
