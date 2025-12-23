<?php
// We use BASE_URL (defined in config/db.php, included by header.php) to create absolute paths.
// This is more robust than relative paths.
$admin_base_url = rtrim(BASE_URL, '/') . '/app/Views/admin/';
?>

<div class="sidebar offcanvas-lg offcanvas-start d-flex flex-column p-3" tabindex="-1" id="sidebarMenu">
    
    <div class="d-flex align-items-center justify-content-between mb-4 mt-2">
        <a href="<?php echo $admin_base_url; ?>index.php" class="d-flex align-items-center text-dark text-decoration-none">
            <span class="fs-4 fw-bold">Admin Panel</span>
        </a>
        <button type="button" class="btn-close d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu"></button>
    </div>

    <hr>
    
    <div class="offcanvas-body d-flex flex-column p-0">
        <ul class="nav nav-pills flex-column mb-auto">
            
            <li class="nav-item">
                <a href="<?php echo $admin_base_url; ?>index.php" 
                   class="nav-link <?php echo ($current_dir == 'admin') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-house me-2"></i> Trang chủ
                </a>
            </li>

            <li>
                <a href="<?php echo $admin_base_url; ?>cruduser/list_users.php" 
                   class="nav-link <?php echo ($current_dir == 'cruduser') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-users me-2"></i> Quản lý User
                </a>
            </li>

            <li>
                <a href="<?php echo $admin_base_url; ?>crudcategories/list_categories.php" 
                   class="nav-link <?php echo ($current_dir == 'crudcategories') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-tags me-2"></i> Quản lý Danh mục cấp 1
                </a>
            </li>

            <li>
                <a href="<?php echo $admin_base_url; ?>crudproducts/list_products.php" 
                   class="nav-link <?php echo ($current_dir == 'crudproducts') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-box me-2"></i> Quản lý Danh mục cấp 2
                </a>
            </li>

            <li>
                <a href="<?php echo $admin_base_url; ?>crudcategorylevel3/list_category_level3.php" 
                   class="nav-link <?php echo ($current_dir == 'crudcategorylevel3') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-sitemap me-2"></i> Quản lý Danh mục Cấp 3
                </a>
            </li>

            <li>
                <a href="<?php echo $admin_base_url; ?>crudiconmenu/list_icon_menu.php" 
                   class="nav-link <?php echo ($current_dir == 'crudiconmenu') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-icons me-2"></i> Quản lý Icon Menu
                </a>
            </li>

            <li>
                <a href="<?php echo $admin_base_url; ?>crudbanner/list_banner.php" 
                   class="nav-link <?php echo ($current_dir == 'crudbanner') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-images me-2"></i> Quản lý banner
                </a>
            </li>

            <li>
                <a href="<?php echo $admin_base_url; ?>crudposts/list_posts.php" 
                   class="nav-link <?php echo ($current_dir == 'crudposts') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-newspaper me-2"></i> Quản lý bài viết
                </a>
            </li>

            <li>
                <a href="<?php echo $admin_base_url; ?>crudcncategories/list_cn_categories.php" 
                   class="nav-link <?php echo ($current_dir == 'crudcncategories') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-book-open me-2"></i> Quản lý DM Cẩm nang
                </a>
            </li>

            <li>
                <a href="<?php echo $admin_base_url; ?>crudcnposts/list_cn_posts.php" 
                   class="nav-link <?php echo ($current_dir == 'crudcnposts') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-book me-2"></i> Quản lý Cẩm nang
                </a>
            </li>

            <li>
                <a href="<?php echo $admin_base_url; ?>crudproducts_actual/list_products.php" 
                   class="nav-link <?php echo ($current_dir == 'crudproducts_actual') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-box-open me-2"></i> Quản lý Sản phẩm
                </a>
            </li>

            <li>
                <a href="<?php echo $admin_base_url; ?>crudorders/list_orders.php" 
                   class="nav-link <?php echo ($current_dir == 'crudorders') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-receipt me-2"></i> Quản lý Đơn hàng
                </a>
            </li>

            <li>
                <a href="<?php echo $admin_base_url; ?>crudprovinces/list_provinces.php" 
                   class="nav-link <?php echo ($current_dir == 'crudprovinces') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-city me-2"></i> Quản lý Tỉnh/Thành phố
                </a>
            </li>
            <li>
                <a href="<?php echo $admin_base_url; ?>crudwards/list_wards.php" 
                   class="nav-link <?php echo ($current_dir == 'crudwards') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-road me-2"></i> Quản lý Xã/Phường
                </a>
            </li>
            <li>
                <a href="<?php echo $admin_base_url; ?>crudstores/list_stores.php" 
                   class="nav-link <?php echo ($current_dir == 'crudstores') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-store me-2"></i> Quản lý Cửa hàng
                </a>
            </li>

        </ul>
        
        <hr class="mt-auto">
        
        <a href="<?php echo $admin_base_url; ?>Auth/logout.php" class="text-danger text-decoration-none p-2">
            <i class="fa-solid fa-right-from-bracket me-2"></i> Đăng xuất
        </a>
    </div>
</div>
