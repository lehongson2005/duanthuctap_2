<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chính Sách Đặt Cọc và Hình Thức Thanh Toán</title>
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
            max-width: 1000px;
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
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 25px;
            margin-bottom: 15px;
        }

        .payment-method h3 {
            color: var(--text-dark);
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .bank-info {
            background-color: #f4fcf4; /* Nền xanh nhạt cho thông tin ngân hàng */
            border: 1px solid #d9edd9;
            padding: 15px;
            border-radius: 6px;
        }
        .bank-info p {
            margin-bottom: 5px;
            font-size: 0.95rem;
        }
        .bank-info span {
            font-weight: bold;
            color: var(--primary-color);
        }

        .note-content {
            background-color: #fff3e0; /* Nền vàng nhạt cho lưu ý */
            border-left: 5px solid #ff9800;
            padding: 10px 15px;
            margin-top: 15px;
            font-style: italic;
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
            <h1 class="policy-title">CHÍNH SÁCH ĐẶT CỌC VÀ HÌNH THỨC THANH TOÁN</h1>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12 policy-container">
            <p>Để thuận tiện cho việc giao nhận hàng và chuyển hàng đến quý khách được nhanh chóng. Cửa Hàng Nông Nghiệp Phố gửi đến quý khách hàng các hình thức thanh toán như sau:</p>
            
            <div class="policy-section">
                <h2>I/ HÌNH THỨC THANH TOÁN</h2>
                
                <div class="payment-method mb-4">
                    <h3>1. Thanh toán trực tiếp</h3>
                    <p>Đối với khách hàng mua hàng tại cửa hàng, khách hàng có thể thanh toán trực tiếp bằng tiền mặt cho cửa hàng.</p>
                </div>
                
                <div class="payment-method mb-4">
                    <h3>2. Thanh toán chuyển khoản ngân hàng – giao hàng qua dịch vụ chuyển phát nhanh</h3>
                    <p>Theo quy định và chính sách của Nông Nghiệp Phố. Khách hàng bắt buộc chuyển khoản trước.</p>
                    <p>Nông Nghiệp Phố xin cung cấp số tài khoản công ty cũng như tài khoản cá nhân đại diện cho công ty như sau:</p>
                    
                    <div class="bank-info mt-3">
                        <p class="mb-2 fw-bold">Nông Nghiệp Phố - Hồ Chí Minh</p>
                        <p>+ Tên ngân hàng: <span>Techcombank – chi nhánh Tân Bình</span></p>
                        <p>+ Chủ tài khoản: <span>Công ty Cổ phần P3T</span></p>
                        <p>+ Số tài khoản: <span>19039934469024</span></p>
                    </div>

                    <div class="note-content mt-3">
                        <p><span class="fw-bold text-danger">* Nội dung:</span> [tên khách hàng] thanh toán tiền cho [mã đơn hàng] hoặc [số điện thoại quý khách]</p>
                        <p><span class="fw-bold">[mã đơn hàng]:</span> áp dụng cho khách đặt hàng qua website.</p>
                        <p><span class="fw-bold">[số điện thoại quý khách]:</span> Khách hàng đặt qua hotline và các kênh bán hàng khác của Nông Nghiệp Phố.</p>
                    </div>

                </div>
                
                </div>
        </div>
    </div>
    
</div>
<?php include '../footer.php'; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>