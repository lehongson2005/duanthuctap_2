<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/CamNangPostModel.php';
include_once '../../../../app/models/CamNangCategoryModel.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$postModel = new CamNangPostModel($conn);
$categoryModel = new CamNangCategoryModel($conn);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['message'] = "ID không hợp lệ!";
    $_SESSION['message_type'] = "danger";
    header("Location: list_cn_posts.php");
    exit();
}

$data = $postModel->getById($id);
if (!$data) {
    $_SESSION['message'] = "Không tìm thấy bài viết!";
    $_SESSION['message_type'] = "danger";
    header("Location: list_cn_posts.php");
    exit();
}
$data['published_at'] = $data['published_at'] ? date('Y-m-d\TH:i', strtotime($data['published_at'])) : '';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $original_data = $data;
    $data = array_merge($data, $_POST);
    
    $data['title'] = trim($_POST['title']);
    $data['slug'] = !empty(trim($_POST['slug'])) ? trim($_POST['slug']) : createSlug($data['title']);
    $data['status'] = isset($_POST['status']) ? (int)$_POST['status'] : 0;
    $data['is_featured'] = isset($_POST['is_featured']) ? 1 : 0;
    $data['published_at'] = !empty($_POST['published_at']) ? date('Y-m-d H:i:s', strtotime($_POST['published_at'])) : null;
    $data['id'] = $id;

    if (empty($data['title'])) $errors['title'] = "Tiêu đề là bắt buộc.";
    if (empty($data['category_id'])) $errors['category_id'] = "Danh mục là bắt buộc.";

    $upload_dir = '../../../../public/uploads/camnang/';
    
    function handleUpload($file_input_name, $upload_dir) {
        if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] == 0) {
            $filename = uniqid() . '-' . basename($_FILES[$file_input_name]['name']);
            $target_path = $upload_dir . $filename;
            if (move_uploaded_file($_FILES[$file_input_name]['tmp_name'], $target_path)) {
                return 'public/uploads/camnang/' . $filename;
            }
        }
        return null;
    }

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
                if (file_exists($old_image_full_path)) unlink($old_image_full_path);
            }
            $_SESSION['message'] = "Cập nhật bài viết thành công!";
            $_SESSION['message_type'] = "success";
            header("Location: list_cn_posts.php");
            exit();
        } else {
            $errors['db'] = "Lỗi: Không thể cập nhật bài viết.";
        }
    }
}

function createSlug($string) {
    $search = ['#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#', '#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#', '#(ì|í|ị|ỉ|ĩ)#', '#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#', '#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#', '#(ỳ|ý|ỵ|ỷ|ỹ)#', '#(đ)#', '#(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)#', '#(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)#', '#(Ì|Í|Ị|Ỉ|Ĩ)#', '#(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)#', '#(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)#', '#(Ỳ|Ý|Ỵ|Ỷ|Ỹ)#', '#(Đ)#', '/[^a-zA-Z0-9\-\_]/'];
    $replace = ['a', 'e', 'i', 'o', 'u', 'y', 'd', 'A', 'E', 'I', 'O', 'U', 'Y', 'D', '-'];
    $string = preg_replace($search, $replace, $string);
    $string = preg_replace('/(-)+/', '-', $string);
    return strtolower($string);
}

$page_title = "Chỉnh sửa Bài viết Cẩm nang";
include_once '../templates/header.php';
$all_categories = $categoryModel->getAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0"><?php echo $page_title; ?></h2>
    <a href="list_cn_posts.php" class="btn btn-outline-secondary shadow-sm"><i class="fas fa-arrow-left me-2"></i> Quay lại</a>
</div>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
    <div class="row g-4">
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
                        <label for="summary" class="form-label fw-bold">Tóm tắt (Summary)</label>
                        <textarea class="form-control" id="summary" name="summary" rows="3"><?php echo htmlspecialchars($data['summary']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label fw-bold">Nội dung</label>
                        <textarea class="form-control" id="content" name="content" rows="10"></textarea>
                    </div>
                </div>
            </div>
        </div>

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
                        <label for="category_id" class="form-label fw-bold">Danh mục Cẩm nang</label>
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
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_featured" name="is_featured" value="1" <?php echo ($data['is_featured'] == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_featured">Bài viết nổi bật</label>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                 <div class="card-header bg-light py-3"><h5 class="mb-0">Ảnh Thumbnail</h5></div>
                <div class="card-body">
                    <input type="file" class="form-control" id="thumbnail" name="thumbnail">
                     <?php if (!empty($data['thumbnail'])): ?>
                        <div class="mt-2">
                            <img src="<?php echo BASE_URL . '/' . htmlspecialchars($data['thumbnail']); ?>" alt="Current Thumbnail" style="width: 100%; border-radius: 4px;">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-end mt-4">
        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save me-2"></i> Cập nhật</button>
    </div>
</form>

<?php include_once '../templates/footer.php'; ?>

<script>

$(document).ready(function() {

    // Initialize Summernote

    $('#content').summernote({

        placeholder: 'Hãy nhập nội dung và chèn ảnh ở đây...',

        tabsize: 2,

        height: 300,

        toolbar: [

          ['style', ['style']],

          ['font', ['bold', 'underline', 'clear']],

          ['color', ['color']],

          ['para', ['ul', 'ol', 'paragraph']],

          ['table', ['table']],

          ['insert', ['link', 'picture', 'video']],

          ['view', ['fullscreen', 'codeview', 'help']]

        ],

        callbacks: {

            onImageUpload: function(files) {

                var formData = new FormData();

                formData.append('file', files[0]);

                $.ajax({

                    url: '<?php echo rtrim(BASE_URL, '/'); ?>/app/api/upload_post_image.php',

                    type: 'POST',

                    data: formData,

                    contentType: false,

                    processData: false,

                    dataType: 'json',

                    success: function(data) {

                        if (data.url) {

                            $('#content').summernote('insertImage', data.url);

                        } else if (data.error) {

                            alert('Lỗi tải ảnh: ' + data.error);

                        }

                    },

                    error: function() {

                        alert('Đã xảy ra lỗi không xác định khi tải ảnh lên.');

                    }

                });

            }

        }

    });



    // Get post ID from URL

    const urlParams = new URLSearchParams(window.location.search);

    const postId = urlParams.get('id');



    if (postId) {

        // Asynchronously load content from the new API endpoint

        $.getJSON(`<?php echo rtrim(BASE_URL, '/'); ?>/app/api/get_post_content.php?id=${postId}&type=cn_post`)

            .done(function(data) {

                if (data && data.content) {

                    $('#content').summernote('code', data.content);

                }

            })

            .fail(function(jqXHR, textStatus, errorThrown) {

                console.error("Failed to load post content:", textStatus, errorThrown);

                $('#content').summernote('code', '<p style="color: red;">Lỗi: Không thể tải nội dung bài viết.</p>');

            });

    }



    // Sync content back to textarea on form submit

    $('form').on('submit', function() {

        $('#content').val($('#content').summernote('code'));

    });

});

</script>
