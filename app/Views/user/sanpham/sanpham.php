<?php
include_once '../../../../app/config/db.php';
include_once '../../../../app/models/ProductModel.php';
include_once '../../../../app/models/CategoryModel.php';
include_once '../../../../app/models/CategoryLevel2Model.php';
include_once '../../../../app/models/CategoryLevel3Model.php';

$productModel = new ProductModel($conn);
$categoryModel = new CategoryModel($conn);
$categoryLevel2Model = new CategoryLevel2Model($conn);
$categoryLevel3Model = new CategoryLevel3Model($conn);

// Pagination settings
$limit = 10; // Số sản phẩm mỗi trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filter parameters (can be extended with actual category IDs if needed)
$keyword = $_GET['keyword'] ?? '';
$category_level1_id = $_GET['category_level1_id'] ?? '';
$category_level2_id = $_GET['category_level2_id'] ?? '';
$category_level3_id = $_GET['category_level3_id'] ?? '';
$status = 1; // Always show active products on frontend

// Sorting parameters
$sort_by = $_GET['sort_by'] ?? 'newest'; // 'price_asc', 'price_desc', 'newest'

// Fetch products
$products_result = $productModel->searchAndFilter(
    $keyword, 
    $category_level1_id, 
    $category_level2_id, 
    $category_level3_id, 
    $status, 
    $limit, 
    $offset
);
$products = ($products_result) ? $products_result->fetch_all(MYSQLI_ASSOC) : [];

// Get total for pagination
$total_products = $productModel->getTotal(
    $keyword, 
    $category_level1_id, 
    $category_level2_id, 
    $category_level3_id, 
    $status
);
$total_pages = ceil($total_products / $limit);

