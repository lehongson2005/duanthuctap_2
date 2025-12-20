<?php
// FILE SETUP & DATA FETCHING
// --------------------------------
// Include dependencies
// This component assumes it's included from a file in the project root (e.g., index.php)
if (!isset($conn)) {
    require_once __DIR__ . '/../../../config/db.php';
}
if (!class_exists('PostModel')) {
    require_once __DIR__ . '/../../../models/PostModel.php';
}

// Instantiate model
$postModel = new PostModel($conn);

// Fetch a limited number of latest published posts for "Kinh nghiệm hay"
$limit = 4; // Display 4 posts
$status = 1; // Only show published posts
$experience_posts = $postModel->searchAndFilter('', '', $status, null, null, $limit);
?>
<style>
    /* CSS for Card Bài Viết */
    .blog-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #eee;
        border-radius: 8px;
        overflow: hidden;
        height: 100%;
        cursor: pointer;
    }
    .blog-card:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transform: translateY(0);
    }

    .blog-card-img-wrapper {
        position: relative;
        overflow: hidden;
        height: 150px;
    }

    .blog-card-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .blog-card:hover .blog-card-img-wrapper img {
        transform: scale(1.05);
    }

    .blog-tag {
        position: absolute;
        bottom: 10px;
        left: 10px;
        background-color: #238E46;
        color: white;
        padding: 4px 8px;
        font-size: 0.7rem;
        font-weight: bold;
        border-radius: 3px;
        z-index: 5;
    }
    
    .blog-meta-time {
        font-size: 0.8rem;
        color: #888;
        margin-bottom: 8px;
    }
</style>

<section id="kinh-nghiem-hay-section" class="mt-4 mb-5">
    <h3 class="fw-bold mb-3">KINH NGHIỆM HAY</h3>
    
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-3">
        <?php if ($experience_posts && $experience_posts->num_rows > 0): ?>
            <?php while($post = $experience_posts->fetch_assoc()): ?>
                <div class="col">
                    <a href="<?php echo BASE_URL; ?>/app/Views/user/tintuc_sukien/chitiettintuc.php?id=<?php echo $post['id']; ?>&slug=<?php echo $post['slug']; ?>" class="text-decoration-none text-dark">
                        <div class="blog-card shadow-sm">
                            <div class="blog-card-img-wrapper">
                                <img src="<?php echo BASE_URL . '/' . htmlspecialchars($post['thumbnail']); ?>" 
                                     class="img-fluid" alt="<?php echo htmlspecialchars($post['title']); ?>">
                                <?php if (!empty($post['category_name'])): ?>
                                    <span class="blog-tag"><?php echo htmlspecialchars(mb_strtoupper($post['category_name'])); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="p-3">
                                <h5 class="fw-bold fs-6"><?php echo htmlspecialchars($post['title']); ?></h5>
                                <p class="blog-meta-time"><?php echo date('d/m/Y', strtotime($post['published_at'])); ?></p>
                                <p class="small text-muted mb-0"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                                <span class="text-success small fw-bold mt-2 d-inline-block">Đọc tiếp <i class="fas fa-arrow-right"></i></span>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-center text-muted">Không có bài viết kinh nghiệm nào để hiển thị.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="text-center mt-4">
        <a href="<?php echo BASE_URL; ?>/app/Views/user/tintuc_sukien/tintuc.php" class="btn btn-outline-secondary">Xem tất cả <i class="fas fa-chevron-right"></i></a>
    </div>
</section>