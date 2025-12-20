<?php
// Đảm bảo có kết nối DB và Model
// Component này giả định nó được include từ một tệp ở thư mục gốc của dự án (ví dụ: index.php)
if (!isset($conn)) {
    // Nếu không, tự include
    require_once __DIR__ . '/../../../config/db.php';
}
if (!class_exists('IconMenuModel')) {
    require_once __DIR__ . '/../../../models/IconMenuModel.php';
}

// Lấy các mục icon menu đang hoạt động, sắp xếp theo sort_order
$iconMenuModel = new IconMenuModel($conn);
$active_icons = $iconMenuModel->searchAndFilter('', 1); // status = 1 là active
?>
<style>
     /* --- STYLE THANH CUỘN NGANG (ICON MENU) --- */
    .nnp-icon-menu-wrapper {
        display: block; 
        margin: 15px 0;
        padding: 10px 15px;
        background-color: #fff;
        border-top: 1px solid #eee;
        border-bottom: 1px solid #eee;
    }
    .nnp-icon-menu-scroll {
        overflow-x: auto; 
        white-space: nowrap; 
        -webkit-overflow-scrolling: touch;
        padding: 0; 
        min-width: 100%; 
    }
    /* Ẩn thanh scrollbar */
    .nnp-icon-menu-scroll::-webkit-scrollbar {
        display: none;
    }
    .nnp-icon-menu-scroll {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }

    .nnp-icon-menu-item {
        display: inline-flex;
        flex-direction: column; 
        align-items: center;
        justify-content: flex-start;
        width: 80px; 
        text-align: center;
        font-size: 0.75rem;
        color: #333;
        white-space: normal;
        text-decoration: none;
        transition: color 0.2s ease; 
        vertical-align: top;
        margin: 0 5px;
    }
    .nnp-icon-menu-item img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: 1px solid #ddd;
        object-fit: cover;
        margin-bottom: 6px;
        transition: transform 0.2s ease; 
    }
    .nnp-icon-menu-item:hover {
        color: #238E46; 
    }
    .nnp-icon-menu-item:hover img {
        transform: scale(1.05);
    }
</style>
<div class="container">
    <div class="row">
        <section id="icon-menu-section" class="mb-4">
             <div class="nnp-icon-menu-wrapper">
                <div class="nnp-icon-menu-scroll">
                    <?php if ($active_icons && $active_icons->num_rows > 0): ?>
                        <?php while($icon = $active_icons->fetch_assoc()): ?>
                            <a href="<?php echo htmlspecialchars($icon['link']); ?>" class="nnp-icon-menu-item" title="<?php echo htmlspecialchars($icon['title']); ?>">
                                <img src="<?php echo BASE_URL . '/' . htmlspecialchars($icon['image']); ?>" alt="<?php echo htmlspecialchars($icon['title']); ?>">
                                <span><?php echo htmlspecialchars($icon['title']); ?></span>
                            </a>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</div>