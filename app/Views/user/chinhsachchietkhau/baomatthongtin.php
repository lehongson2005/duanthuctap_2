<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chính Sách Bảo Mật Thông Tin</title>
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
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 25px;
            margin-bottom: 15px;
            border-left: 5px solid var(--primary-color);
            padding-left: 10px;
        }

        .policy-section p, .policy-section ul {
            line-height: 1.8;
            color: var(--text-dark);
            font-size: 0.95rem;
        }

        .policy-section ul {
            list-style-type: disc;
            margin-left: 20px;
            padding-left: 0;
        }
        .policy-section li {
            margin-bottom: 10px;
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
            <h1 class="policy-title">CHÍNH SÁCH BẢO MẬT THÔNG TIN</h1>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12 policy-container">
            
            <div class="policy-section">
                <h2>Mục đích thu thập thông tin cá nhân</h2>
                <p>Để truy cập và sử dụng một số dịch vụ tại website, bạn có thể sẽ được yêu cầu đăng ký với chúng tôi thông tin cá nhân (Email, Họ tên, Số ĐT liên lạc…). Mọi thông tin khai báo phải đảm bảo tính chính xác và hợp pháp. Chúng tôi không chịu mọi trách nhiệm liên quan đến pháp luật của thông tin khai báo.</p>
            </div>

            <div class="policy-section">
                <h2>Sử dụng thông tin cá nhân</h2>
                <p>Chúng tôi thu thập và sử dụng thông tin cá nhân bạn với mục đích phù hợp và hoàn toàn tuân thủ nội dung của “Chính sách bảo mật” này.</p>
                <p>Khi cần thiết, chúng tôi có thể sử dụng những thông tin này để liên hệ trực tiếp với bạn dưới các hình thức như:</p>
                <ul>
                    <li>Gởi thư ngỏ, đơn đặt hàng, thư cảm ơn.</li>
                    <li>Thông tin về kỹ thuật và bảo mật.</li>
                </ul>
            </div>

            <div class="policy-section">
                <h2>Chia sẻ thông tin cá nhân</h2>
                <p>Ngoại trừ các trường hợp về sử dụng thông tin cá nhân như đã nêu trong chính sách này, chúng tôi cam kết sẽ <span class="highlight-text">không tiết lộ thông tin cá nhân bạn ra ngoài</span>.</p>
                <p>Trong một số trường hợp, thông tin của bạn có thể được cung cấp cho một đơn vị độc lập mà chúng tôi thuê để tiến hành các dự án nghiên cứu thị trường. Bên thứ ba này sẽ bị ràng buộc bởi một thỏa thuận về bảo mật, theo đó họ chỉ được phép sử dụng thông tin được cung cấp cho mục đích hoàn thành dự án.</p>
                <p>Chúng tôi có thể tiết lộ hoặc cung cấp thông tin cá nhân của bạn trong các trường hợp thật sự cần thiết sau:</p>
                <ul>
                    <li>Khi có yêu cầu của các cơ quan pháp luật.</li>
                    <li>Trong trường hợp chúng tôi tin rằng điều đó sẽ giúp chúng tôi bảo vệ quyền lợi chính đáng của mình trước pháp luật.</li>
                    <li>Tình huống khẩn cấp và cần thiết để bảo vệ quyền an toàn cá nhân của các thành viên khác.</li>
                </ul>
            </div>

            <div class="policy-section">
                <h2>Truy xuất thông tin cá nhân</h2>
                <p>Bất cứ thời điểm nào bạn cũng có thể <span class="highlight-text">truy cập và chỉnh sửa</span> những thông tin cá nhân của mình theo các links thích hợp mà chúng tôi cung cấp.</p>
            </div>

            <div class="policy-section">
                <h2>Bảo mật thông tin cá nhân</h2>
                <p>Chúng tôi cam kết <span class="highlight-text">bảo mật thông tin cá nhân của bạn bằng mọi cách thức có thể</span>. Chúng tôi sẽ sử dụng nhiều công nghệ bảo mật thông tin khác nhau nhằm bảo vệ thông tin này không bị truy lục, sử dụng hoặc tiết lộ ngoài ý muốn.</p>
                <p>Chúng tôi khuyến cáo bạn nên bảo mật các thông tin liên quan đến mật khẩu truy xuất của bạn và không nên chia sẻ với bất kỳ người nào khác. Nếu sử dụng máy tính chung nhiều người, bạn nên đăng xuất, hoặc thoát hết tất cả cửa sổ Website đang mở.</p>
            </div>

            <div class="policy-section">
                <h2>Thay đổi về chính sách</h2>
                <p>Nội dung của “Chính sách bảo mật” này có thể thay đổi để phù hợp với các nhu cầu của chúng tôi cũng như nhu cầu và sự phản hồi từ khách hàng nếu có. Khi cập nhật nội dung chính sách này, chúng tôi sẽ chỉnh sửa lại thời gian “Cập nhật lần cuối” bên trên.</p>
                <p>Nội dung “Chính sách bảo mật” này chỉ áp dụng tại website của chúng tôi, không bao gồm hoặc liên quan đến các bên thứ ba đặt quảng cáo. Do đó, chúng tôi đề nghị bạn đọc và tham khảo kỹ nội dung “Chính sách bảo mật” của từng website mà bạn đang truy cập.</p>
            </div>
            
        </div>
    </div>
    
</div>

<?php include '../footer.php'; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>