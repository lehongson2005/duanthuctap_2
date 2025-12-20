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
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($post['summary'] ?? ''); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root { --primary-color: #238E46; --text-dark: #333; --border-light: #eee; }
        body { background-color: #f8f9fa; }
        .container { max-width: 1200px; }
        .breadcrumb-item a { color: var(--primary-color) !important; }
        .article-title { color: var(--primary-color); font-weight: 700; margin-bottom: 5px; }
        .article-meta { font-size: 0.85rem; color: #6c757d; margin-bottom: 20px; border-bottom: 1px solid var(--border-light); padding-bottom: 10px; }
        .toc-sidebar { position: sticky; top: 20px; padding: 20px; border: 1px solid var(--border-light); border-radius: 8px; background-color: white; margin-bottom: 20px; }
        .toc-header { color: var(--primary-color); font-weight: bold; border-bottom: 2px solid var(--border-light); padding-bottom: 8px; margin-bottom: 10px; }
        .article-content { background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .article-content h2, .article-content h3 { color: var(--primary-color); margin-top: 25px; padding-bottom: 5px; border-bottom: 1px dashed var(--border-light); }
        .article-content p { line-height: 1.8; color: var(--text-dark); margin-bottom: 15px; }
        .sidebar-right { position: sticky; top: 20px; }
        .sidebar-widget { margin-bottom: 30px; border: 1px solid var(--border-light); border-radius: 8px; background-color: white; overflow: hidden; padding: 15px; }
        .sidebar-widget h4 { font-size: 1.15rem; color: var(--primary-color); border-left: 5px solid var(--primary-color); padding-left: 10px; margin-bottom: 15px !important; }
        .sidebar-item { display: flex; margin-bottom: 15px; align-items: center; }
        .sidebar-item img { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; margin-right: 10px; }
        .sidebar-item-title { font-size: 0.85rem; line-height: 1.4; font-weight: 500; color: var(--text-dark); }
        .sidebar-item a:hover .sidebar-item-title { color: var(--primary-color); }
    </style>
</head>
<body>

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
                        <p class="small text-muted">Mục lục sẽ sớm được cập nhật.</p>
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
                        <?php echo nl2br($post['content']); ?>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>