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
            <h1 class="policy-title">CHÍNH SÁCH & QUY ĐỊNH CHUNG
</h1>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12 policy-container">
            <p>Bao gồm các điều kiện hạn chế, tiêu chuẩn dịch vụ, quy trình, quy định.



</p>
<p>1. QUY ĐỊNH VÀ HÌNH THỨC THANH TOÁN</p>
<p>2. CHÍNH SÁCH BẢO MẬT THÔNG TIN</p>
<p>3. CHÍNH SÁCH VẬN CHUYỂN, GIAO NHẬN</p>
    </div>
    </div>
    
</div>
<?php include '../footer.php'; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>