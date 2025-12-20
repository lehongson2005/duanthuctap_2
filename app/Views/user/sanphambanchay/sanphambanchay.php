<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản phẩm bán chạy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* CSS Tùy Chỉnh Cho Trang Sản Phẩm Bán Chạy (Sử dụng CSS nhất quán) */
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

        /* --- Header/Footer Giả Định --- */
        .fake-header, .fake-footer {
            width: 100%;
            background-color: #333;
            color: white;
            padding: 15px 0;
            text-align: center;
            font-size: 0.9rem;
        }
        .fake-header {
            background-color: var(--primary-color);
        }
        
        /* --- Tiêu đề Trang --- */
        h1.page-title {
            color: var(--text-dark);
            font-weight: 700;
            margin-bottom: 25px;
            padding-bottom: 5px;
            border-bottom: 3px solid var(--primary-color);
            display: inline-block;
        }
        .section-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 20px;
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
        .product-card .price-new {
            color: var(--primary-color);
            font-size: 1.1rem;
            font-weight: bold;
            margin-right: 10px;
        }
        .product-card .price-old {
            color: var(--text-muted);
            text-decoration: line-through;
            font-size: 0.85rem;
        }
        .product-card .product-name {
            font-size: 0.95rem;
            min-height: 40px; 
            margin-bottom: 10px;
            color: var(--text-dark);
            font-weight: 500;
        }
        
        /* Icon tags bên trái (image_09db22.jpg) */
        .product-icon-list {
            list-style: none;
            padding: 0;
            margin: 0;
            position: absolute;
            top: 5px;
            left: 5px;
            z-index: 10;
        }
        .product-icon-list li {
            width: 30px;
            height: 30px;
            margin-bottom: 5px;
            background-color: white;
            border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .product-icon-list i {
            color: var(--primary-color);
            font-size: 0.8rem;
        }

        /* Thẻ Công ty/Sản phẩm chính hãng bên phải */
        .company-tag {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: var(--primary-color);
            color: white;
            padding: 2px 5px;
            font-size: 0.65rem;
            border-radius: 3px;
            font-weight: 600;
        }

        /* --- Button Add to Cart --- */
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

        /* --- Hộp Phân Trang --- */
        .pagination {
            justify-content: center;
            margin-top: 40px;
        }

    </style>

</head>
<body>

<?php include '../header.php'; ?>

<div class="container my-5">
    
    <div class="row">
        <div class="col-12">
            <h1 class="section-title">Sản phẩm bán chạy</h1>
        </div>
    </div>

    <div class="sort-bar d-flex align-items-center">
        <span class="fw-bold me-3">Sắp xếp:</span>
        <a href="#" class="sort-link">Giá tăng dần</a>
        <a href="#" class="sort-link">Giá giảm dần</a>
        <a href="#" class="sort-link active">Hàng mới</a>
    </div>
    
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
        
        <div class="col">
            <a href="#" class="text-decoration-none text-dark h-100">
                <div class="product-card d-flex flex-column">
                    <div class="position-relative text-center">
                        <ul class="product-icon-list">
                            <li><i class="fas fa-headset"></i></li>
                            <li><i class="fas fa-award"></i></li>
                            <li><i class="fas fa-leaf"></i></li>
                        </ul>
                        <span class="company-tag">SẢN PHẨM CHÍNH HÃNG</span>
                        <img src="https://via.placeholder.com/300x200?text=Vien+nen+xo+dua" alt="Viên nén xơ dừa ươm hạt">
                    </div>
                    <div class="product-card-body flex-grow-1">
                        <div class="product-name">Viên nén xơ dừa ươm hạt</div>
                        <div class="price-group">
                            <span class="price-new">1,000₫</span>
                            <button class="btn btn-add-cart ms-auto"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col">
            <a href="#" class="text-decoration-none text-dark h-100">
                <div class="product-card d-flex flex-column">
                    <div class="position-relative text-center">
                        <ul class="product-icon-list">
                            <li><i class="fas fa-headset"></i></li>
                            <li><i class="fas fa-award"></i></li>
                            <li><i class="fas fa-leaf"></i></li>
                        </ul>
                        <span class="company-tag">SẢN PHẨM CHÍNH HÃNG</span>
                        <img src="https://via.placeholder.com/300x200?text=Dat+sach+Tribat" alt="Đất sạch Tribat">
                    </div>
                    <div class="product-card-body flex-grow-1">
                        <div class="product-name">Đất sạch Tribat trồng cây giàu dinh dưỡng (Bao 50dm³)</div>
                        <div class="price-group">
                            <span class="price-old">66,000₫</span>
                            <span class="price-new">11,000₫</span>
                            <button class="btn btn-add-cart ms-auto"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col">
            <a href="#" class="text-decoration-none text-dark h-100">
                <div class="product-card d-flex flex-column">
                    <div class="position-relative text-center">
                        <ul class="product-icon-list">
                            <li><i class="fas fa-headset"></i></li>
                            <li><i class="fas fa-award"></i></li>
                            <li><i class="fas fa-leaf"></i></li>
                        </ul>
                        <span class="company-tag">SẢN PHẨM CHÍNH HÃNG</span>
                        <img src="https://via.placeholder.com/300x200?text=EMUNIV" alt="EMUNIV">
                    </div>
                    <div class="product-card-body flex-grow-1">
                        <div class="product-name">Chế phẩm EMUNIV (200g) – Ủ phân và rác hữu cơ hiệu quả</div>
                        <div class="price-group">
                            <span class="price-new">38,000₫</span>
                            <button class="btn btn-add-cart ms-auto"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col">
            <a href="#" class="text-decoration-none text-dark h-100">
                <div class="product-card d-flex flex-column">
                    <div class="position-relative text-center">
                        <ul class="product-icon-list">
                            <li><i class="fas fa-headset"></i></li>
                            <li><i class="fas fa-award"></i></li>
                            <li><i class="fas fa-leaf"></i></li>
                        </ul>
                        <span class="company-tag">SẢN PHẨM CHÍNH HÃNG</span>
                        <img src="https://via.placeholder.com/300x200?text=N3M" alt="N3M kích rễ">
                    </div>
                    <div class="product-card-body flex-grow-1">
                        <div class="product-name">N3M kích rễ - Kích rễ cực mạnh cho cây ăn trái, kiểng...</div>
                        <div class="price-group">
                            <span class="price-new">28,000₫</span>
                            <button class="btn btn-add-cart ms-auto"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col">
            <a href="#" class="text-decoration-none text-dark h-100">
                <div class="product-card d-flex flex-column">
                    <div class="position-relative text-center">
                        <ul class="product-icon-list">
                            <li><i class="fas fa-headset"></i></li>
                            <li><i class="fas fa-award"></i></li>
                            <li><i class="fas fa-leaf"></i></li>
                        </ul>
                        <span class="company-tag">SẢN PHẨM CHÍNH HÃNG</span>
                        <img src="https://via.placeholder.com/300x200?text=Trichoderma" alt="Trichoderma Humic Plus SFARM">
                    </div>
                    <div class="product-card-body flex-grow-1">
                        <div class="product-name">Chế phẩm Trichoderma Humic Plus SFARM (1kg)...</div>
                        <div class="price-group">
                            <span class="price-new">89,000₫</span>
                            <button class="btn btn-add-cart ms-auto"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col">
            <a href="#" class="text-decoration-none text-dark h-100">
                <div class="product-card d-flex flex-column">
                    <div class="position-relative text-center">
                        <ul class="product-icon-list">
                            <li><i class="fas fa-headset"></i></li>
                            <li><i class="fas fa-award"></i></li>
                            <li><i class="fas fa-leaf"></i></li>
                        </ul>
                        <span class="company-tag">SẢN PHẨM CHÍNH HÃNG</span>
                        <img src="https://via.placeholder.com/300x200?text=Trichoderma+Goi" alt="Trichoderma Gói">
                    </div>
                    <div class="product-card-body flex-grow-1">
                        <div class="product-name">Chế phẩm Trichoderma (Gói)</div>
                        <div class="price-group">
                            <span class="price-new">20,000₫</span>
                            <button class="btn btn-add-cart ms-auto"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col">
            <a href="#" class="text-decoration-none text-dark h-100">
                <div class="product-card d-flex flex-column">
                    <div class="position-relative text-center">
                        <ul class="product-icon-list">
                            <li><i class="fas fa-headset"></i></li>
                            <li><i class="fas fa-award"></i></li>
                            <li><i class="fas fa-leaf"></i></li>
                        </ul>
                        <span class="company-tag">SẢN PHẨM CHÍNH HÃNG</span>
                        <img src="https://via.placeholder.com/300x200?text=Run+Que+Vien+Nen" alt="Rụn Quế Viên Nén">
                    </div>
                    <div class="product-card-body flex-grow-1">
                        <div class="product-name">Phân trùn quế viên nén SFARM (2kg)</div>
                        <div class="price-group">
                            <span class="price-new">45,000₫</span>
                            <button class="btn btn-add-cart ms-auto"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col">
            <a href="#" class="text-decoration-none text-dark h-100">
                <div class="product-card d-flex flex-column">
                    <div class="position-relative text-center">
                        <ul class="product-icon-list">
                            <li><i class="fas fa-headset"></i></li>
                            <li><i class="fas fa-award"></i></li>
                            <li><i class="fas fa-leaf"></i></li>
                        </ul>
                        <span class="company-tag">SẢN PHẨM CHÍNH HÃNG</span>
                        <img src="https://via.placeholder.com/300x200?text=ATONIK+Goi" alt="ATONIK Gói">
                    </div>
                    <div class="product-card-body flex-grow-1">
                        <div class="product-name">ATONIK (Gói) - Kích chồi mầm, dưỡng hoa</div>
                        <div class="price-group">
                            <span class="price-new">2,500₫</span>
                            <button class="btn btn-add-cart ms-auto"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col">
            <a href="#" class="text-decoration-none text-dark h-100">
                <div class="product-card d-flex flex-column">
                    <div class="position-relative text-center">
                        <ul class="product-icon-list">
                            <li><i class="fas fa-headset"></i></li>
                            <li><i class="fas fa-award"></i></li>
                            <li><i class="fas fa-leaf"></i></li>
                        </ul>
                        <span class="company-tag">CORTENA</span>
                        <img src="https://via.placeholder.com/300x200?text=Radiant" alt="Radiant 60SC">
                    </div>
                    <div class="product-card-body flex-grow-1">
                        <div class="product-name">Thuốc trừ sâu Radiant 60SC (Gói 3ml)</div>
                        <div class="price-group">
                            <span class="price-new">15,000₫</span>
                            <button class="btn btn-add-cart ms-auto"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col">
            <a href="#" class="text-decoration-none text-dark h-100">
                <div class="product-card d-flex flex-column">
                    <div class="position-relative text-center">
                        <ul class="product-icon-list">
                            <li><i class="fas fa-headset"></i></li>
                            <li><i class="fas fa-award"></i></li>
                            <li><i class="fas fa-leaf"></i></li>
                        </ul>
                        <span class="company-tag">SẢN PHẨM CHÍNH HÃNG</span>
                        <img src="https://via.placeholder.com/300x200?text=Sieu+Lan" alt="Phân bón lá siêu lân KALI">
                    </div>
                    <div class="product-card-body flex-grow-1">
                        <div class="product-name">Phân bón lá siêu lân KALI (Chai 500ml)</div>
                        <div class="price-group">
                            <span class="price-new">88,000₫</span>
                            <button class="btn btn-add-cart ms-auto"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
    </div>
    
    <nav aria-label="Phân trang sản phẩm">
        <ul class="pagination justify-content-center mt-4">
            <li class="page-item disabled">
                <a class="page-link" href="#" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item">
                <a class="page-link" href="#" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>

</div>

<?php include '../footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>