// Fetch categories for breadcrumb or filter (if needed)
$current_category1 = null;
if (!empty($category_level1_id)) {
    $current_category1 = $categoryModel->getById($category_level1_id);
}
$current_category2 = null;
if (!empty($category_level2_id)) {
    $current_category2 = $categoryLevel2Model->getById($category_level2_id);
}
$current_category3 = null;
if (!empty($category_level3_id)) {
    $current_category3 = $categoryLevel3Model->getById($category_level3_id);
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tất cả sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* CSS Tùy Chỉnh Cho Trang Sản Phẩm */
        :root {
            --primary-color: #238E46; /* Màu xanh lá cây nổi bật */
            --secondary-color: #4CAF50;
            --text-dark: #333;
            --text-muted: #6c757d;
            --border-light: #eee;
        }

        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 1200px;
        }

        h1.page-title {
            color: var(--text-dark);
            font-weight: 700;
            margin-bottom: 25px;
            padding-bottom: 5px;
            border-bottom: 3px solid var(--primary-color);
            display: inline-block;
        }

        /* --- Thanh Sắp Xếp --- */
        .sort-bar {
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border-light);
            padding-bottom: 10px;
        }
        .sort-bar .sort-link {
            font-weight: 500;
            color: var(--text-muted);
            text-decoration: none;
            margin-right: 15px;
            transition: color 0.2s;
            font-size: 0.95rem;
        }
        .sort-bar .sort-link:hover, .sort-bar .sort-link.active {
            color: var(--primary-color);
            font-weight: bold;
        }

        /* --- Product Card (Sản phẩm) --- */
        .product-card {
            border: 1px solid var(--border-light);
            border-radius: 8px;
            background-color: white;
            overflow: hidden;
            transition: box-shadow 0.3s ease, transform 0.3s ease;
            height: 100%;
        }
        .product-card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        .product-card-body {
            padding: 15px;
            text-align: center;
        }
        .product-card img {
            height: 180px;
            object-fit: contain;
            width: 100%;
            display: block;
            border-bottom: 1px solid #f9f9f9;
            padding: 10px;
        }
        .product-card .price-old {
            color: var(--text-muted);
            text-decoration: line-through;
            font-size: 0.85rem;
        }
        .product-card .price-new {
            color: var(--primary-color);
            font-size: 1.1rem;
            font-weight: bold;
            margin-right: 10px;
        }
        .product-card .product-name {
            font-size: 0.95rem;
            min-height: 40px; /* Đảm bảo chiều cao đồng nhất */
            margin-bottom: 10px;
            color: var(--text-dark);
            font-weight: 500;
        }
        
        /* Overlay Tag (Màu xanh lá) */
        .tag-overlay {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: var(--primary-color);
            color: white;
            padding: 2px 8px;
            font-size: 0.75rem;
            border-radius: 4px;
            font-weight: 600;
        }
        
        /* Button Add to Cart */
        .btn-add-cart {
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: background-color 0.2s;
        }
        .btn-add-cart:hover {
            background-color: var(--secondary-color);
        }
        .price-group {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 10px;
        }

        .product-card:hover .product-hover-img {
            opacity: 1 !important;
        }
        .product-card .product-hover-img {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        /* --- Style Phân Trang --- */
        .pagination .page-link {
            color: var(--text-dark);
            border-radius: 4px;
            margin: 0 3px;
        }
        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        .pagination {
            justify-content: center;
            margin-top: 40px;
        }
    </style>

</head>
<body>

<?php include '../header.php'; ?>



<div class="container my-5">
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>" class="text-success">Trang chủ</a></li>
        <?php if ($current_category1): ?>
            <li class="breadcrumb-item"><a href="?category_level1_id=<?php echo $current_category1['id']; ?>" class="text-success"><?php echo htmlspecialchars($current_category1['name']); ?></a></li>
        <?php endif; ?>
        <?php if ($current_category2): ?>
            <li class="breadcrumb-item"><a href="?category_level2_id=<?php echo $current_category2['id']; ?>" class="text-success"><?php echo htmlspecialchars($current_category2['name']); ?></a></li>
        <?php endif; ?>
        <?php if ($current_category3): ?>
            <li class="breadcrumb-item"><a href="?category_level3_id=<?php echo $current_category3['id']; ?>" class="text-success"><?php echo htmlspecialchars($current_category3['name']); ?></a></li>
        <?php endif; ?>
        <li class="breadcrumb-item active" aria-current="page">Tất cả sản phẩm</li>
    </ol>
</nav>

<?php include '../trangchu/icon.php'; ?>


    
    <div class="row">
        <div class="col-12">
            <h1 class="page-title">Tất cả sản phẩm</h1>
        </div>
    </div>

<?php
    $base_query_params = array_filter([
        'keyword' => $keyword,
        'category_level1_id' => $category_level1_id,
        'category_level2_id' => $category_level2_id,
        'category_level3_id' => $category_level3_id,
    ]);

    function build_sort_link($current_sort_by, $sort_param, $text, $base_params) {
        $params = array_merge($base_params, ['sort_by' => $sort_param]);
        $query_string = http_build_query($params);
        $active_class = ($current_sort_by == $sort_param) ? 'active' : '';
        return "<a href=\"?" . htmlspecialchars($query_string) . "\" class=\"sort-link {$active_class}\">{$text}</a>";
    }
    ?>
    <div class="sort-bar d-flex align-items-center">
        <span class="fw-bold me-3">Sắp xếp:</span>
        <?php
        echo build_sort_link($sort_by, 'price_asc', 'Giá tăng dần', $base_query_params);
        echo build_sort_link($sort_by, 'price_desc', 'Giá giảm dần', $base_query_params);
        echo build_sort_link($sort_by, 'newest', 'Hàng mới', $base_query_params);
        ?>
    </div>
    
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="col">
                    <a href="chitietsanpham.php?id=<?php echo $product['id']; ?>" class="text-decoration-none text-dark h-100">
                        <div class="product-card d-flex flex-column">
                            <div class="position-relative text-center">
                                <img src="<?php echo BASE_URL . '/' . htmlspecialchars($product['thumbnail'] ?? 'public/uploads/default.png'); ?>" 
                                     class="img-fluid" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php if (!empty($product['image_hover'])): ?>
                                    <img src="<?php echo BASE_URL . '/' . htmlspecialchars($product['image_hover']); ?>" 
                                         class="img-fluid product-hover-img" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                         style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain; opacity: 0; transition: opacity 0.3s ease;">
                                <?php endif; ?>
                                <?php if ($product['is_new'] == 1): ?>
                                    <span class="tag-overlay">Hàng mới</span>
                                <?php elseif ($product['discount_price'] < $product['price']): ?>
                                    <span class="tag-overlay">GIẢM SỐC</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-card-body flex-grow-1">
                                <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                                <div class="price-group">
                                    <?php if ($product['discount_price'] < $product['price']): ?>
                                        <span class="price-old"><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</span>
                                        <span class="price-new"><?php echo number_format($product['discount_price'], 0, ',', '.'); ?>₫</span>
                                    <?php else: ?>
                                        <span class="price-new"><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</span>
                                    <?php endif; ?>
                                    <button class="btn btn-add-cart ms-auto"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">Không có sản phẩm nào.</div>
        <?php endif; ?>
    </div>
    
    <nav aria-label="Phân trang sản phẩm">
        <ul class="pagination">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?<?php echo http_build_query(array_merge($base_query_params, ['page' => $page - 1, 'sort_by' => $sort_by])); ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php else: ?>
                <li class="page-item disabled">
                    <a class="page-link" href="#" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?<?php echo http_build_query(array_merge($base_query_params, ['page' => $i, 'sort_by' => $sort_by])); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?<?php echo http_build_query(array_merge($base_query_params, ['page' => $page + 1, 'sort_by' => $sort_by])); ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php else: ?>
                <li class="page-item disabled">
                    <a class="page-link" href="#" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

</div>

<?php include '../footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>