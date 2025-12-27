<?php
// FILE SETUP & DATA FETCHING
// --------------------------------
// Include dependencies
include_once __DIR__ . '/../../../config/db.php';
include_once __DIR__ . '/../../../models/PostModel.php';

// Get post ID from URL
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = null;
$related_posts = null;
$page_title = "Không tìm thấy bài viết"; // Default title

if ($post_id > 0) {
    // Instantiate model
    $postModel = new PostModel($conn);
    
    // Fetch the main post
    $post = $postModel->getById($post_id);

    if ($post) {
        // If post is found, fetch related posts
        $page_title = $post['title'];
        $related_posts_limit = 5;
        $status = 1; // Published
        $related_posts = $postModel->searchAndFilter(
            '', 
            $post['category_id'], 
            $status, 
            null, 
            null, 
            $related_posts_limit, 
            null, 
            $post['id'] // Exclude current post
        );

        // --- TOC Generation ---
        $toc_html = '<p class="small text-muted">Bài viết này không có mục lục.</p>';
        $modified_content = isset($post['content']) ? $post['content'] : '';

        if (!empty($post['content'])) {
            // Slugify function to create valid IDs
            function slugify_toc($text) {
                $text = preg_replace('~[^\pL\d]+~u', '-', $text);
                $text = iconv('utf-8', 'us-ascii//TRANSLIT//IGNORE', $text);
                $text = preg_replace('~[^-\w]+~', '', $text);
                $text = trim($text, '-');
                $text = preg_replace('~-+~', '-', $text);
                $text = strtolower($text);
                return empty($text) ? 'section' : $text;
            }

            $doc = new DOMDocument();
            libxml_use_internal_errors(true);
            $doc->loadHTML('<?xml encoding="UTF-8">' . $post['content'], LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();

            $xpath = new DOMXPath($doc);
            $headings = $xpath->query('//h2 | //h3');
            $toc_items = [];
            $used_ids = [];

            foreach ($headings as $heading) {
                $text = $heading->textContent;
                $level = (int)substr($heading->tagName, 1);

                $base_id = slugify_toc($text);
                $id = $base_id;
                $counter = 1;
                while (in_array($id, $used_ids)) {
                    $id = $base_id . '-' . $counter++;
                }
                $used_ids[] = $id;

                $heading->setAttribute('id', $id);
                $toc_items[] = ['level' => $level, 'id' => $id, 'text' => $text];
            }

            if (!empty($toc_items)) {
                $toc_html = '<ul class="nav flex-column">';
                foreach ($toc_items as $item) {
                    $class = 'nav-link toc-link text-dark';
                    if ($item['level'] == 3) {
                        $class .= ' ps-4';
                    }
                    $toc_html .= '<li class="nav-item"><a class="' . $class . '" href="#' . $item['id'] . '">' . htmlspecialchars($item['text']) . '</a></li>';
                }
                $toc_html .= '</ul>';
                
                $modified_content = $doc->saveHTML();
            }
        }
        // --- End TOC Generation ---
    }
}

// Include header - This file opens <html>, <head>, <body>
include_once '../header.php';
?>

<div class="container my-4">
    <?php if ($post): ?>
    <div class="row">
        <div class="col-12 mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/index.php" class="text-success">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/app/Views/user/tintuc_sukien/tintuc.php" class="text-success">Tin Tức</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($post['title']); ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <?php include_once '../trangchu/icon.php'; ?>
    </div>
    
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            <div class="toc-sidebar">
                <div class="toc-header"><i class="fas fa-list-ul"></i> Nội dung bài viết</div>
                <nav id="toc-nav">
                    <?php echo $toc_html; ?>
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
                    <div id="postContentWrapper" class="content-truncated">
                        <?php echo $modified_content; ?>
                    </div>
                    <button id="toggleContentBtn" class="btn btn-link text-success d-none">Xem thêm <i class="fas fa-chevron-down"></i></button>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const postContentWrapper = document.getElementById('postContentWrapper');
                const toggleContentBtn = document.getElementById('toggleContentBtn');

                // Check if content overflows and display button if it does
                if (postContentWrapper.scrollHeight > postContentWrapper.clientHeight) {
                    toggleContentBtn.classList.remove('d-none');
                }

                toggleContentBtn.addEventListener('click', function() {
                    postContentWrapper.classList.toggle('content-truncated');
                    postContentWrapper.classList.toggle('content-expanded');

                    if (postContentWrapper.classList.contains('content-expanded')) {
                        toggleContentBtn.innerHTML = 'Thu gọn <i class="fas fa-chevron-up"></i>';
                    } else {
                        toggleContentBtn.innerHTML = 'Xem thêm <i class="fas fa-chevron-down"></i>';
                    }
                });
            });
        </script>

        <div class="col-lg-3 col-md-4">
            <div class="sidebar-right">
                <div class="sidebar-widget">
                    <h4 class="fw-bold">BÀI VIẾT LIÊN QUAN</h4>
                    <?php if ($related_posts && $related_posts->num_rows > 0): ?>
                        <?php while($related = $related_posts->fetch_assoc()): ?>
                            <a href="chitiettintuc.php?id=<?php echo $related['id']; ?>&slug=<?php echo $related['slug']; ?>" class="text-decoration-none">
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
            <p>Bài viết bạn đang tìm kiếm không tồn tại hoặc đã bị xóa.</p>
            <a href="<?php echo BASE_URL; ?>/app/Views/user/tintuc_sukien/tintuc.php" class="btn btn-success">Quay lại trang tin tức</a>
        </div>
    <?php endif; ?>
</div>

<?php include_once '../footer.php'; ?>

</body>
</html>
