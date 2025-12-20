<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chính Sách Trả Hàng - Hoàn Tiền</title>
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
            max-width: 1000px; /* Giới hạn độ rộng hợp lý cho trang chính sách */
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
            /* Sử dụng style nhất quán: Border-left màu xanh lá */
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 25px;
            margin-bottom: 15px;
            border-left: 5px solid var(--primary-color);
            padding-left: 10px;
        }

        .policy-section p, .policy-section li {
            line-height: 1.8;
            color: var(--text-dark);
            font-size: 0.95rem;
        }

        .policy-section ul {
            list-style-type: disc;
            margin-left: 20px;
            padding-left: 0;
            margin-bottom: 20px;
        }
        .policy-section ol {
            list-style-type: decimal;
            margin-left: 20px;
            padding-left: 0;
            margin-bottom: 20px;
        }
        .policy-section li {
            margin-bottom: 8px;
        }

        .highlight-text {
            font-weight: bold;
            color: var(--primary-color);
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
            <h1 class="policy-title">CHÍNH SÁCH TRẢ HÀNG - HOÀN TIỀN</h1>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12 policy-container">
            
            <div class="policy-section">
                <h2>1. Trường hợp được đổi/trả hàng</h2>
                <ul>
                    <li>Sản phẩm giao không đúng số lượng, thông tin, quy cách đóng gói theo đơn đặt hàng.</li>
                    <li>Sản phẩm mua bị lỗi bao gồm: tem nhãn bị phai màu, bị rách, bị trầy xước, sản phẩm bị vỡ, sản phẩm bị tháo ra, méo mó, biến dạng...</li>
                    <li>Sản phẩm quá hạn sử dụng.</li>
                    <li>Sản phẩm bị hỏng dù bao bì còn nguyên, bị lỗi kỹ thuật (bao bì không kín hơi, bị xì, có vật thể lạ, thiếu thể tích...)</li>
                    <li>Đối với hạt giống bị lỗi kỹ thuật hoặc lỗi phát sinh từ Nông Nghiệp Phố thì Nông Nghiệp Phố sẽ đổi hạt giống khác có giá trị tương đương (không đền bù cây giống).</li>
                </ul>
                <p class="fst-italic text-muted small">Ghi chú: Trường hợp mua tại cửa hàng, Quý khách vui lòng kiểm tra kỹ hàng hóa trước khi rời cửa hàng.</p>
            </div>

            <div class="policy-section">
                <h2>2. Điều khoản điều kiện đổi/trả hàng</h2>
                <ul>
                    <li>Thời gian đổi các mặt hàng tại Nông nghiệp phố là <span class="highlight-text">07 ngày</span> từ ngày mua/nhận hàng.</li>
                    <li>Hàng được đổi phải đảm bảo <span class="highlight-text">chưa qua sử dụng</span>, còn nguyên tem nhãn, hộp, phụ kiện như thời điểm mua/nhận hàng.</li>
                    <li>Sản phẩm được mua tại các cửa hàng Nông Nghiệp Phố thì áp dụng đổi/trả tại <span class="highlight-text">Chi nhánh đã mua</span>. Trường hợp mua online vui lòng liên hệ hotline <span class="highlight-text">0865 399 086</span> để được hướng dẫn đổi/trả hàng.</li>
                    <li>Tổng giá trị các mặt hàng muốn đổi phải có giá trị <span class="highlight-text">tương đương hoặc lớn hơn</span> so với mặt hàng trả lại. Nông nghiệp phố không hoàn lại tiền thừa trong trường hợp sản phẩm mới có giá trị thấp hơn sản phẩm đã mua.</li>
                    <li>Trường hợp hết hàng khi khách hàng muốn đổi mặt hàng cùng loại, Nông nghiệp phố sẽ <span class="highlight-text">hoàn lại toàn bộ số tiền</span> của sản phẩm đó.</li>
                    <li>Không đổi sản phẩm bán trong chương trình khuyến mãi nếu chương trình có quy định là không được đổi trả hàng, hoặc hàng đã được đổi một lần trước đó, trừ trường hợp sản phẩm đó lại gặp lỗi kỹ thuật.</li>
                    <li>Không đổi hàng đối với các sản phẩm bị hư hại, bị bẩn, trầy xước do lỗi khách hàng.</li>
                    <li>Khách hàng chịu chi phí vận chuyển, liên lạc... nếu có phát sinh trong quá trình trả hàng.</li>
                    <li>Công ty sẽ hoàn lại tiền sau khi trừ các chi phí phát sinh (nếu có) trong quá trình trả hàng cho khách hàng qua tài khoản hoặc bằng tiền mặt.</li>
                </ul>
            </div>

            <div class="policy-section">
                <h2>3. Quy trình đổi/trả hàng</h2>
                <ol>
                    <li><span class="highlight-text">Bước 1: Kiểm tra và phản hồi ngay</span><br>
                        Sau khi nhận được hàng. Yêu cầu khách hàng cần kiểm tra kỹ khi nhận hàng bao gồm tên sản phẩm, số lượng, chủng loại, bao bì... Nếu có vấn đề xin phản hồi với nhân viên giao hàng. Trường hợp sau khi nhân viên giao hàng đã đi, nếu muốn đổi trả hàng có thể liên hệ với chúng tôi qua số <span class="highlight-text">0865 399 086</span> để kịp thời xử lý.</li>
                    <li><span class="highlight-text">Bước 2: Yêu cầu đổi/trả</span><br>
                        Khách hàng liên hệ cửa hàng hoặc hotline <span class="highlight-text">0865 399 086</span> để yêu cầu việc đổi/trả sản phẩm, Nông Nghiệp Phố sẽ hướng dẫn bạn cách đổi/trả sản phẩm. Nếu quá trình đổi/trả sản phẩm của khách hợp lệ.</li>
                    <li><span class="highlight-text">Bước 3: Gửi sản phẩm</span><br>
                        Khách hàng gửi sản phẩm hàng hóa cho Nông Nghiệp Phố tiếp nhận theo chỉ dẫn phía trên (tại cửa hàng hoặc chuyển theo đường bưu điện đối với sản phẩm mua online).</li>
                    <li><span class="highlight-text">Bước 4: Kiểm tra và tiến hành đổi</span><br>
                        Nông Nghiệp Phố nhận sản phẩm và kiểm tra sản phẩm và tiến hành đổi hàng.</li>
                    <li><span class="highlight-text">Bước 5: Nhận sản phẩm/tiền hoàn</span><br>
                        Khách hàng nhận sản phẩm thay thế hoặc nhận tiền hoàn lại (thời gian hoàn tiền là <span class="highlight-text">03 ngày</span> kể từ ngày shop nhận được hàng).</li>
                </ol>
            </div>
            
        </div>
    </div>
    
</div>
<?php include '../footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>