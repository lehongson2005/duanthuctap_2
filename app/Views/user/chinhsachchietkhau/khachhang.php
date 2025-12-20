<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chương Trình Tích Điểm Khách Hàng Thân Thiết</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* CSS Tùy Chỉnh Cho Trang Thông Báo Chính Sách */
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
            border-left: 5px solid var(--primary-color);
            padding-left: 10px;
        }
        
        /* Tiêu đề mục nhỏ */
        .policy-section h3 {
            color: var(--text-dark);
            font-size: 1.2rem;
            font-weight: 600;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        
        .policy-section p, .policy-section li {
            line-height: 1.8;
            color: var(--text-dark);
            font-size: 0.95rem;
        }

        .highlight-text {
            font-weight: bold;
            color: var(--primary-color);
        }

        .reward-info-box {
            background-color: #f4fcf4; /* Nền xanh nhạt */
            border: 1px solid #d9edd9;
            padding: 20px;
            border-radius: 6px;
            margin-top: 15px;
        }

        .store-list h4 {
            color: var(--secondary-color);
            font-weight: 700;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .store-list ul {
            list-style: none;
            padding-left: 0;
        }
        .store-list li {
            margin-bottom: 5px;
            font-size: 0.9rem;
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
                    <li class="breadcrumb-item active" aria-current="page">Chương trình tích điểm</li>
                </ol>
            </nav>
            <h1 class="policy-title">CHƯƠNG TRÌNH TÍCH ĐIỂM KHÁCH HÀNG THÂN THIẾT</h1>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12 policy-container">
            
            <p class="lead fw-bold text-center mb-4">
                Thông báo: Chương Trình Tích Điểm Khách Hàng Thân Thiết Dành Cho Khách Mua Hàng Tại Nông Nghiệp Phố
            </p>
            
            <p class="text-center fst-italic">
                Áp dụng từ ngày <span class="highlight-text">01.12.2023</span>.
            </p>

            <div class="policy-section">
                <h2>1. Đối tượng áp dụng</h2>
                <ul>
                    <li>Chỉ áp dụng cho các <span class="highlight-text">khách hàng cá nhân</span>.</li>
                    <li>Không áp dụng cho khách hàng bán buôn hoặc mua số lượng lớn phục vụ cho doanh nghiệp.</li>
                </ul>
            </div>

            <div class="policy-section">
                <h2>2. Phạm vi áp dụng</h2>
                <ul>
                    <li>Khách hàng mua trực tiếp tại <span class="highlight-text">hệ thống cửa hàng Nông Nghiệp Phố trên toàn quốc</span>.</li>
                    <li>Khách hàng mua hàng trên các <span class="highlight-text">kênh bán hàng trực tuyến chính thức</span> của hệ thống Nông Nghiệp Phố.</li>
                </ul>
            </div>

            <div class="policy-section">
                <h2>3. Quy cách tích điểm và quy đổi thành tiền</h2>
                <div class="reward-info-box">
                    <ul>
                        <li>Khi quý khách mua hàng thành công, cứ với <span class="highlight-text">50.000 VND</span> sẽ được tích lũy <span class="highlight-text">1 điểm</span> mua hàng.</li>
                        <li>Điểm tích lũy sẽ có giá trị quy đổi thành tiền giảm giá trực tiếp cho đơn hàng với mức quy đổi là <span class="highlight-text">1 điểm = 500 VND</span>.</li>
                        <li>Sau khi khách hàng tiến hành sử dụng điểm, Nông Nghiệp Phố sẽ khấu trừ các điểm thưởng đã được tích trong hệ thống và các hóa đơn sau đó sẽ phải tích lại từ đầu.</li>
                        <li>Số điểm được tích lũy sẽ được cộng dồn và <span class="highlight-text">không bị mất</span> nếu không sử dụng.</li>
                    </ul>
                </div>
            </div>

            <div class="policy-section">
                <h2>4. Các hạng điểm tích lũy</h2>
                <ul>
                    <li><i class="fas fa-certificate text-secondary"></i> Hạng bạc: <span class="highlight-text">200 điểm</span></li>
                    <li><i class="fas fa-star text-warning"></i> Hạng vàng: <span class="highlight-text">500 điểm</span></li>
                    <li><i class="fas fa-gem text-info"></i> Hạng kim cương: <span class="highlight-text">1000 điểm</span></li>
                </ul>
            </div>
            
            <div class="policy-section">
                <h2>5. Lưu ý & Liên hệ</h2>
                <ul>
                    <li>Thể lệ và thời gian diễn ra chương trình có thể được thay đổi mà không cần thông báo trước.</li>
                    <li>Tất cả các thắc mắc và khiếu nại về chương trình, vui lòng liên hệ với chúng tôi qua: <br>
                        Hotline: <span class="highlight-text">0865 399 086</span> (miễn cước).</li>
                </ul>
            </div>

            <hr class="my-4">
            
            <div class="text-center mb-4">
                <p class="mb-1 fw-bold">Nông Nghiệp Phố - chuỗi cửa hàng cung cấp vật tư làm vườn, trồng rau và hoa kiểng nơi phố thị.</p>
                <p>➤ Website: <a href="https://nongnghieppho.vn/" class="text-success text-decoration-none">https://nongnghieppho.vn/</a> | Hotline: <span class="highlight-text">0865 399 086</span></p>
            </div>
            
            <div class="store-list">
                <h3 class="text-center">Danh sách cửa hàng:</h3>
                <div class="row">
                    <div class="col-md-4">
                        <h4><i class="fas fa-map-marker-alt"></i> TP.HCM</h4>
                        <ul>
                            <li>- Q. Tân Bình: 81 Nguyễn Minh Hoàng, P12</li>
                            <li>- Q. Bình Thạnh: 220 Phan Văn Hân, P17</li>
                            <li>- Quận 7: 291 Lâm Văn Bền, P. Bình Thuận</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h4><i class="fas fa-map-marker-alt"></i> Đà Nẵng</h4>
                        <ul>
                            <li>- 227A Núi Thành, phường Hòa Cường Bắc, quận Hải Châu</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h4><i class="fas fa-map-marker-alt"></i> Hà Nội</h4>
                        <ul>
                            <li>- 10A phố Nguyễn Lân, phường Phương Liệt, Q.Thanh Xuân</li>
                        </ul>
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