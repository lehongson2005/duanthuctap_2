<?php
date_default_timezone_set('Asia/Ho_Chi_Minh'); // Set timezone early

$db_path = realpath(__DIR__ . '/app/config/db.php'); // Corrected path to db.php
if ($db_path !== false) {
    include_once $db_path;
} else {
    die("Error: Could not find database configuration file.");
}

// Include BannerModel
include_once __DIR__ . '/app/models/BannerModel.php';
$bannerModel = new BannerModel($conn);

// Fetch banners for different positions
$main_banners = $bannerModel->searchAndFilter('', 'main-slider', 1); // Position 'main-slider', status active (1)
$small_banners = $bannerModel->searchAndFilter('', 'small-banners', 1); // Position 'small-banners', status active (1)
$hot_banners = $bannerModel->searchAndFilter('', 'hot-banner', 1); // Position 'hot-banner', status active (1)

// Include Category and Product Models
include_once __DIR__ . '/app/models/CategoryModel.php';
include_once __DIR__ . '/app/models/CategoryLevel2Model.php';
include_once __DIR__ . '/app/models/CategoryLevel3Model.php';
include_once __DIR__ . '/app/models/ProductModel.php';

// Instantiate models
$categoryModel = new CategoryModel($conn);
$categoryLevel2Model = new CategoryLevel2Model($conn);
$categoryLevel3Model = new CategoryLevel3Model($conn);
$productModel = new ProductModel($conn);

// --- Fetch products for "Kích Rễ" section by keyword search ---
$kichre_products = $productModel->searchAndFilter('Kích rễ', '', '', '', '1', '', 8, null);
$kichre_view_all_url = BASE_URL . '/timkiem.php?keyword=' . urlencode('Kích rễ');


// --- Fetch products for "Phân Bón" category ---
$phanbon_products = [];
$phanbon_view_all_id = null;
$phanBonCategory = $categoryModel->getByName('Phân Bón');
if ($phanBonCategory) {
    $phanbon_view_all_id = $phanBonCategory['id'];
    $phanbon_products = $productModel->searchAndFilter('', $phanbon_view_all_id, '', '', '1', '', null, null);
} else {
    $phanBonCategoryL2 = $categoryLevel2Model->getByName('Phân Bón');
    if ($phanBonCategoryL2) {
        $phanbon_view_all_id = $phanBonCategoryL2['category_id']; // Get parent L1 ID
        $phanbon_products = $productModel->searchAndFilter('', '', $phanBonCategoryL2['id'], '', '1', '', null, null);
    } else {
        $phanBonCategoryL3 = $categoryLevel3Model->getByName('Phân Bón');
        if ($phanBonCategoryL3) {
            $l3_parent_l2 = $categoryLevel2Model->getById($phanBonCategoryL3['category_level2_id']);
            if($l3_parent_l2) $phanbon_view_all_id = $l3_parent_l2['category_id']; // Get parent L1 ID
            $phanbon_products = $productModel->searchAndFilter('', '', '', $phanBonCategoryL3['id'], '1', '', null, null);
        }
    }
}

// --- Fetch Best-Selling ("Featured") products ---
$sanphambanchay_products = $productModel->searchAndFilter('', '', '', '', '1', '1', 10, null);

// --- Fetch products for "Giá thể" category ---
$giathe_products = [];
$giathe_view_all_id = null;
$giatheCategory = $categoryModel->getByName('Giá thể');
if ($giatheCategory) {
    $giathe_view_all_id = $giatheCategory['id'];
    $giathe_products = $productModel->searchAndFilter('', $giathe_view_all_id, '', '', '1', '', null, null);
} else {
    $giatheCategoryL2 = $categoryLevel2Model->getByName('Giá thể');
    if ($giatheCategoryL2) {
        $giathe_view_all_id = $giatheCategoryL2['category_id'];
        $giathe_products = $productModel->searchAndFilter('', '', $giatheCategoryL2['id'], '', '1', '', null, null);
    } else {
        $giatheCategoryL3 = $categoryLevel3Model->getByName('Giá thể');
        if ($giatheCategoryL3) {
            $l3_parent_l2 = $categoryLevel2Model->getById($giatheCategoryL3['category_level2_id']);
            if($l3_parent_l2) $giathe_view_all_id = $l3_parent_l2['category_id'];
            $giathe_products = $productModel->searchAndFilter('', '', '', $giatheCategoryL3['id'], '1', '', null, null);
        }
    }
}

