<?php
session_start();
// Include the database connection and the CategoryModel
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../models/CategoryModel.php';

// Get the category ID from the URL
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$category = null;
$error_message = '';

if ($category_id > 0) {
    // Create a new CategoryModel instance
    $categoryModel = new CategoryModel($conn);
    // Get the category by its ID
    $category = $categoryModel->getById($category_id);

    if (!$category) {
        // Category not found
        $error_message = 'Không tìm thấy danh mục bạn yêu cầu.';
    }
} else {
    // Invalid ID
    $error_message = 'ID danh mục không hợp lệ.';
}

// Set the page title dynamically
$page_title = $category ? $category['name'] : 'Chi tiết danh mục';

// Include the header
include __DIR__ . '/../header.php';
?>

<div class="container my-5">
    <?php if ($error_message): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
        <a href="<?php echo BASE_URL; ?>" class="btn btn-primary">Quay về trang chủ</a>
    <?php elseif ($category): ?>
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($category['name']); ?></li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h1><?php echo htmlspecialchars($category['name']); ?></h1>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <?php echo nl2br(htmlspecialchars($category['description'])); ?>
                        </p>
                    </div>
                </div>

                <div class="mt-4">
                    <h3>Sản phẩm thuộc danh mục này</h3>
                    <p><em>(Phần này sẽ được phát triển ở giai đoạn sau để hiển thị các sản phẩm thuộc danh mục "<?php echo htmlspecialchars($category['name']); ?>")</em></p>
                    <!-- Product listing would go here -->
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
// Include the footer
include __DIR__ . '/../footer.php';
?>
