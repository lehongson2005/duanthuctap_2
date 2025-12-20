<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiểm tra đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* CSS Tùy Chỉnh Cho Trang Kiểm Tra Đơn Hàng */
     
        .check-order-box {
            background: linear-gradient(to bottom, #00c6ff, #00a0e5); /* Gradient màu xanh dương */
            color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
            margin-top: 20px;
        }

        h2.box-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 10px;
        }

        /* --- Input Fields & Radio Buttons --- */
        .form-check-label, .form-label {
            font-weight: 500;
        }
        .form-check-input:checked {
            background-color: white;
            border-color: white;
            box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.5);
        }
        .form-control {
            border-radius: 5px;
            border: none;
            padding: 10px 15px;
        }
        .form-label {
            margin-bottom: 8px;
            margin-top: 15px;
        }

        /* --- reCAPTCHA Placeholder --- */
        .recaptcha-placeholder {
            background-color: white;
            color: #333;
            border: 1px solid #ccc;
            border-radius: 3px;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 0.85rem;
            position: relative;
        }
        .recaptcha-text {
            line-height: 1.3;
        }
        .recaptcha-logo {
            width: 30px;
            height: 30px;
        }
        .recaptcha-checkbox {
            width: 25px;
            height: 25px;
            border: 2px solid #ccc;
            margin-right: 10px;
            flex-shrink: 0;
            background-color: #f9f9f9;
        }

        /* --- Button --- */
        .btn-check-now {
            background-color: #ff9800; /* Màu cam */
            border-color: #ff9800;
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            transition: background-color 0.2s;
        }
        .btn-check-now:hover {
            background-color: #e68a00;
            border-color: #e68a00;
            color: white;
        }
        
        /* Cấu trúc chứa chính */
        .main-page-content {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
    </style>

</head>
<body>


<?php include '../header.php'; ?>


<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 text-start mb-3">
            <h1 class="text-dark fw-bold">Kiểm tra đơn hàng</h1>
        </div>
        <div class="col-md-7 col-lg-5 main-page-content">
            
            <div class="check-order-box">
                <h2 class="box-title"><i class="fas fa-search"></i> Kiểm tra đơn hàng của bạn</h2>

                <div class="recaptcha-placeholder">
                    <div class="d-flex align-items-center">
                        <div class="recaptcha-checkbox"></div>
                        <span class="recaptcha-text">Tôi không phải là người máy<br><a href="#" class="text-decoration-none text-muted" style="font-size:0.75rem;">Điều khoản dịch vụ</a></span>
                    </div>
                    <div class="text-end">
                        <img src="https://via.placeholder.com/30x30?text=R" class="recaptcha-logo" alt="reCAPTCHA">
                         <p style="font-size:0.6rem; color:#888; margin-top:2px;">reCAPTCHA<br>Bảo mật - Điều khoản</p>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Phương thức kiểm tra</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="checkMethod" id="radioPhone" value="phone" checked>
                            <label class="form-check-label" for="radioPhone">Số điện thoại</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="checkMethod" id="radioEmail" value="email">
                            <label class="form-check-label" for="radioEmail">Email</label>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="inputPhone" class="form-label">Số điện thoại:</label>
                    <input type="text" class="form-control" id="inputPhone" placeholder="0909 xxxx xxx">
                </div>
                
                <div class="text-center">
                    <p class="small mb-2">Nếu quý khách có bất kỳ thắc mắc nào, xin vui lòng gọi <span class="fw-bold fs-6">0865 399 086</span></p>
                    <button type="submit" class="btn btn-check-now">Xem ngay</button>
                </div>
                
            </div>
            
        </div>
    </div>
</div>


 <?php include '../footer.php'; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>