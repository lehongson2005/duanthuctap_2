<?php
// Include database and models
$baseDir = __DIR__;
include_once $baseDir . '/../../config/db.php';

include_once $baseDir . '/../../models/CategoryModel.php';
include_once $baseDir . '/../../models/CategoryLevel2Model.php'; 
include_once $baseDir . '/../../models/CategoryLevel3Model.php';
include_once $baseDir . '/../../models/ProductModel.php';

// --- Fetch Cart Data for Header ---
$cart_item_count = 0;
$cart_total_price = 0;
$cart_products = [];
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $productModel_for_cart = new ProductModel($conn);
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $cart_product_item = $productModel_for_cart->getById($product_id);
        if ($cart_product_item) {
            $price = (isset($cart_product_item['discount_price']) && $cart_product_item['discount_price'] > 0) ? $cart_product_item['discount_price'] : $cart_product_item['price'];
            $cart_products[] = [
                'id' => $cart_product_item['id'],
                'name' => $cart_product_item['name'],
                'thumbnail' => $cart_product_item['thumbnail'],
                'quantity' => $quantity,
                'price' => $price,
                'sub_total' => $price * $quantity
            ];
            $cart_total_price += $price * $quantity;
        }
    }
    $cart_item_count = count($cart_products);
}

// --- Original PHP logic continues below ---
$categoryModel = new CategoryModel($conn);
$categoryLevel2Model = new CategoryLevel2Model($conn); 
$categoryLevel3Model = new CategoryLevel3Model($conn);

// Fetch all data
$level1CategoriesResult = $categoryModel->getAll();
$level1Categories = ($level1CategoriesResult) ? $level1CategoriesResult->fetch_all(MYSQLI_ASSOC) : [];

$level2CategoriesResult = $categoryLevel2Model->getAll();
$level2Categories = ($level2CategoriesResult) ? $level2CategoriesResult->fetch_all(MYSQLI_ASSOC) : [];

$level3CategoriesResult = $categoryLevel3Model->getAll();
$level3Categories = ($level3CategoriesResult) ? $level3CategoriesResult->fetch_all(MYSQLI_ASSOC) : [];

// Build the category tree
$categoryTree = [];

// Group level 3 by parent
$level3Grouped = [];
if (!empty($level3Categories)) {
    foreach ($level3Categories as $cat3) {
        $level3Grouped[$cat3['category_level2_id']][] = $cat3;
    }
}


// Group level 2 by parent and attach level 3 children
$level2Grouped = [];
if (!empty($level2Categories)) {
    foreach ($level2Categories as $cat2) {
        if (isset($level3Grouped[$cat2['id']])) {
            $cat2['children'] = $level3Grouped[$cat2['id']];
        } else {
            $cat2['children'] = [];
        }
        // Assuming category_level2 table has a 'category_id' column for the parent
        if (isset($cat2['category_id'])) {
            $level2Grouped[$cat2['category_id']][] = $cat2;
        }
    }
}

