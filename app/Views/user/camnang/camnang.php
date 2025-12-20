<?php
// FILE SETUP & DATA FETCHING
include_once __DIR__ . '/../../../config/db.php';
include_once __DIR__ . '/../../../models/CamNangPostModel.php';
include_once __DIR__ . '/../../../models/CamNangCategoryModel.php';

$postModel = new CamNangPostModel($conn);
$categoryModel = new CamNangCategoryModel($conn);

// --- Main Content: All Posts (Paginated) ---
$limit = 9; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$status = 1; // Only show published posts

// Get total records for pagination
$total_records = $postModel->getTotal('', '', $status); 
$total_pages = ceil($total_records / $limit);
$posts = $postModel->searchAndFilter('', '', $status, null, $limit, $offset); 

// --- Sidebar: Featured Posts ---
$featured_posts_limit = 5;
$featured_posts = $postModel->searchAndFilter('', '', $status, 1, $featured_posts_limit); 

include '../header.php'; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cẩm Nang Thi Công Cảnh Quan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { --primary-color: #238E46; --text-dark: #333; --text-muted: #6c757d; --border-light: #eee; }
        body { font-family: 'Arial', sans-serif; background-color: #f8f9fa; }
        .container { max-width: 1200px; }
        .breadcrumb-item a { color: var(--primary-color) !important; }
        h1.fw-bold { color: var(--text-dark); border-bottom: 3px solid var(--primary-color); display: inline-block; padding-bottom: 5px; margin-bottom: 20px !important; }
        .main-content-area { padding-right: 25px; }
        .blog-grid-card { border: 1px solid var(--border-light); border-radius: 8px; background-color: white; overflow: hidden; transition: box-shadow 0.3s ease, transform 0.3s ease; height: 100%; }
        .blog-grid-card:hover { box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15); transform: translateY(-2px); }
        .blog-grid-card img { height: 180px; object-fit: cover; width: 100%; display: block; }
        .blog-grid-card .card-body { display: flex; flex-direction: column; }
        .blog-grid-card h5.card-title-grid { color: var(--primary-color); font-weight: 700; font-size: 1.1rem; margin-bottom: 8px; min-height: 50px; }
        .blog-grid-card .card-text-summary { font-size: 0.9rem; color: var(--text-muted); line-height: 1.4; margin-bottom: 10px; flex-grow: 1; }
        .blog-grid-card .text-success { color: var(--primary-color) !important; }
        .blog-tag-overlay-small { position: absolute; top: 10px; left: 10px; background-color: rgba(35, 142, 70, 0.9); color: white; padding: 4px 10px; font-size: 0.75rem; font-weight: bold; border-radius: 4px; }
        .sidebar-widget { margin-bottom: 30px; border: 1px solid var(--border-light); border-radius: 8px; background-color: white; overflow: hidden; padding: 20px; }
        .sidebar-widget h4 { color: var(--text-dark); font-size: 1.15rem; border-left: 5px solid var(--primary-color); padding-left: 10px; margin-bottom: 20px !important; }
        .sidebar-item { display: flex; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px dashed var(--border-light); align-items: center; }
        .sidebar-item:last-child { margin-bottom: 0; padding-bottom: 0; border-bottom: none; }
        .sidebar-item img { min-width: 65px; height: 65px; object-fit: cover; border-radius: 6px; margin-right: 12px; }
        .sidebar-item-title { font-size: 0.9rem; line-height: 1.4; font-weight: 500; color: var(--text-dark); transition: color 0.2s; }
        .sidebar-item a:hover .sidebar-item-title { color: var(--primary-color); }
    </style>
</head>
<body>

<div class="container my-4">
    <div class="row">
        <div class="col-12 mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/index.php" class="text-success">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Cẩm Nang</li>
                </ol>
            </nav>
            <h1 class="fw-bold mb-4">CẨM NANG</h1>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-9 main-content-area">
            <div class="row">
                <?php if ($posts && $posts->num_rows > 0): ?>
                    <?php while($post = $posts->fetch_assoc()): ?>
                        <div class="col-md-4 mb-4">
                            <a href="camnangchitiet.php?id=<?php echo $post['id']; ?>&slug=<?php echo $post['slug']; ?>" class="text-decoration-none text-dark">
                                <div class="blog-grid-card">
                                    <div class="position-relative">
                                        <img src="<?php echo BASE_URL . '/' . htmlspecialchars($post['thumbnail']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($post['title']); ?>">
                                        <?php if(!empty($post['category_name'])): ?>
                                        <span class="blog-tag-overlay-small"><?php echo htmlspecialchars($post['category_name']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body p-3">
                                        <h5 class="card-title-grid"><?php echo htmlspecialchars($post['title']); ?></h5>
                                        <p class="card-text small text-muted"><?php echo date('d/m/Y', strtotime($post['published_at'])); ?></p>
                                        <p class="card-text-summary"><?php echo htmlspecialchars($post['summary']); ?></p>
                                        <span class="text-success small fw-bold">Đọc tiếp <i class="fas fa-arrow-right"></i></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-center text-muted">Chưa có bài viết cẩm nang nào.</p>
                <?php endif; ?>
            </div>
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a></li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php if ($i == $page) echo 'active'; ?>"><a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
        
        <div class="col-lg-3 d-none d-lg-block">
            <div class="sidebar-widget">
                <h4 class="fw-bold mb-3">BÀI VIẾT NỔI BẬT</h4>
                <?php if ($featured_posts && $featured_posts->num_rows > 0): ?>
                    <?php while($featured = $featured_posts->fetch_assoc()): ?>
                        <a href="camnangchitiet.php?id=<?php echo $featured['id']; ?>&slug=<?php echo $featured['slug']; ?>" class="text-decoration-none">
                            <div class="sidebar-item">
                                <img src="<?php echo BASE_URL . '/' . htmlspecialchars($featured['thumbnail']); ?>" alt="<?php echo htmlspecialchars($featured['title']); ?>">
                                <p class="sidebar-item-title mb-0"><?php echo htmlspecialchars($featured['title']); ?></p>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="small text-muted">Không có bài viết nổi bật.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>