// --- Fetch products for "Hạt giống" category ---
$hatgiong_products = [];
$hatgiong_view_all_id = null;
$hatgiongCategory = $categoryModel->getByName('Hạt giống');
if ($hatgiongCategory) {
    $hatgiong_view_all_id = $hatgiongCategory['id'];
    $hatgiong_products = $productModel->searchAndFilter('', $hatgiong_view_all_id, '', '', '1', '', null, null);
} else {
    $hatgiongCategoryL2 = $categoryLevel2Model->getByName('Hạt giống');
    if ($hatgiongCategoryL2) {
        $hatgiong_view_all_id = $hatgiongCategoryL2['category_id'];
        $hatgiong_products = $productModel->searchAndFilter('', '', $hatgiongCategoryL2['id'], '', '1', '', null, null);
    } else {
        $hatgiongCategoryL3 = $categoryLevel3Model->getByName('Hạt giống');
        if ($hatgiongCategoryL3) {
            $l3_parent_l2 = $categoryLevel2Model->getById($hatgiongCategoryL3['category_level2_id']);
            if($l3_parent_l2) $hatgiong_view_all_id = $l3_parent_l2['category_id'];
            $hatgiong_products = $productModel->searchAndFilter('', '', '', $hatgiongCategoryL3['id'], '1', '', null, null);
        }
    }
}

// --- Fetch products for "Combo Bán Chạy" category (using keyword search) ---
$combobanchay_products = $productModel->searchAndFilter('Combo', '', '', '', '1', '', null, null);

// --- Fetch products for "Dụng cụ làm vườn" TABS ---
$dungculamvuon_parent_category_id = null;
$chautrongrau_products = [];
$dungcuchamsoc_products = [];
$dungculamvuon_products = [];

// Try to find "Dụng cụ làm vườn" at any level to get the main parent L1 ID
$dclv_cat_l1 = $categoryModel->getByName('Dụng cụ làm vườn');
if ($dclv_cat_l1) {
    $dungculamvuon_parent_category_id = $dclv_cat_l1['id'];
} else {
    $dclv_cat_l2 = $categoryLevel2Model->getByName('Dụng cụ làm vườn');
    if ($dclv_cat_l2) {
        $dungculamvuon_parent_category_id = $dclv_cat_l2['category_id']; // Get parent L1 ID
    } else {
        $dclv_cat_l3 = $categoryLevel3Model->getByName('Dụng cụ làm vườn');
        if ($dclv_cat_l3) {
            $l3_parent_l2 = $categoryLevel2Model->getById($dclv_cat_l3['category_level2_id']);
            if ($l3_parent_l2) {
                $dungculamvuon_parent_category_id = $l3_parent_l2['category_id']; // Get parent L1 ID
            }
        }
    }
}

