<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* ===== CỘT TRÁI ===== */
        .contact-info-section {
            padding: 20px;
            background: #fff;
            border-right: 1px solid #eee;
        }

        .contact-detail {
            font-size: 0.95rem;
        }

        .contact-detail strong {
            color: #238E46;
            display: block;
            margin-top: 10px;
        }

        .contact-detail p {
            margin-bottom: 4px;
        }

        /* ===== FORM ===== */
        .form-group {
            margin-bottom: 15px;
        }

        /* ===== CỘT PHẢI - MAP ===== */
        .map-container {
            padding: 0;
            min-height: 600px;
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }

        /* ===== DESKTOP: 2 CỘT CAO BẰNG NHAU ===== */
        @media (min-width: 992px) {
            .contact-row {
                display: flex;
            }
            .map-container {
                height: auto;
            }
        }

        /* ===== MOBILE ===== */
        @media (max-width: 991.98px) {
            .contact-info-section {
                border-right: none;
                border-bottom: 1px solid #eee;
            }
            .map-container {
                min-height: 300px;
            }
        }
    </style>
</head>
<body>

<?php include '../header.php'; ?>

<div class="container my-4">

    <!-- Breadcrumb -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>" class="text-success">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Liên hệ</li>
                </ol>
            </nav>
            <h1 class="fw-bold">THÔNG TIN LIÊN HỆ</h1>
        </div>
    </div>

    <!-- Nội dung chính -->
    <div class="row contact-row">

        <!-- CỘT TRÁI -->
        <div class="col-12 col-lg-6 contact-info-section">

            <div class="contact-detail">
                <i class="fas fa-map-marker-alt text-success me-2"></i>
                <strong>Địa chỉ</strong>

                <p><strong>TP.HCM</strong></p>
                <p>81 Nguyễn Minh Hoàng, Q.Tân Bình</p>
                <p>160 Lâm Văn Bền, Q.7</p>
                <p>220 Phan Văn Hân, Q.Bình Thạnh</p>

                <p><strong>Hà Nội</strong></p>
                <p>10A Nguyễn Lân, Thanh Xuân</p>

                <p><strong>Đà Nẵng</strong></p>
                <p>227A Núi Thành, Hải Châu</p>

                <p><i class="far fa-clock text-success me-1"></i> 7:30 – 18:00</p>
                <p><i class="fas fa-phone text-success me-1"></i> 0965 399 926</p>
                <p><i class="far fa-envelope text-success me-1"></i> info@nongnghieppho.vn</p>
            </div>

            <hr>

            <h2 class="fs-4 fw-bold mb-3">LIÊN HỆ VỚI CHÚNG TÔI</h2>

            <form method="post">
                <div class="form-group">
                    <label>Họ tên *</label>
                    <input type="text" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Số điện thoại *</label>
                    <input type="tel" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Nội dung *</label>
                    <textarea class="form-control" rows="4" required></textarea>
                </div>

                <button class="btn w-100 fw-bold text-dark mt-3" style="background:#FDD835;">
                    GỬI LIÊN HỆ
                </button>
            </form>
        </div>

        <!-- CỘT PHẢI -->
        <div class="col-12 col-lg-6 map-container">
           <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d31348.691086701172!2d106.6696704!3d10.8429312!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317529a3be3265e9%3A0x73eb335ab4f71bbb!2zQ8O0bmcgdmnDqm4gTMOgbmcgaG9hIEfDsiBW4bqlcA!5e0!3m2!1svi!2s!4v1765780036115!5m2!1svi!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>

    </div>
</div>

<?php include '../footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
