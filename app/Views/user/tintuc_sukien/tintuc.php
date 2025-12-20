<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tất cả bài viết</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { --green: #238E46; --border: #eee; }
        body { background: #f8f9fa; }
        .blog-card { background: #fff; border: 1px solid var(--border); border-radius: 10px; overflow: hidden; transition: .3s; height: 100%; }
        .blog-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,.15); transform: translateY(-4px); }
        .blog-card img { width: 100%; height: 220px; object-fit: cover; }
        .blog-card .blog-body h5 { color: var(--green); font-weight: 700; font-size: 1.1rem; }
        .blog-card .blog-body p { font-size: .95rem; }
        .blog-card .read-more { color: var(--green); font-weight: 700; font-size: .9rem; }
        .sidebar { background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 20px; }
        .sidebar h4 { border-left: 5px solid var(--green); padding-left: 10px; font-size: 1.1rem; margin-bottom: 20px; }
        .sidebar-item { display: flex; gap: 15px; margin-bottom: 15px; align-items: center; }
        .sidebar-item img { width: 70px; height: 70px; object-fit: cover; border-radius: 6px; flex-shrink: 0; }
        .sidebar-item p { font-size: .9rem; margin: 0; font-weight: 600; }
        .sidebar-item a { color: #333; text-decoration: none; }
        .sidebar-item a:hover { color: var(--green); }
    </style>
</head>
<body>

<?php
// FILE SETUP & DATA FETCHING
// --------------------------------
// Include dependencies
include_once __DIR__ . '/../../../config/db.php';
include_once __DIR__ . '/../../../models/PostModel.php';

// Instantiate model
$postModel = new PostModel($conn);

// --- Main Content: All Posts (Paginated) ---
$limit = 9; // 9 posts for a 3-column layout
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$status = 1; // Only show published posts

// Get total posts for pagination
$total_records = $postModel->getTotal('', '', $status);
$total_pages = ceil($total_records / $limit);

// Get posts for the current page
$posts = $postModel->searchAndFilter('', '', $status, null, null, $limit, $offset);


// --- Sidebar: Featured Posts ---
$featured_posts_limit = 4;
$featured_posts = $postModel->searchAndFilter('', '', $status, 1, null, $featured_posts_limit); // is_featured = 1

// Include header
include '../header.php';
?>

<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/index.php" class="text-success">Trang chủ</a></li>
            <li class="breadcrumb-item active">Tất cả bài viết</li>
        </ol>
    </nav>

    <h1 class="fw-bold mb-4">TẤT CẢ BÀI VIẾT</h1>

    <div class="row g-4">
        <!-- DANH SÁCH BÀI VIẾT -->
        <div class="col-lg-9">
            <div class="row g-4">
                <?php if ($posts && $posts->num_rows > 0): ?>
                    <?php while($post = $posts->fetch_assoc()): ?>
                        <div class="col-lg-4 col-md-6">
                            <a href="chitiettintuc.php?id=<?php echo $post['id']; ?>&slug=<?php echo $post['slug']; ?>" class="text-decoration-none text-dark">
                                <div class="blog-card">
                                    <div class="position-relative">
                                        <img src="<?php echo BASE_URL . '/' . htmlspecialchars($post['thumbnail']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                                    </div>
                                    <div class="blog-body p-3">
                                        <h5><?php echo htmlspecialchars($post['title']); ?></h5>
                                        <p class="text-muted small"><?php echo date('d/m/Y', strtotime($post['published_at'])); ?></p>
                                        <p><?php echo htmlspecialchars($post['excerpt']); ?></p>
                                        <span class="read-more">Đọc tiếp →</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <p class="text-center text-muted">Chưa có bài viết nào.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- PAGINATION -->
            <?php if($total_pages > 1): ?>
            <nav aria-label="Page navigation" class="mt-5">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a></li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>"><a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>

        </div>

        <!-- SIDEBAR -->
        <div class="col-lg-3 d-none d-lg-block">
            <div class="sidebar">
                <h4>BÀI VIẾT NỔI BẬT</h4>
                <?php if ($featured_posts && $featured_posts->num_rows > 0): ?>
                    <?php while($featured = $featured_posts->fetch_assoc()): ?>
                        <a href="chitiettintuc.php?id=<?php echo $featured['id']; ?>&slug=<?php echo $featured['slug']; ?>" class="sidebar-item">
                            <img src="<?php echo BASE_URL . '/' . htmlspecialchars($featured['thumbnail']); ?>" alt="<?php echo htmlspecialchars($featured['title']); ?>">
                            <p><?php echo htmlspecialchars($featured['title']); ?></p>
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
