<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chính Sách Vận Chuyển, Giao Nhận</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* CSS Tùy Chỉnh Cho Trang Chính Sách */
        :root {
            --primary-color: #238E46; /* Màu xanh lá cây nổi bật */
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

        .policy-container {
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        /* --- Header & Breadcrumb --- */
        .breadcrumb-item a {
            color: var(--primary-color) !important;
        }

        h1.policy-title {
            color: var(--text-dark);
            font-weight: 700;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 3px solid var(--primary-color);
            display: inline-block;
        }
        
        /* --- Nội dung chính sách --- */
        .policy-section h2 {
            color: var(--text-dark);
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 25px;
            margin-bottom: 15px;
            border-left: 5px solid var(--primary-color);
            padding-left: 10px;
        }

        /* --- Bảng Vận Chuyển --- */
        .shipping-table {
            font-size: 0.9rem;
        }
        .shipping-table th {
            background-color: #238E46;
            color: white;
            vertical-align: middle;
            text-align: center;
        }
        .shipping-table td {
            vertical-align: middle;
            text-align: center;
        }
        .shipping-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .shipping-table .area-list {
            text-align: left;
            padding-left: 15px;
        }
        .shipping-table .area-list p {
            margin-bottom: 2px;
        }
    </style>

</head>
<body>
<?php include '../header.php'; ?>


<div class="container my-5">
    
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>" class="text-success">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chính sách</li>
                </ol>
            </nav>
            <h1 class="policy-title">CHÍNH SÁCH VẬN CHUYỂN, GIAO NHẬN</h1>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12 policy-container">
            
            <div class="policy-section">
                <h2>1. Giao Hàng Tận Nhà Tại TP. Hồ Chí Minh</h2>
                <p>Cửa hàng Nông Nghiệp Phố giao hàng tận nhà cho quý khách hàng có địa chỉ tại TPHCM. Phí giao hàng được áp dụng theo biểu phí hiện hành của Nông Nghiệp Phố.</p>
                
                <div class="table-responsive mt-3">
                    <table class="table table-bordered shipping-table">
                        <thead>
                            <tr>
                                <th rowspan="2">Giá trị đơn hàng</th>
                                <th>Khu vực 1</th>
                                <th>Khu vực 2</th>
                                <th>Khu vực 3</th>
                                <th>Khu vực 4</th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-start p-1 fw-normal bg-secondary">Cước phí (VNĐ)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>< 200.000 VNĐ</td>
                                <td>15.000</td>
                                <td>15.000</td>
                                <td>25.000</td>
                                <td rowspan="4">Theo phí bưu điện, gửi chành xe</td>
                            </tr>
                            <tr>
                                <td>200.000 VNĐ</td>
                                <td>Free</td>
                                <td>15.000</td>
                                <td>25.000</td>
                            </tr>
                            <tr>
                                <td>400.000 VNĐ</td>
                                <td>Free</td>
                                <td>Free</td>
                                <td>15.000</td>
                            </tr>
                            <tr>
                                <td>600.000 VNĐ</td>
                                <td>Free</td>
                                <td>Free</td>
                                <td>Free</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Khu vực</td>
                                <td class="area-list">
                                    <p>Tân Bình</p>
                                    <p>Bình Thạnh</p>
                                    <p>Quận 7</p>
                                </td>
                                <td class="area-list">
                                    <p>Quận 1,3,4,5,6,8,10</p>
                                    <p>Phú Nhuận</p>
                                    <p>Bình Tân</p>
                                    <p>Quận 11</p>
                                    <p>Quận 12</p>
                                    <p>Tân Phú</p>
                                    <p>Gò Vấp</p>
                                    <p>Thủ Đức</p>
                                </td>
                                <td class="area-list">
                                    <p>Hóc Môn</p>
                                    <p>Bình Chánh</p>
                                    <p>Nhà Bè</p>
                                </td>
                                <td class="area-list">
                                    <p>Cần Giờ</p>
                                    <p>Củ Chi</p>
                                    <p>Các tỉnh khác</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
            
            <div class="policy-section">
                <h2>2. Giao Hàng Tận Nhà Tại Hà Nội</h2>
                <p>Nội dung chi tiết về chính sách giao hàng tại Hà Nội...</p>
            </div>
            
        </div>
    </div>
    
</div>
<?php include '../footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>