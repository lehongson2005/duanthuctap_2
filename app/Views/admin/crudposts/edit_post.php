<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/PostModel.php';
include_once '../../../../app/models/CategoryModel.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$postModel = new PostModel($conn);
$categoryModel = new CategoryModel($conn);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['message'] = "ID bài viết không hợp lệ!";
    $_SESSION['message_type'] = "danger";
    header("Location: list_posts.php");
    exit();
}

$data = $postModel->getById($id);
if (!$data) {
    $_SESSION['message'] = "Không tìm thấy bài viết!";
    $_SESSION['message_type'] = "danger";
    header("Location: list_posts.php");
    exit();
}
// Format datetime for input field
$data['published_at'] = $data['published_at'] ? date('Y-m-d\TH:i', strtotime($data['published_at'])) : '';


$errors = [];

function handleUpload($file_input_name, $upload_dir) {
    if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] == 0) {
        $filename = uniqid() . '-' . basename($_FILES[$file_input_name]['name']);
        $target_path = $upload_dir . $filename;
        if (move_uploaded_file($_FILES[$file_input_name]['tmp_name'], $target_path)) {
            return 'public/uploads/posts/' . $filename;
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
    $original_data = $data;
    $data = array_merge($data, $_POST);
    
    $data['title'] = trim($_POST['title']);
    $data['slug'] = !empty(trim($_POST['slug'])) ? trim($_POST['slug']) : createSlug($data['title']);
    $data['status'] = isset($_POST['status']) ? (int)$_POST['status'] : 0;
    $data['is_featured'] = isset($_POST['is_featured']) ? 1 : 0;
    $data['is_hot'] = isset($_POST['is_hot']) ? 1 : 0;
    $data['enable_toc'] = isset($_POST['enable_toc']) ? 1 : 0;
    $data['published_at'] = !empty($_POST['published_at']) ? date('Y-m-d H:i:s', strtotime($_POST['published_at'])) : null;
    $data['id'] = $id;

    if (empty($data['title'])) $errors['title'] = "Tiêu đề là bắt buộc.";
    if (empty($data['content'])) $errors['content'] = "Nội dung là bắt buộc.";
    if (empty($data['category_id'])) $errors['category_id'] = "Danh mục là bắt buộc.";

    $upload_dir = '../../../../public/uploads/posts/';
    $new_thumbnail_path = handleUpload('thumbnail', $upload_dir);
    if ($new_thumbnail_path) {
        $data['thumbnail'] = $new_thumbnail_path;
    } else {
        $data['thumbnail'] = $original_data['thumbnail'];
    }

    if (empty($errors)) {
        if ($postModel->update($data)) {
            if ($new_thumbnail_path && $new_thumbnail_path !== $original_data['thumbnail']) {
                $old_image_full_path = '../../../../' . $original_data['thumbnail'];
                if (file_exists($old_image_full_path)) {
                    unlink($old_image_full_path);
                }
            }
            $_SESSION['message'] = "Cập nhật bài viết thành công!";
            $_SESSION['message_type'] = "success";
            header("Location: list_posts.php");
            exit();
        } else {
            $errors['db'] = "Lỗi: Không thể cập nhật bài viết.";
        }
    }
}

$page_title = "Chỉnh sửa Bài viết";
include_once '../templates/header.php';
$all_categories = $categoryModel->getAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0"><?php echo $page_title; ?></h2>
        <p class="text-muted small">Cập nhật bài viết</p>
    </div>
    <a href="list_posts.php" class="btn btn-outline-secondary shadow-sm">
        <i class="fas fa-arrow-left me-2"></i> Quay lại
    </a>
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
                        <label for="title" class="form-label fw-bold">Tiêu đề</label>
                        <input type="text" class="form-control <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>" id="title" name="title" value="<?php echo htmlspecialchars($data['title']); ?>">
                        <?php if (isset($errors['title'])) echo "<div class='invalid-feedback'>{$errors['title']}</div>"; ?>
                    </div>
                    <div class="mb-3">
                        <label for="slug" class="form-label fw-bold">Slug (URL)</label>
                        <input type="text" class="form-control" id="slug" name="slug" value="<?php echo htmlspecialchars($data['slug']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label fw-bold">Nội dung</label>
                        <textarea class="form-control <?php echo isset($errors['content']) ? 'is-invalid' : ''; ?>" id="content" name="content" rows="15"><?php echo htmlspecialchars($data['content']); ?></textarea>
                         <?php if (isset($errors['content'])) echo "<div class='invalid-feedback'>{$errors['content']}</div>"; ?>
                    </div>
                    <div class="mb-3">
                        <label for="excerpt" class="form-label fw-bold">Tóm tắt (Excerpt)</label>
                        <textarea class="form-control" id="excerpt" name="excerpt" rows="3"><?php echo htmlspecialchars($data['excerpt']); ?></textarea>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm border-0 rounded-4 mt-4">
                <div class="card-header bg-light py-3"><h5 class="mb-0">Tối ưu SEO</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="meta_title" class="form-label fw-bold">Meta Title</label>
                        <input type="text" class="form-control" id="meta_title" name="meta_title" value="<?php echo htmlspecialchars($data['meta_title']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="meta_description" class="form-label fw-bold">Meta Description</label>
                        <textarea class="form-control" id="meta_description" name="meta_description" rows="3"><?php echo htmlspecialchars($data['meta_description']); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-light py-3"><h5 class="mb-0">Xuất bản</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="published_at" class="form-label fw-bold">Ngày đăng</label>
                        <input type="datetime-local" class="form-control" id="published_at" name="published_at" value="<?php echo htmlspecialchars($data['published_at']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="author" class="form-label fw-bold">Tác giả</label>
                        <input type="text" class="form-control" id="author" name="author" value="<?php echo htmlspecialchars($data['author']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label fw-bold">Trạng thái</label>
                        <select name="status" id="status" class="form-select">
                            <option value="1" <?php echo ($data['status'] == 1) ? 'selected' : ''; ?>>Published</option>
                            <option value="0" <?php echo ($data['status'] == 0) ? 'selected' : ''; ?>>Draft</option>
                        </select>
                    </div>
                </div>
            </div>

             <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-light py-3"><h5 class="mb-0">Thuộc tính</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-bold">Danh mục</label>
                        <select class="form-select <?php echo isset($errors['category_id']) ? 'is-invalid' : ''; ?>" id="category_id" name="category_id">
                            <option value="">-- Chọn danh mục --</option>
                            <?php mysqli_data_seek($all_categories, 0); ?>
                            <?php while ($cat = $all_categories->fetch_assoc()): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($data['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <?php if (isset($errors['category_id'])) echo "<div class='invalid-feedback'>{$errors['category_id']}</div>"; ?>
                    </div>
                    <div class="mb-3">
                        <label for="tags" class="form-label fw-bold">Tags</label>
                        <input type="text" class="form-control" id="tags" name="tags" value="<?php echo htmlspecialchars($data['tags']); ?>" placeholder="tag 1, tag 2, ...">
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_featured" name="is_featured" value="1" <?php echo ($data['is_featured'] == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_featured">Bài viết nổi bật</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_hot" name="is_hot" value="1" <?php echo ($data['is_hot'] == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_hot">Bài viết hot</label>
                    </div>
                     <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="enable_toc" name="enable_toc" value="1" <?php echo ($data['enable_toc'] == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="enable_toc">Bật mục lục (TOC)</label>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                 <div class="card-header bg-light py-3"><h5 class="mb-0">Ảnh Thumbnail</h5></div>
                <div class="card-body">
                    <input type="file" class="form-control" id="thumbnail" name="thumbnail">
                    <?php if (!empty($data['thumbnail'])): ?>
                        <div class="mt-2">
                            <img src="<?php echo BASE_URL . '/' . htmlspecialchars($data['thumbnail']); ?>" alt="Current Thumbnail" style="width: 100%; height: auto; object-fit: cover; border-radius: 4px;">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-end mt-4">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-save me-2"></i> Cập nhật Bài viết
        </button>
    </div>
</form>

<?php
include_once '../templates/footer.php';
?>