// Now, if we found the parent category, fetch the products for the tabs
if ($dungculamvuon_parent_category_id) {
    // Tab 1: Chậu trồng rau (Filter by keyword 'Chậu' within the parent category)
    $chautrongrau_products = $productModel->searchAndFilter('Chậu', $dungculamvuon_parent_category_id, '', '', '1', '', null, null);

// Tab 2: Dụng cụ chăm sóc cây
$dungcuchamsoc_products = [];
$dungcuchamsoc_category_id_l1 = null;
$dungcuchamsoc_category_id_l2 = null;
$dungcuchamsoc_category_id_l3 = null;

$dcc_cat_l1 = $categoryModel->getByName('Dụng cụ chăm sóc cây');
if ($dcc_cat_l1) {
    $dungcuchamsoc_category_id_l1 = $dcc_cat_l1['id'];
} else {
    $dcc_cat_l2 = $categoryLevel2Model->getByName('Dụng cụ chăm sóc cây');
    if ($dcc_cat_l2) {
        $dungcuchamsoc_category_id_l2 = $dcc_cat_l2['id'];
    } else {
        $dcc_cat_l3 = $categoryLevel3Model->getByName('Dụng cụ chăm sóc cây');
        if ($dcc_cat_l3) {
            $dungcuchamsoc_category_id_l3 = $dcc_cat_l3['id'];
        }
    }
}

if ($dungcuchamsoc_category_id_l1) {
    $dungcuchamsoc_products = $productModel->searchAndFilter('', $dungcuchamsoc_category_id_l1, '', '', '1', '', null, null);
} elseif ($dungcuchamsoc_category_id_l2) {
    $dungcuchamsoc_products = $productModel->searchAndFilter('', '', $dungcuchamsoc_category_id_l2, '', '1', '', null, null);
} elseif ($dungcuchamsoc_category_id_l3) {
    $dungcuchamsoc_products = $productModel->searchAndFilter('', '', '', $dungcuchamsoc_category_id_l3, '1', '', null, null);
}
    
    // Tab 3: Dụng cụ làm vườn (General - All products in the parent category)
    $dungculamvuon_products = $productModel->searchAndFilter('', $dungculamvuon_parent_category_id, '', '', '1', '', null, null);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ</title>
    
    <script>
        window.isHomePage = true;
    </script>

    <style>

        .nnp-homepage-layout {
            padding-top: 1px;
            padding-left: 22px !important;
            padding-right: 22px !important;
        }
        .nnp-menu-col {
            padding-right: 0 !important;
            min-height: 800px;
        }
        
       

        /* --- STYLE SLIDER & NAVIGATION CHUNG --- */
        .nnp-product-slider-wrapper {
            position: relative;
            padding: 0 40px; 
        }
        .nnp-product-slider {
            overflow-x: auto; 
            white-space: nowrap; 
            -webkit-overflow-scrolling: touch;
            padding-bottom: 15px; 
            scroll-behavior: smooth; 
            scrollbar-width: none; 
            -ms-overflow-style: none; 
        }
        .nnp-product-slider::-webkit-scrollbar {
            display: none; 
        }
        
        .nnp-product-slider .col {
            display: inline-block; 
            width: 25%; /* 4 sản phẩm trên Desktop */
            max-width: 25%;
            padding: 0 8px; 
            vertical-align: top;
            box-sizing: border-box;
        }

        /* Nút Điều hướng */
        .nnp-slider-nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 20;
            background: #fff;
            border: 1px solid #ddd;
            color: #333;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: background 0.2s;
        }
        .nnp-slider-nav-btn:hover {
            background: #f0f0f0;
        }
        .nnp-prev-btn { left: 5px; }
        .nnp-next-btn { right: 5px; }
        
        /* --- RESPONSIVE FIXES (Mobile) --- */
        @media (max-width: 991.98px) {
            .nnp-homepage-layout {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
            .nnp-icon-menu-wrapper {
                margin-top: 15px; 
                margin-bottom: 15px; 
            }
            .nnp-product-slider-wrapper {
                padding: 0 15px; 
            }
            .nnp-product-slider .col {
                width: 50%; 
                max-width: 50%;
            }
        }
        /* CSS cho Product Card, Hover Image, và Tag "Hàng mới về" phải được đặt trong các file component tương ứng (phanbon.php/kichre.php) */
    </style>
</head>
<body>

<?php 
// 1. HEADER (CHỨA: Top Bar, Nav Bar, Mega Menu dọc)
include 'app/Views/user/header.php'; 
?>

<div class="container nnp-homepage-layout">
    <div class="row">

        <div class="col-lg-3 d-none d-lg-block nnp-menu-col">
            </div>

        <div class="col-12 ps-2 col-lg-9 nnp-banner-col">
            
            <section id="main-banner-section">
                <?php if ($main_banners && $main_banners->num_rows > 0): ?>
                    <?php $main_banner = $main_banners->fetch_assoc(); // Assuming one main banner ?>
                    <div class="bg-light p-3 border rounded shadow-sm mb-3 text-center">
                        <a href="<?php echo htmlspecialchars($main_banner['link']); ?>" target="<?php echo htmlspecialchars($main_banner['target']); ?>">
                            <img src="<?php echo BASE_URL . '/' . htmlspecialchars($main_banner['image']); ?>"
                                 class="img-fluid rounded" alt="<?php echo htmlspecialchars($main_banner['title']); ?>"> 
                        </a>
                    </div>
                <?php else: ?>
                     <div class="bg-light p-3 border rounded shadow-sm mb-3 text-center text-muted">
                        Không có main banner nào.
                    </div>
                <?php endif; ?>
            </section>
            
            <section id="small-banners-section">
                <div class="row">
                    <?php if ($small_banners && $small_banners->num_rows > 0): ?>
                        <?php while($s_banner = $small_banners->fetch_assoc()): ?>
                        <div class="col-4">
                            <a href="<?php echo htmlspecialchars($s_banner['link']); ?>" target="<?php echo htmlspecialchars($s_banner['target']); ?>">
                                <img src="<?php echo BASE_URL . '/' . htmlspecialchars($s_banner['image']); ?>" class="img-fluid rounded mb-2" alt="<?php echo htmlspecialchars($s_banner['title']); ?>">
                            </a>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12 text-center text-muted">
                            Không có small banner nào.
                        </div>
                    <?php endif; ?>
                </div>
            </section>
            
        </div>
        
    </div>
</div>

        <?php include 'app/Views/user/trangchu/icon.php'; ?>



<div class="container">
    <div class="row">
        <?php include 'app/Views/user/trangchu/kichre.php'; ?>

        
        <?php include 'app/Views/user/trangchu/phanbon.php'; ?>
        <?php include 'app/Views/user/trangchu/sanphambanchay.php'; ?>
        <?php include 'app/Views/user/trangchu/giathe.php'; ?>

       


<div class="container my-4">
    <?php if ($hot_banners && $hot_banners->num_rows > 0): ?>
        <?php $hot_banner = $hot_banners->fetch_assoc(); // Assuming one hot banner ?>
        <a href="<?php echo htmlspecialchars($hot_banner['link']); ?>" target="<?php echo htmlspecialchars($hot_banner['target']); ?>">
            <img src="<?php echo BASE_URL . '/' . htmlspecialchars($hot_banner['image']); ?>" 
                 alt="<?php echo htmlspecialchars($hot_banner['title']); ?>"
                 class="img-fluid d-block mx-auto rounded shadow-sm">
        </a>
    <?php else: ?>
        <div class="text-center text-muted">
            Không có hot banner nào.
        </div>
    <?php endif; ?>
</div>


 <?php include 'app/Views/user/trangchu/hatgiong.php'; ?>
        <?php include 'app/Views/user/trangchu/combobanchay.php'; ?>
        <?php include 'app/Views/user/trangchu/dungculamvuon.php'; ?>



        <div class="container my-5">
    <h4 class="fw-bold mb-4">THƯƠNG HIỆU SẢN PHẨM</h4>

    <div class="row align-items-center justify-content-between text-center g-4">
        <div class="col-6 col-md">
            <img src="https://theme.hstatic.net/1000269461/1000985512/14/brand_1_small.jpg?v=2277" class="img-fluid" alt="Đặng Gia Trang">
        </div>
        <div class="col-6 col-md">
            <img src="https://theme.hstatic.net/1000269461/1000985512/14/brand_2_small.jpg?v=2277" class="img-fluid" alt="TRIBAT">
        </div>
        <div class="col-6 col-md">
            <img src="https://theme.hstatic.net/1000269461/1000985512/14/brand_3_small.jpg?v=2277" class="img-fluid" alt="Phú Nông Seeds">
        </div>
        <div class="col-6 col-md">
            <img src="https://theme.hstatic.net/1000269461/1000985512/14/brand_4_small.jpg?v=2277" class="img-fluid" alt="Tràng Nông">
        </div>
        <div class="col-6 col-md">
            <img src="https://theme.hstatic.net/1000269461/1000985512/14/brand_5_small.jpg?v=2277" class="img-fluid" alt="Syngenta">
        </div>
                <div class="col-6 col-md">
            <img src="https://theme.hstatic.net/1000269461/1000985512/14/brand_6_small.jpg?v=2277" class="img-fluid" alt="Bayer">
        </div>
        <div class="col-6 col-md">
            <img src="https://theme.hstatic.net/1000269461/1000985512/14/brand_7_small.jpg?v=2277" class="img-fluid" alt="Hợp Trí">
        </div>

    </div>
</div>
        <?php include 'app/Views/user/trangchu/kinhnghiem.php'; ?>

        
        <div class="modal fade" id="viewAllProductsModal" tabindex="-1" aria-labelledby="viewAllProductsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewAllProductsModalLabel">Tất Cả Sản Phẩm Nổi Bật</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted">Đây là nơi tất cả các sản phẩm sẽ được tải.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>



<script>
    // Hàm JavaScript cho chức năng cuộn slider
    function scrollSlider(sliderId, direction) {
        const slider = document.getElementById(sliderId);
        if (!slider) return;

        // Tính toán chiều rộng của một sản phẩm (25% trên desktop, 50% trên mobile)
        let scrollAmount = slider.clientWidth / 4; 
        
        if (window.innerWidth < 992) {
            scrollAmount = slider.clientWidth / 2;
        }

        if (direction === 'next') {
            slider.scrollLeft += scrollAmount;
        } else if (direction === 'prev') {
            slider.scrollLeft -= scrollAmount;
        }
    }
</script>

<?php 
// 3. FOOTER (CHỨA: Footer, và Bootstrap JS)
include 'app/Views/user/footer.php'; 
?>

</body>
</html>