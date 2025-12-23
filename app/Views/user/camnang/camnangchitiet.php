<?php
// FILE SETUP & DATA FETCHING
include_once __DIR__ . '/../../../config/db.php';
include_once __DIR__ . '/../../../models/CamNangPostModel.php';

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = null;
$related_posts = null;
$page_title = "Không tìm thấy bài viết";

if ($post_id > 0) {
    $postModel = new CamNangPostModel($conn);
    $post = $postModel->getById($post_id);

    if ($post) {
        $page_title = $post['title'];
        $related_posts = $postModel->searchAndFilter('', $post['category_id'], 1, null, 5, null, $post['id']);
    }
}

include_once '../header.php';
?>

<div class="container my-4">
    <?php if ($post): ?>
        <div class="row">
            <div class="col-12 mb-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/index.php" class="text-success">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/app/Views/user/camnang/camnang.php" class="text-success">Cẩm Nang</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($post['title']); ?></li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3 d-none d-lg-block">
                <div class="toc-sidebar">
                    <div class="toc-header"><i class="fas fa-list-ul"></i> Nội dung bài viết</div>
                    <nav>
                        <!-- TOC will be generated here by JS from footer.php -->
                        <p class="small text-muted">Mục lục đang được tải...</p>
                    </nav>
                </div>
            </div>

            <div class="col-lg-6 col-md-8">
                <div class="article-content">
                    <h1 class="article-title"><?php echo htmlspecialchars($post['title']); ?></h1>
                    <p class="article-meta">
                        Tác giả: <span class="fw-bold"><?php echo htmlspecialchars($post['author']); ?></span> | <?php echo date('l d/m/Y', strtotime($post['published_at'])); ?>
                    </p>
                    <?php if(!empty($post['thumbnail'])): ?>
                        <img src="<?php echo BASE_URL . '/' . htmlspecialchars($post['thumbnail']); ?>" class="img-fluid rounded mb-4" alt="<?php echo htmlspecialchars($post['title']); ?>">
                    <?php endif; ?>
                    <div class="post-body">
                        <?php echo $post['content']; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-4">
                <div class="sidebar-right">
                    <div class="sidebar-widget">
                        <h4 class="fw-bold">BÀI VIẾT LIÊN QUAN</h4>
                        <?php if ($related_posts && $related_posts->num_rows > 0): ?>
                            <?php while($related = $related_posts->fetch_assoc()): ?>
                                <a href="camnangchitiet.php?id=<?php echo $related['id']; ?>&slug=<?php echo $related['slug']; ?>" class="text-decoration-none">
                                    <div class="sidebar-item">
                                        <img src="<?php echo BASE_URL . '/' . htmlspecialchars($related['thumbnail']); ?>" alt="<?php echo htmlspecialchars($related['title']); ?>">
                                        <p class="sidebar-item-title mb-0"><?php echo htmlspecialchars($related['title']); ?></p>
                                    </div>
                                </a>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="small text-muted">Không có bài viết liên quan.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger text-center">
            <h2>404 - Not Found</h2>
            <p>Bài viết cẩm nang bạn tìm không tồn tại hoặc đã bị xóa.</p>
            <a href="<?php echo BASE_URL; ?>/app/Views/user/camnang/camnang.php" class="btn btn-success">Quay lại trang cẩm nang</a>
        </div>
    <?php endif; ?>
</div>

<?php include_once '../footer.php'; ?>

</body>
</html>