// Build the final tree
if (!empty($level1Categories)) {
    foreach ($level1Categories as $cat1) {
        if (isset($level2Grouped[$cat1['id']])) {
            $cat1['children'] = $level2Grouped[$cat1['id']];
        } else {
            $cat1['children'] = [];
        }
        $categoryTree[] = $cat1;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation Bar Nông Nghiệp Phố - Chuyển sang Hover Mode</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <style>
        :root {
            --nnp-green: #238E46; 
            --nnp-dark-blue: #34495e; 
            --nnp-light-bg: #F0F0F0;
            --nnp-cart-green: #2F9B50; 
            --nnp-yellow: #F9D050;
        }
        a{
            text-decoration: none !important;
            color: black;
        }
        ul, li {
            list-style-type: none;
            padding: 0;
            margin-left: 0; 
        }
        .search-input-group {
            position: relative;
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
            width: 25% !important ;
        }
        @media (min-width: 992px) {
            .input-group {
                width: 100%;
                margin-right: 45px;
            }
        }
        .bg-nnp-green { background-color: var(--nnp-green) !important; }
        .nnp-logo-icon { width: 28px; height: 28px; text-align: center; line-height: 28px; font-size: 1.1rem; font-weight: bold; color: white; border: 2px solid white; border-radius: 50%; margin-right: 8px; }
        .search-input-group .form-control { 
            background-color: var(--nnp-light-bg); border: none; box-shadow: none; 
            height: 45px; border-radius: 0.375rem 0 0 0.375rem; padding-left: 15px; 
        }
        .btn-nnp-yellow { 
            background-color: var(--nnp-yellow); border-color: var(--nnp-yellow); color: #333; 
            border-radius: 0 0.375rem 0.375rem 0; padding: 0 15px; display: flex; 
            align-items: center; 
        }
        .hot-search-keywords { font-size: 0.75rem; color: rgba(255, 255, 255, 0.7); margin-top: 5px; line-height: 1.2; }
        .hot-search-keywords a { color: white; text-decoration: none; margin-right: 5px; }
        .header-icon-link {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #fff;
            text-decoration: none;
            font-size: 13px;
            padding-left:10px;
            width: 150px;
        }
        .header-icon-link:hover{
            color:var(--nnp-yellow); 
        }
        .header-icon-link i {
            font-size: 22px;
        }
        .header-icon-link .title {
            display: block;
            font-weight: 600;
        }
        .header-icon-link .sub {
            display: block;
            font-size: 12px;
            
        }
        .cart-btn-custom {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #2fa24a;
            padding: 8px 14px;
            border-radius: 8px;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            margin-left:3px;
            width: 125px;
        }
        .cart-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #ffd84d;
            color: #333;
            font-size: 11px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 50%;
        }
        .navbar-nnp { background-color: white; border-bottom: 1px solid #eee;padding: 0 50px 0px 50px }
        .btn-category-toggle {background: #2f4858; color: white; padding: 0.75rem 1.25rem; display: flex; align-items: center; gap: 10px; font-weight: bold; font-size: 0.95rem; transition: background-color 0.2s; height: 52px; line-height: 1.2; width: 99%; justify-content: start; }
        .btn-category-toggle:hover {  color:white; }
        .navbar-nav .nav-link { color: #333; font-weight: 500; padding: 0.75rem 1rem; transition: color 0.2s; font-size: 0.9rem; }
        .navbar-nav .nav-link:hover { color: var(--nnp-green); }
        .sticky-header-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1050;
            background:#238e46;
            transform: translateY(-100%);
            opacity: 0;
            transition: transform 0.35s ease, opacity 0.35s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .sticky-header-container.scrolled {
            transform: translateY(0);
            opacity: 1;
        }
        .sticky-desktop { background-color: var(--nnp-green); padding: 8px 0; }
        .sticky-desktop .sticky-category-toggle { 
            background-color: var(--nnp-dark-blue); color: white; padding: 0 1.25rem; 
            display: flex; align-items: center; gap: 10px; font-weight: bold; 
            font-size: 0.95rem; height: 40px; text-decoration: none; 
            transition: background-color 0.2s; 
            z-index: 1060; 
        }
        .sticky-desktop .sticky-category-toggle:hover { background-color: #2c3e50; }
        .sticky-desktop .search-input-group .form-control { height: 40px; background-color: white; }
        .sticky-desktop .btn-nnp-yellow { height: 40px; min-width: 40px; }
        .category-dropdown-container { 
            position: relative; 
            z-index: 1000; 
        }
        .mega-menu {
            position: absolute;
            top: 100%; 
            left: 0;
            opacity: 0; 
            visibility: hidden;
            transform: scale(0.95);
            transform-origin: top left;
            transition: opacity 0.3s ease-out, visibility 0.3s, transform 0.3s ease-out, width 0.3s ease; 
            width: 300px; 
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #eee;
            border-top: none;
            padding: 0;
            margin: 0;
            flex-wrap: nowrap;
            z-index: 1055; 
            display: flex; 
            flex-direction: row;
            overflow: hidden; 
            max-height: 600px; 
        }
        .mega-menu.is-open { 
            opacity: 1; 
            visibility: visible;
            transform: scale(1);
        }
        .mega-menu-list-main{
            padding-left: 0px; 
            padding-top: 10px;
        }
        .mega-menu .col-left {
            width: 300px;
            transition: border-right 0.3s ease; 
            flex-shrink: 0;
            padding: 0;
            overflow: hidden; 
            max-height: 600px; 
            overflow-y: auto; 
            overflow-x: hidden; 
        }
        .mega-menu.has-sub-shown {
             width: 600px;
        }
        .mega-menu.has-sub-shown .col-left {
            width: 300px;
            border-right: 1px solid #eee;
        }
        .mega-menu-sub-wrapper {
            width: 300px;
            flex-shrink: 0;
            display: none; /* Default hidden state, will be overridden by .is-active-sub */
            padding: 10px 0; 
            padding-left: 30px; 
            overflow-y: auto; 
            overflow-x: hidden; 
        }
        .mega-menu-sub-wrapper.is-active-sub {
            display: block;
        }
        .mega-menu-list-main .list-group-item {
            padding: 0 !important; 
        }
        .mega-menu-list-main .list-group-item a {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 15px 8px 30px; 
            color: black;
            text-decoration: none;
            transition: background-color 0.2s, color 0.2s;
        }
        .mega-menu-list-main .list-group-item:hover {
            background-color: var(--nnp-light-bg);
        }
        .mega-menu-list-main .list-group-item:hover a {
            color: var(--nnp-green); 
        }
        .mega-menu-list-main .list-group-item:hover .fa-chevron-right {
            color: var(--nnp-green) !important;
        }
        .mega-menu-list-sub .list-group-item {
             padding: 4px 0 !important;
        }
        .mega-menu-list-sub .list-group-item a {
             padding: 0;
             margin: 0;
        }
        @keyframes blink-red {
            0%   { color: #e53935; }
            25%  { color: #ffffff; }
            50%  { color: #ff7043; }
            75%  { color: #ffffff; }
            100% { color: #43a047; }
        }
        .input-group .btn{
            background:yellow;
        }
        .btn-category-toggle.blink-effect, 
        .sticky-category-toggle.blink-effect {
            animation: blink-red 2s infinite !important;
            background-color: var(--nnp-dark-blue); 
        }
        .btn-category-toggle.blink-effect:hover,
        .sticky-category-toggle.blink-effect:hover {
            animation: none !important; 
            color: var(--nnp-yellow) !important; 
        }
        .nnp-mobile-header { 
            background-color: var(--nnp-green);
            padding: 10px 15px;
            display: flex;
            align-items: center;
            flex-wrap: wrap; 
            gap: 10px;
            z-index: 1040; 
            position: relative; 
        }
        .nnp-mobile-header .nnp-mobile-search { 
            width: 100%; 
            margin-top: 5px; 
            order: 10; 
        }
        .sticky-mobile {
            background-color: var(--nnp-green);
            padding: 10px 15px;
            width: 100%;
            align-items: center;
            z-index: 1050; 
            position: relative;
        }
        .offcanvas.offcanvas-start {
            top: 0 !important; 
            height: 100vh !important; 
            display: flex;
            flex-direction: column;
            width: 100%; 
            max-width: 100%;
        }
        .offcanvas-header-custom{
            background: var(--nnp-green);
            color: white;
            padding: 1.5rem 1rem;
            flex-shrink: 0; 
            position: relative; 
        }
        .offcanvas-header-custom .user-info {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.7);
        }
        .offcanvas-header-custom .user-link {
            font-weight: bold;
            color: white;
            text-decoration: none;
        }
        .offcanvas-body {
            overflow-y: auto; 
            flex-grow: 1; 
            padding: 0 !important; 
        }
        .offcanvas-body .list-group-flush {
            padding: 0 0 1rem 0; 
        }
        .offcanvas-body .list-group-item a {
            display: block; 
            padding: 0.5rem 1rem;
            margin: -0.5rem -1rem; 
            color: black;
            text-decoration: none;
            padding:0 10px 0 30px;
        }
        .offcanvas-body .list-group-item a:hover {
            color: var(--nnp-green);
        }
        .offcanvas-body .list-group-item {
            padding: 0.5rem 1rem; 
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer; 
        }
        .offcanvas-body .list-group-item.active {
             background-color: var(--nnp-light-bg) !important;
             color: var(--nnp-green) !important;
             font-weight: bold;
        }
        .offcanvas-body .list-group-item:hover:not(.active) {
            background-color: var(--nnp-light-bg);
        }
        .offcanvas-footer-contact {
            flex-shrink: 0; 
            padding: 1rem;
            border-top: 1px solid #eee;
            background-color: white; 
            display: flex;
            gap: 10px;
            z-index: 10; 
        }
        .offcanvas-footer-contact .contact-btn {
            flex-grow: 1;
            text-align: center;
            padding: 0.75rem 0;
            border-radius: 0.25rem;
            font-weight: bold;
            text-decoration: none;
        }
        .offcanvas-footer-contact .contact-btn:first-child {
            background-color: var(--nnp-green);
            color: white;
        }
        .offcanvas-footer-contact .contact-btn:last-child {
            background-color: #007bff; 
            color: white;
        }
        .nnp-sub-offcanvas {
            z-index: 1060; 
        }
        .nnp-sub-offcanvas .offcanvas-header-custom {
            background: var(--nnp-dark-blue); 
        }
        .nnp-sub-offcanvas .offcanvas-header-custom button[data-bs-dismiss="offcanvas"] {
            display: none !important; 
        }
        @media (min-width: 992px) {
            .sticky-header-container:not(.scrolled) .sticky-desktop { display: none !important; }
            .sticky-mobile, .nnp-mobile-header { display: none !important; }
            .nnp-header-main { display: block !important; }
            .nnp-header-main .input-group { width: 100% !important; }
            .navbar-nnp .col-lg-3.category-dropdown-container {
                width: 25%;
                max-width: 25%;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
            .navbar-nnp .col-lg-9 {
                width: 75%;
                max-width: 75%;
            }
            .sticky-header-container .category-dropdown-container {
                width: 300px; 
                flex-shrink: 0;
                margin-left: 0 !important;
                margin-right: 1rem !important; 
            }
            .offcanvas { visibility: hidden; }
        }
        @media (max-width: 991.98px) {
            .mega-menu { display: none !important; }
            .nnp-header-main { display: none !important; } 
            .nnp-mobile-header { display: flex !important; }
            .sticky-desktop { display: none !important; }
            .sticky-mobile { display: none; }
            .sticky-header-container.scrolled .sticky-mobile {
                display: flex !important;
                background-color: var(--nnp-green);
            }
            .mega-menu-sub-wrapper{
                padding-top: 10px !important;
                padding-left: 30px !important;
            }
        }
        .header-account-container {
            display: flex;
            align-items: center; 
            padding: 10px 15px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 6px;
            transition: background-color 0.2s;
        }
        .header-icon-large {
            font-size: 1.8rem;
            margin-right: 10px;
            min-width: 25px;
        }
        .account-links-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            line-height: 1.2;
        }
        .account-links-container a {
            color: inherit;
            text-decoration: none;
            padding: 0;
            margin: 0;
            transition: color 0.2s;
        }
        .account-links-container a:hover {
            color: #ffcc00;
        }
        .account-links-container .title {
            font-weight: bold;
            font-size: 0.8rem;
        }
        .account-links-container .sub {
            font-size: 0.6rem;
        }
    .cart-btn-container { position: relative; }
        .mini-cart {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            width: 350px;
            background-color: white;
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            z-index: 1100;
            border-radius: 4px;
        }
        .cart-btn-container:hover .mini-cart { display: block; }
        .mini-cart-header {
            padding: 10px 15px;
            font-weight: bold;
            border-bottom: 1px solid #eee;
            color: #333;
        }
        .mini-cart-body {
            max-height: 300px;
            overflow-y: auto;
            padding: 10px;
        }
        .mini-cart-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            color: #333;
        }
        .mini-cart-item img {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-right: 10px;
            border: 1px solid #eee;
        }
        .mini-cart-item-info { flex-grow: 1; }
        .mini-cart-item-name {
            font-size: 0.9rem;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #333;
            font-weight: 500;
        }
        .mini-cart-item-price { font-size: 0.8rem; color: #888; }
        .mini-cart-footer {
            padding: 15px;
            border-top: 1px solid #eee;
            background-color: #f9f9f9;
        }
        .mini-cart-total {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }
    .cart-btn-container { position: relative; }
        .mini-cart {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            width: 350px;
            background-color: white;
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            z-index: 1100;
            border-radius: 4px;
        }
        .cart-btn-container:hover .mini-cart { display: block; }
        .mini-cart-header {
            padding: 10px 15px;
            font-weight: bold;
            border-bottom: 1px solid #eee;
            color: #333;
        }
        .mini-cart-body {
            max-height: 300px;
            overflow-y: auto;
            padding: 10px;
        }
        .mini-cart-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            color: #333;
        }
        .mini-cart-item img {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-right: 10px;
            border: 1px solid #eee;
        }
        .mini-cart-item-info { flex-grow: 1; }
        .mini-cart-item-name {
            font-size: 0.9rem;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #333;
            font-weight: 500;
        }
        .mini-cart-item-price { font-size: 0.8rem; color: #888; }
        .mini-cart-footer {
            padding: 15px;
            border-top: 1px solid #eee;
            background-color: #f9f9f9;
        }
        .mini-cart-total {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }
    </style>
</head>
<body>

<div class="d-lg-none nnp-mobile-header" id="mobileHeader">
    <button class="btn p-0 text-white fs-3 me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#nnpMobileMenu" aria-controls="nnpMobileMenu">
        <i class="fas fa-bars"></i>
    </button>
    <div class="nnp-mobile-logo d-flex align-items-center">
        <a href="<?php echo BASE_URL; ?>/index.php" class="text-white text-decoration-none d-flex align-items-center">
            <span class="nnp-logo-icon" style="margin-right: 5px;">i</span>
            <span class="h4 fw-bold mb-0 text-white">Nông Nghiệp Phố</span>
        </a>
    </div>
    <a href="<?php echo BASE_URL; ?>/app/Views/user/giohang/giohang.php" class="btn p-0 text-white fs-4 ms-auto position-relative">
        <i class="fas fa-shopping-cart"></i>
        <?php if ($cart_item_count > 0): ?>
            <span class="cart-badge" style="top: -5px; right: -5px; padding: 2px 6px;"><?php echo $cart_item_count; ?></span>
        <?php endif; ?>
    </a>
    
    <div class="nnp-mobile-search">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Tìm kiếm..." aria-label="Tìm kiếm sản phẩm">
            <button class="btn btn-nnp-yellow" type="button" style="height: 40px;"><i class="fas fa-search"></i></button>
        </div>
        <div class="hot-search-keywords">
            <a href="#">lưới lan</a> | <a href="#">trichoderma</a> | <a href="#">đất sạch</a> | <a href="#">phân bón</a> | <a href="#">phân gà</a>
        </div>
    </div>
</div>

<div class="sticky-header-container" id="stickyHeader">
    <div class="container px-5 sticky-desktop d-none d-lg-flex align-items-center">
        <div class="d-flex w-100 align-items-center">
            
            <div class="me-3 category-dropdown-container" id="stickyDropdownContainer">
                <a class="sticky-category-toggle blink-effect" id="stickyCategoryToggleBtn" href="#" role="button" aria-expanded="false">
                    <i class="fas fa-bars me-2"></i>
                    DANH MỤC SẢN PHẨM
                </a>
                </div>
            
            <div class="input-group search-input-group">
                <input type="text" class="form-control" placeholder="Tìm kiếm sản phẩm..." aria-label="Tìm kiếm sản phẩm">
                <button class="btn btn-nnp-yellow" type="button"><i class="fas fa-search"></i></button>
            </div>
            
            <a href="tel:0865588883" class="header-icon-link">
                <i class="fas fa-phone-alt"></i><span class="header-phone-number"> Gọi mua hàng 0865588883</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/app/Views/user/hethongcuahang/hethongcuahang.php" class="header-icon-link">
                <i class="fas fa-store"></i><span>Hệ thống <br> cửa hàng</span>
            </a>
            <div class="header-account-container">
                <i class="fas fa-user header-icon-large"></i>
                <div class="account-links-container">
                    <a href="<?php echo BASE_URL; ?>/app/Views/user/taikhoan/taikhoan.php" class="title">Tài khoản</a>
                    <a href="<?php echo BASE_URL; ?>/app/Views/admin/index.php" class="sub">Đăng nhập</a>
                </div>
            </div>
            <div class="cart-btn-container">
                <a href="<?php echo BASE_URL; ?>/app/Views/user/giohang/giohang.php" class="cart-btn-custom position-relative text-decoration-none">
                    <i class="fas fa-shopping-bag fs-5"></i><span>Giỏ hàng</span>
                    <?php if ($cart_item_count > 0): ?>
                     <span class="cart-badge badge rounded-pill bg-warning text-dark position-absolute">
    <?= $cart_item_count > 0 ? $cart_item_count : '' ?>
</span>
                    <?php endif; ?>
                </a>
                <div class="mini-cart">
                    <?php if (!empty($cart_products)): ?>
                        <div class="mini-cart-header">Sản phẩm mới thêm</div>
                        <div class="mini-cart-body">
                            <?php foreach($cart_products as $item): ?>
                            <div class="mini-cart-item">
                                <img src="<?php echo BASE_URL . '/' . htmlspecialchars($item['thumbnail']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <div class="mini-cart-item-info">
                                    <a href="<?php echo BASE_URL; ?>/app/Views/user/sanpham/chitietsanpham.php?id=<?php echo $item['id']; ?>" class="mini-cart-item-name"><?php echo htmlspecialchars($item['name']); ?></a>
                                    <span class="mini-cart-item-price"><?php echo $item['quantity']; ?> x <?php echo number_format($item['price']); ?>₫</span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mini-cart-footer">
                            <div class="mini-cart-total">
                                <span>Tổng cộng:</span>
                                <span><?php echo number_format($cart_total_price); ?>₫</span>
                            </div>
                            <a href="<?php echo BASE_URL; ?>/app/Views/user/giohang/giohang.php" class="btn btn-success w-100">Xem giỏ hàng</a>
                        </div>
                    <?php else: ?>
                        <div class="p-3 text-center text-dark">Giỏ hàng của bạn đang trống.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="sticky-mobile d-lg-none d-flex align-items-center" id="nnp-sticky-mobile">
        <button class="btn p-0 text-white fs-3 me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#nnpMobileMenu" aria-controls="nnpMobileMenu">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="nnp-mobile-logo d-flex align-items-center me-3">
            <a href="<?php echo BASE_URL; ?>/index.php" class="text-white text-decoration-none d-none align-items-center"> 
                <span class="nnp-logo-icon" style="margin-right: 5px;">i</span>
                <span class="h4 fw-bold mb-0 text-white" style="font-size: 1.2rem;">Nông Nghiệp Phố</span>
            </a>
        </div>
        <div class=" me-5">
            <a href="<?php echo BASE_URL; ?>/index.php"><i class="fa-solid fa-house" style="color: #ffffff;"></i></a>       
        </div>
        
        <div class="input-group search-input-group flex-grow-1 me-3">
            <input type="text" class="form-control" placeholder="Tìm kiếm..." style="height: 40px; background-color: white; border: none; border-radius: 0.375rem 0 0 0.375rem;">
            <button class="btn btn-nnp-yellow" type="button" style="height: 40px;"><i class="fas fa-search"></i></button>
        </div>
        
        <a href="<?php echo BASE_URL; ?>/app/Views/user/giohang/giohang.php" class="btn p-0 text-white fs-4 position-relative">
            <i class="fas fa-shopping-cart"></i>
            <?php if ($cart_item_count > 0): ?>
                <span class="cart-badge" style="top: -5px; right: -5px; padding: 2px 6px;"><?php echo $cart_item_count; ?></span>
            <?php endif; ?>
        </a>
    </div>
</div>

<header class="bg-nnp-green py-3 nnp-header-main d-none d-lg-block">
    <div class="container px-5">
        <div class="row align-items-center">
            <div class="col-lg-2 d-flex align-items-center">
                <a href="<?php echo BASE_URL; ?>/index.php" class="text-white text-decoration-none d-flex align-items-center">
                    <h5 class="text-light"><a href="<?php echo BASE_URL; ?>/index.php" class="text-light fw-bold">Nông Nghiệp Phố</a></h5>
                </a>
            </div>
            <div class="col-lg-4">
                <div class="input-group search-input-group">
                    <input type="text" class="form-control" placeholder="Tìm kiếm sản phẩm..." aria-label="Tìm kiếm sản phẩm">
                    <button class="btn btn-nnp-yellow" type="button"><i class="fas fa-search"></i></button>
                </div>
                <div class="hot-search-keywords">
                    <a href="#">lưới lan</a> | <a href="#">thuốc trừ sâu</a> | <a href="#">trichoderma</a> | <a href="#">đất sạch</a> | <a href="#">phân bón</a> | <a href="#">phân gà</a>
                </div>
            </div>
            <div class="col-lg-6 d-flex justify-content-end align-items-center">
                <a href="tel:0865588883" class="header-icon-link">
                    <i class="fas fa-phone-alt"></i>
                    <div>
                        <span class="title">Gọi mua hàng</span>
                        <span class="sub">0865588883</span>
                    </div>
                </a>

                <a href="<?php echo BASE_URL; ?>/app/Views/user/hethongcuahang/hethongcuahang.php" class="header-icon-link">
                    <i class="fas fa-store"></i>
                    <div>
                        <span class="title">Hệ thống <br> cửa hàng</span>
                    </div>
                </a>

                <div class="header-account-container">
                    <i class="fas fa-user header-icon-large"></i>
                    <div class="account-links-container">
                        <a href="<?php echo BASE_URL; ?>/app/Views/user/taikhoan/taikhoan.php" class="title">Tài khoản</a>
                        <a href="<?php echo BASE_URL; ?>/app/Views/admin/index.php" class="sub">Đăng nhập</a>
                    </div>
                </div>
               
                <div class="cart-btn-container">
                    <a href="<?php echo BASE_URL; ?>/app/Views/user/giohang/giohang.php" class="cart-btn-custom position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Giỏ hàng</span>
                         <?php if ($cart_item_count > 0): ?>
                         <span class="cart-badge badge rounded-pill bg-warning text-dark position-absolute">
    <?= $cart_item_count > 0 ? $cart_item_count : '' ?>
</span>
                        <?php endif; ?>
                    </a>
                    <div class="mini-cart">
                        <?php if (!empty($cart_products)): ?>
                            <div class="mini-cart-header">Sản phẩm mới thêm</div>
                            <div class="mini-cart-body">
                                <?php foreach($cart_products as $item): ?>
                                <div class="mini-cart-item">
                                    <img src="<?php echo BASE_URL . '/' . htmlspecialchars($item['thumbnail']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    <div class="mini-cart-item-info">
                                        <a href="<?php echo BASE_URL; ?>/app/Views/user/sanpham/chitietsanpham.php?id=<?php echo $item['id']; ?>" class="mini-cart-item-name"><?php echo htmlspecialchars($item['name']); ?></a>
                                        <span class="mini-cart-item-price"><?php echo $item['quantity']; ?> x <?php echo number_format($item['price']); ?>₫</span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="mini-cart-footer">
                                <div class="mini-cart-total">
                                    <span>Tổng cộng:</span>
                                    <span><?php echo number_format($cart_total_price); ?>₫</span>
                                </div>
                                <a href="<?php echo BASE_URL; ?>/app/Views/user/giohang/giohang.php" class="btn btn-success w-100">Xem giỏ hàng</a>
                            </div>
                        <?php else: ?>
                            <div class="p-3 text-center text-dark">Giỏ hàng của bạn đang trống.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<nav class="navbar navbar-expand-lg navbar-nnp d-none d-lg-block">
    <div class="container px-5">
        <div class="row w-100">
            
            <div class="col-lg-3 p-0 category-dropdown-container" id="mainDropdownContainer">
                <a class="btn-category-toggle blink-effect" id="categoryToggleBtn" href="#" role="button" aria-expanded="false">
                    <i class="fas fa-bars"></i>
                    DANH MỤC SẢN PHẨM
                </a>
                
                <div class="mega-menu" id="desktopMegaMenu">
                    <div class="col-left">
                        <ul class="mega-menu-list-main list-group-flush">
                            <?php if (!empty($categoryTree)): ?>
                                <?php foreach ($categoryTree as $cat1): ?>
                                    <li class="list-group-item <?php echo !empty($cat1['children']) ? 'has-submenu' : ''; ?>" data-parent-title="<?php echo htmlspecialchars($cat1['name']); ?>">
                                        <a href="#">
                                            <?php echo htmlspecialchars($cat1['name']); ?>
                                            <?php if (!empty($cat1['children'])): ?>
                                                <i class="fas fa-chevron-right text-muted"></i>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item"><a href="#">Không có danh mục</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <?php if (!empty($categoryTree)): ?>
                        <?php foreach ($categoryTree as $cat1): ?>
                            <?php if (!empty($cat1['children'])): ?>
                                <div class="mega-menu-sub-wrapper" data-parent="<?php echo htmlspecialchars($cat1['name']); ?>">
                                    <ul class="mega-menu-list-sub list-group-flush">
                                        <?php foreach ($cat1['children'] as $cat2): ?>
                                            <li class="list-group-item">
                                                <a href="#"><strong><?php echo htmlspecialchars($cat2['name']); ?></strong></a>
                                                <?php if (!empty($cat2['children'])): ?>
                                                    <ul class="list-group" style="padding-left: 15px; border: none;">
                                                        <?php foreach ($cat2['children'] as $cat3): ?>
                                                            <li class="list-group-item" style="border: none; padding: 4px 0;"><a href="#"><?php echo htmlspecialchars($cat3['name']); ?></a></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>
            
            <div class="col-lg-9 p-0">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 flex-row justify-content-start">
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/app/Views/user/sanpham/sanpham.php">SẢN PHẨM</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/app/Views/user/kiemtra/kiemtra.php">KIỂM TRA ĐƠN HÀNG</a></li>
                     <li class="nav-item dropdown">
                 <a class="nav-link dropdown-toggle"
               href="#"
                  id="camNangDropdown"
                  role="button"
                  data-bs-toggle="dropdown"
                  aria-expanded="false">
                   Chính sách chiết khấu
                              </a>

                     <ul class="dropdown-menu" aria-labelledby="camNangDropdown">
                   <li>
                  <a class="dropdown-item" href="<?php echo BASE_URL; ?>/app/Views/user/chinhsachchietkhau/chinhsachvanchuyen.php">Chính sách vận chuyển</a>
                      </li>
                         <li>
                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/app/Views/user/chinhsachchietkhau/quydinh.php">QUY ĐỊNH VỀ CHÍNH SÁCH ĐẶT CỌC
                    VÀ HÌNH THỨC THANH TOÁN</a>
                              </li>
                    </ul>
                                        </li>
                   <li class="nav-item dropdown">
                                                 <a class="nav-link dropdown-toggle"
                                                href="#"
                                         id="camNangDropdown"
                                     role="button"
                                     data-bs-toggle="dropdown"
                                     aria-expanded="false">
                                  CẨM NANG
                                            </a>

                      <ul class="dropdown-menu" aria-labelledby="camNangDropdown">
        <li>
            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/app/Views/user/camnang/camnang.php">Cẩm nang thi công</a>
        </li>
    </ul>
</li>

                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/app/Views/user/tintuc_sukien/tintuc.php">TIN TỨC - SỰ KIỆN</a></li>
<li class="nav-item">
    <a class="nav-link" href="<?php echo BASE_URL; ?>/app/Views/user/lienhe/lienhe.php">LIÊN HỆ</a>
</li>
                    <li class="nav-item"><a class="nav-link" href="#">GÓP Ý</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="offcanvas offcanvas-start" tabindex="-1" id="nnpMobileMenu" aria-labelledby="nnpMobileMenuLabel">
    <div class="offcanvas-header-custom p-4 bg-success text-white">
        <div class="d-flex align-items-center justify-content-between w-100">
            <div class="d-flex align-items-center">
                <i class="fas fa-user-circle fs-1 me-3"></i>
                <div>
                    <span class="d-block small opacity-75">Chào bạn,</span>
                    <a href="#" class="text-white fw-bold text-decoration-none">Đăng nhập / Đăng ký</a>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
    </div>

    <div class="offcanvas-body p-0">
        <div class="mobile-menu-content">
            <div class="menu-section-title px-3 py-2 bg-light fw-bold text-muted small">DANH MỤC SẢN PHẨM</div>
            <ul class="list-group list-group-flush">
                <?php if (!empty($categoryTree)): ?>
                    <?php foreach ($categoryTree as $cat1): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3"
                            <?php if (!empty($cat1['children'])): ?>
                                data-bs-toggle="offcanvas" data-bs-target="#nnpSubMenu_L1_<?php echo $cat1['id']; ?>"
                            <?php endif; ?>>
                            <a href="#" class="text-dark text-decoration-none flex-grow-1"><?php echo htmlspecialchars($cat1['name']); ?></a>
                            <?php if (!empty($cat1['children'])): ?>
                                <i class="fas fa-chevron-right text-muted small"></i>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>

            <div class="menu-section-title px-3 py-2 bg-light fw-bold text-muted small mt-2">MENU CHÍNH</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item py-3">
                    <a class="text-dark text-decoration-none d-block w-100" href="<?php echo BASE_URL; ?>/app/Views/user/sanpham/sanpham.php">SẢN PHẨM</a>
                </li>
                <li class="list-group-item py-3">
                    <a class="text-dark text-decoration-none d-block w-100" href="<?php echo BASE_URL; ?>/app/Views/user/kiemtra/kiemtra.php">KIỂM TRA ĐƠN HÀNG</a>
                </li>
                
                <li class="list-group-item py-3">
                    <div class="d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#collapseChinhSach">
                        <span>CHÍNH SÁCH</span>
                        <i class="fas fa-plus text-muted small"></i>
                    </div>
                    <div class="collapse mt-2 ms-3" id="collapseChinhSach">
                        <a class="d-block py-2 text-muted text-decoration-none small" href="<?php echo BASE_URL; ?>/app/Views/user/chinhsachchietkhau/chinhsachvanchuyen.php">Chính sách vận chuyển</a>
                        <a class="d-block py-2 text-muted text-decoration-none small" href="<?php echo BASE_URL; ?>/app/Views/user/chinhsachchietkhau/quydinh.php">Quy định thanh toán</a>
                    </div>
                </li>

                <li class="list-group-item py-3">
                    <a class="text-dark text-decoration-none d-block w-100" href="<?php echo BASE_URL; ?>/app/Views/user/camnang/camnang.php">CẨM NANG</a>
                </li>
                <li class="list-group-item py-3">
                    <a class="text-dark text-decoration-none d-block w-100" href="<?php echo BASE_URL; ?>/app/Views/user/tintuc_sukien/tintuc.php">TIN TỨC - SỰ KIỆN</a>
                </li>
                <li class="list-group-item py-3">
                    <a class="text-dark text-decoration-none d-block w-100" href="<?php echo BASE_URL; ?>/app/Views/user/lienhe/lienhe.php">LIÊN HỆ</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="offcanvas-footer p-3 border-top d-flex gap-2">
        <a href="tel:0865588883" class="btn btn-success flex-grow-1 py-2">
            <i class="fas fa-phone-alt me-2"></i> Gọi điện
        </a>
        <a href="#" class="btn btn-outline-success flex-grow-1 py-2">
            <i class="fab fa-facebook-messenger me-2"></i> Nhắn tin
        </a>
    </div>
</div>

<?php if (!empty($categoryTree)): ?>
    <?php foreach ($categoryTree as $cat1): ?>
        <?php if (!empty($cat1['children'])): ?>
        <div class="offcanvas offcanvas-start nnp-sub-offcanvas" tabindex="-1" id="nnpSubMenu_L1_<?php echo $cat1['id']; ?>" aria-labelledby="nnpSubMenu_L1_<?php echo $cat1['id']; ?>Label">
            <div class="offcanvas-header-custom">
                <div class="d-flex align-items-center">
                    <button class="btn p-0 text-white fs-3 me-2" type="button" id="backToMainMenu_L1_<?php echo $cat1['id']; ?>" aria-label="Back">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span class="h5 fw-bold mb-0 text-white"><?php echo htmlspecialchars($cat1['name']); ?></span>
                </div>
            </div>
            <div class="offcanvas-body p-0">
                <ul class="list-group list-group-flush">
                    <?php foreach ($cat1['children'] as $cat2): ?>
                        <li class="list-group-item"
                            <?php if (!empty($cat2['children'])): ?>
                                data-bs-toggle="offcanvas" data-bs-target="#nnpSubMenu_L2_<?php echo $cat2['id']; ?>" aria-controls="nnpSubMenu_L2_<?php echo $cat2['id']; ?>"
                            <?php endif; ?>>
                            <a href="#" class="flex-grow-1"><?php echo htmlspecialchars($cat2['name']); ?></a>
                            <?php if (!empty($cat2['children'])): ?>
                                <i class="fas fa-chevron-right text-muted"></i>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php foreach ($categoryTree as $cat1): ?>
        <?php if (!empty($cat1['children'])): ?>
            <?php foreach ($cat1['children'] as $cat2): ?>
                <?php if (!empty($cat2['children'])): ?>
                <div class="offcanvas offcanvas-start nnp-sub-offcanvas" tabindex="-1" id="nnpSubMenu_L2_<?php echo $cat2['id']; ?>" aria-labelledby="nnpSubMenu_L2_<?php echo $cat2['id']; ?>Label">
                    <div class="offcanvas-header-custom">
                        <div class="d-flex align-items-center">
                            <button class="btn p-0 text-white fs-3 me-2" type="button" id="backToPreviousMenu_L2_<?php echo $cat2['id']; ?>" data-parent-id="#nnpSubMenu_L1_<?php echo $cat1['id']; ?>" aria-label="Back">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span class="h5 fw-bold mb-0 text-white"><?php echo htmlspecialchars($cat2['name']); ?></span>
                        </div>
                    </div>
                    <div class="offcanvas-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($cat2['children'] as $cat3): ?>
                                <li class="list-group-item">
                                    <a href="#"><?php echo htmlspecialchars($cat3['name']); ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const BASE_URL = "<?= BASE_URL; ?>";

    // Define refreshCartDisplay in global scope
    function refreshCartDisplay() {
        console.log('refreshCartDisplay called');
        fetch(BASE_URL + '/app/api/get_cart_data.php')
            .then(response => {
                console.log('Fetch response received:', response);
                // Attempt to parse as JSON directly, if it fails due to HTML, it will be caught.
                return response.json(); 
            })
            .then(data => {
                console.log('Cart data received:', data);
                // Update cart badges
                const cartBadges = document.querySelectorAll('.cart-badge');
                console.log('Found cart badges:', cartBadges.length); // Log the count of found badges
                cartBadges.forEach(badge => {
                    console.log('Processing cart badge:', badge); // Log each badge being processed
                    if (data.item_count > 0) {
                        badge.textContent = data.item_count;
                        badge.style.display = 'block !important'; // Force display to block
                    } else {
                        badge.textContent = ''; // Clear text if empty
                        badge.style.display = 'none !important'; // Force display to none
                    }
                });

                // Update mini-cart content
                const miniCartContainers = document.querySelectorAll('.cart-btn-container .mini-cart'); // Target only direct mini-cart
                
                miniCartContainers.forEach(container => {
                    const header = container.querySelector('.mini-cart-header');
                    const body = container.querySelector('.mini-cart-body');
                    const footer = container.querySelector('.mini-cart-footer');
                    const miniCartEmptyMessage = body ? body.querySelector('.p-3.text-center.text-dark') : null;

                    if (data.item_count === 0) {
                        if (body) {
                            if (!miniCartEmptyMessage) { // Only add if it doesn't exist
                                body.innerHTML = '<div class="p-3 text-center text-dark">Giỏ hàng của bạn đang trống.</div>';
                            }
                        }
                        if (footer) footer.style.display = 'none'; // Hide footer if cart is empty
                        if (header) header.style.display = 'none'; // Hide header if cart is empty
                    } else {
                        if (footer) footer.style.display = 'block'; // Show footer if cart has items
                        if (header) header.style.display = 'block'; // Show header if cart has items

                        let productsHtml = '';
                        data.cart_products.forEach(item => {
                            productsHtml += `
                                <div class="mini-cart-item">
                                    <img src="${item.thumbnail}" alt="${item.name}">
                                    <div class="mini-cart-item-info">
                                        <a href="${BASE_URL}/app/Views/user/sanpham/chitietsanpham.php?id=${item.id}" class="mini-cart-item-name">${item.name}</a>
                                        <span class="mini-cart-item-price">${item.quantity} x ${item.price_formatted}</span>
                                    </div>
                                </div>
                            `;
                        });
                        if (body) body.innerHTML = productsHtml;

                        const totalSpan = container.querySelector('.mini-cart-total span:last-child');
                        if (totalSpan) {
                            totalSpan.textContent = data.total_price_formatted;
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching cart data:', error);
            });
    }

    document.addEventListener("DOMContentLoaded", function() {
        refreshCartDisplay(); // Call on page load to initialize cart display

    // 1. Khai báo các phần tử
    const stickyHeader = document.getElementById('stickyHeader');
    const mobileHeader = document.getElementById('mobileHeader');
    const mainDropdownContainer = document.getElementById('mainDropdownContainer');
    const stickyDropdownContainer = document.getElementById('stickyDropdownContainer');
    const desktopMegaMenu = document.getElementById('desktopMegaMenu');
    
    const scrollThreshold = 100;
    const isHomePage = typeof window.isHomePage !== 'undefined' && window.isHomePage === true;

    // 2. Clone Mega Menu cho Sticky Header nếu chưa có
    if (desktopMegaMenu && stickyDropdownContainer && !stickyDropdownContainer.querySelector('.mega-menu')) {
        const megaMenuClone = desktopMegaMenu.cloneNode(true);
        megaMenuClone.id = 'stickyMegaMenu'; 
        stickyDropdownContainer.appendChild(megaMenuClone);
    }

    // 3. Hàm Reset trạng thái Menu (Quan trọng nhất)
    function resetMegaMenu(megaMenuElement) {
        if (!megaMenuElement) return;
        megaMenuElement.classList.remove('is-open');
        megaMenuElement.classList.remove('has-sub-shown');
        megaMenuElement.style.width = '300px';
        
        const allSubs = megaMenuElement.querySelectorAll('.mega-menu-sub-wrapper');
        allSubs.forEach(sub => {
            sub.classList.remove('is-active-sub');
        });
    }

    // 4. Thiết lập sự kiện Hover cho các Item cấp 1
    function setupItemsHover(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        const megaMenu = container.querySelector('.mega-menu');
        if (!megaMenu) return;

        const menuItems = megaMenu.querySelectorAll('.mega-menu-list-main > li');

        menuItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                // Xóa các sub-menu đang hiện
                const allSubs = megaMenu.querySelectorAll('.mega-menu-sub-wrapper');
                allSubs.forEach(s => s.classList.remove('is-active-sub'));

                if (item.classList.contains('has-submenu')) {
                    const parentText = item.getAttribute('data-parent-title');
                    const targetSub = megaMenu.querySelector(`.mega-menu-sub-wrapper[data-parent="${parentText}"]`);
                    
                    if (targetSub) {
                        targetSub.classList.add('is-active-sub');
                        megaMenu.classList.add('has-sub-shown');
                        megaMenu.style.width = '600px';
                    }
                } else {
                    megaMenu.classList.remove('has-sub-shown');
                    megaMenu.style.width = '300px';
                }
            });
        });
    }

    // 5. Thiết lập sự kiện Hover cho toàn bộ Container (Đóng/Mở menu)
    function setupContainerHover(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        const megaMenu = container.querySelector('.mega-menu');
        if (!megaMenu) return;

        let closeTimeout;

        // Nếu là trang chủ, menu chính luôn mở (trừ khi cuộn xuống thành sticky)
        const shouldAlwaysOpen = (containerId === 'mainDropdownContainer' && isHomePage);

        container.addEventListener('mouseenter', function() {
            clearTimeout(closeTimeout);
            
            // Đóng menu của header kia để tránh xung đột
            if (containerId === 'mainDropdownContainer') {
                const other = stickyDropdownContainer.querySelector('.mega-menu');
                if (other) resetMegaMenu(other);
            } else {
                const other = mainDropdownContainer.querySelector('.mega-menu');
                if (other && !shouldAlwaysOpen) resetMegaMenu(other);
            }

            megaMenu.classList.add('is-open');
        });

        container.addEventListener('mouseleave', function() {
            // Nếu là trang chủ và đang ở header chính, không tự đóng menu con
            if (shouldAlwaysOpen && window.scrollY <= scrollThreshold) {
                // Chỉ reset phần mở rộng cấp 2, giữ lại cấp 1
                const allSubs = megaMenu.querySelectorAll('.mega-menu-sub-wrapper');
                allSubs.forEach(s => s.classList.remove('is-active-sub'));
                megaMenu.classList.remove('has-sub-shown');
                megaMenu.style.width = '300px';
                return;
            }

            closeTimeout = setTimeout(() => {
                resetMegaMenu(megaMenu);
            }, 200);
        });

        setupItemsHover(containerId);
    }

    // Khởi tạo cho cả 2 menu
    setupContainerHover('mainDropdownContainer');
    setupContainerHover('stickyDropdownContainer');

    // Mở mặc định nếu ở trang chủ
    if (isHomePage && desktopMegaMenu) {
        desktopMegaMenu.classList.add('is-open');
    }

    // 6. Xử lý Scroll
    function handleScroll() {
        const isScrolled = window.scrollY > scrollThreshold;
        stickyHeader.classList.toggle('scrolled', isScrolled);
        
        if (window.innerWidth < 992) {
            mobileHeader.style.display = isScrolled ? 'none' : 'flex';
        }

        // Khi cuộn, xử lý menu trang chủ
        if (isScrolled) {
            if (isHomePage) {
                const mainMenu = mainDropdownContainer.querySelector('.mega-menu');
                resetMegaMenu(mainMenu);
            }
        } else {
            if (isHomePage) {
                const mainMenu = mainDropdownContainer.querySelector('.mega-menu');
                if (mainMenu) mainMenu.classList.add('is-open');
            }
            const stickyMenu = stickyDropdownContainer.querySelector('.mega-menu');
            resetMegaMenu(stickyMenu);
        }
    }

    window.addEventListener('scroll', handleScroll);
    handleScroll();

    // 7. Logic Mobile Offcanvas (Giữ nguyên của bạn vì nó hoạt động tốt)
    const mainOffcanvasElement = document.getElementById('nnpMobileMenu');
    const subOffcanvasElements = document.querySelectorAll('.nnp-sub-offcanvas');

    subOffcanvasElements.forEach(sub => {
        sub.addEventListener('show.bs.offcanvas', () => {
            const mainInstance = bootstrap.Offcanvas.getInstance(mainOffcanvasElement);
            if (mainInstance) mainInstance.hide();
        });

        const backBtn = sub.querySelector('button[id^="backToMainMenu_"], button[id^="backToPreviousMenu_"]');
        if (backBtn) {
            backBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const parentId = backBtn.getAttribute('data-parent-id') || '#nnpMobileMenu';
                bootstrap.Offcanvas.getOrCreateInstance(sub).hide();
                
                sub.addEventListener('hidden.bs.offcanvas', () => {
                    bootstrap.Offcanvas.getOrCreateInstance(document.querySelector(parentId)).show();
                }, { once: true });
            });
        }
    });
});
</script>

</body>
</html>
