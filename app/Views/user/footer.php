
<?php

$db_path = realpath(__DIR__ . '/../../config/db.php');

if ($db_path !== false && file_exists($db_path)) {
    include_once $db_path;
} else {

}


?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ví dụ Footer và FABs</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLMDJd/r9Vj5XpD1TzYhQGf21K2I3G2c7B9f4g2D4y4cK6eF4dYv4fF8e4g5eB5A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* ===================================== */
        /* --- KHAI BÁO BIẾN CSS (Cần thiết cho Footer & FABs) --- */
        /* ===================================== */
        :root {
            --nnp-green: #238E46; 
            --nnp-dark-blue: #34495e; 
            --nnp-yellow: #F9D050;
        }
        a{
            text-decoration: none !important;
            color: black;
        }

        ul, li {
            list-style-type: none;
            padding: 0;
            margin-left: 0; 
        }
        
        /* Thiết lập padding cho container footer trên desktop */
        .nnp-footer .container-fluid {
            padding-left: 3rem !important; /* Tương đương px-5 */
            padding-right: 3rem !important; /* Tương đương px-5 */
        }

        /* ===================================== */
        /* --- FOOTER STYLES --- */
        /* ===================================== */
        .nnp-footer {
            /* Đã thay đổi bg-dark trong phần social/signup thành bg-secondary để rõ hơn, bạn có thể đổi lại */
            background-color: #f7f7f7; 
            padding: 5px 0;
            color: #333;
            font-size: 0.9rem;
            border-top: 1px solid #ddd;
        }
        /* Style cho thanh social/signup trên cùng của footer (sử dụng màu đen như hình ảnh gốc) */
        .nnp-footer-top-bar {
            background-color: #333333; 
            color: white;
            padding: 15px 0;
        }

        .footer-heading {
            font-size: 1rem;
            font-weight: bold;
            color: var(--nnp-dark-blue);
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        .footer-col-item {
            margin-bottom: 5px;
        }
        .footer-col-item a {
            color: #555;
            transition: color 0.2s;
        }
        .footer-col-item a:hover {
            color: var(--nnp-green);
        }
        .contact-info strong {
            color: var(--nnp-green);
        }
        .footer-col-item img {
            max-width: 150px;
            height: auto;
        }
        .footer-social-link {
            display: inline-block;
            margin-right: 10px;
            font-size: 20px;
            color: #333;
        }
        /* Tùy chỉnh input đăng ký nhận tin */
        .nnp-footer-top-bar input.form-control {
            border-radius: 0 !important; 
        }

        /* Tùy chỉnh nút Đăng ký */
        .nnp-footer-top-bar .btn-warning {
            background-color: var(--nnp-yellow) !important; 
            border-color: var(--nnp-yellow) !important;
            color: black !important;
            border-radius: 0 !important; 
        }
        .nnp-footer-top-bar .fab {
            color: white !important; /* Biểu tượng mạng xã hội trong thanh đen phải màu trắng */
        }
        .nnp-footer-top-bar .text-nowrap {
            color: white !important;
        }

        /* ===================================== */
        /* --- FLOATING ACTION BUTTONS (FAB) --- */
        /* ===================================== */
        .fab-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1030;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .fab-wrapper {
            position: relative; /* Container cho nút và tooltip */
            display: flex;
            justify-content: flex-end;
        }
        .fab-btn {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            color: white;
            transition: transform 0.2s;
            text-decoration: none; 
        }
        .fab-btn:hover {
            transform: scale(1.1);
        }
        
        /* Tooltip Styles */
        .fab-tooltip {
            position: absolute;
            right: 60px; /* Cách nút 60px (48px nút + 12px khoảng cách) */
            top: 50%;
            transform: translateY(-50%);
            
            background-color: #333;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            white-space: nowrap;
            font-size: 0.85rem;
            
            /* Hiệu ứng ẩn/hiện */
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s, visibility 0.3s;
        }
        /* Thêm hover cho tooltip */
        .fab-wrapper:hover .fab-tooltip {
            opacity: 1;
            visibility: visible;
        }

        /* Màu sắc FABs */
        .fab-up { background-color: var(--nnp-yellow); color: black !important; }
        .fab-location { background-color: var(--nnp-green); }
        .fab-phone { background-color: #e53935; }
        .fab-zalo { background-color: #008cff; }

        /* Ẩn/Hiện nút Lên đầu trang */
        .fab-up.hidden { display: none; }

        /* ===================================== */
        /* --- TOP BAR STYLES --- */
        /* ===================================== */
        .nnp-top-bar {
            background-color: var(--nnp-green);
            color: white;
            padding: 10px 0;
            font-size: 0.9rem;
            display: none; 
        }
        
        .nnp-top-bar-item {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-weight: 500;
            gap: 8px;
            position: relative;
        }
        
        .nnp-top-bar-item i {
            color: white !important; /* Biểu tượng trong top bar phải màu trắng */
            font-size: 1.1rem;
        }
        
        /* Đường chia cột */
        .nnp-top-bar .col-lg-4:not(:last-child) .nnp-top-bar-item::after {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 70%;
            width: 1px;
            background-color: rgba(255, 255, 255, 0.4);
        }
        
        /* Điều chỉnh padding ngang cho container trên Desktop */
        .nnp-top-bar .container-fluid {
            padding-left: 3rem !important;
            padding-right: 3rem !important;
        }
        
        /* Hiển thị trên Desktop */
        @media (min-width: 992px) {
            .nnp-top-bar {
                display: block;
            }
        }

        /* ===================================== */
        /* --- RESPONSIVE MOBILE FIXES --- */
        /* ===================================== */
        @media (max-width: 991.98px) {
            /* Điều chỉnh Padding Footer trên mobile */
            .nnp-footer .container-fluid {
                padding-left: 1rem !important; 
                padding-right: 1rem !important; 
            }
            
            /* Điều chỉnh FABs trên Mobile */
            .fab-container {
                bottom: 10px;
                right: 20px;
                gap: 8px;
            }
            .fab-btn {
                width: 45px;
                height: 45px;
                font-size: 18px;
            }
            /* Ẩn Tooltip trên Mobile để không làm xáo trộn màn hình nhỏ */
            .fab-tooltip {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    

    <div class="nnp-top-bar">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="nnp-top-bar-item">
                        <i class="fas fa-check-circle"></i>
                        <span>HÀNG CHẤT LƯỢNG</span>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="nnp-top-bar-item">
                        <i class="fas fa-box-open"></i>
                        <span>ĐA DẠNG HÀNG HÓA</span>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="nnp-top-bar-item">
                        <i class="fas fa-headset"></i>
                        <span>MIỄN PHÍ TƯ VẤN KỸ THUẬT</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="nnp-footer">
        
        <div class="nnp-footer-top-bar">
            <div class="container">
                <div class="row align-items-center">

                    <div class="col-12 col-md-4 d-flex justify-content-center justify-content-md-start mb-3 mb-md-0">
                        <a href="https://www.facebook.com/NongNghiepPho.vn/" taget="_blank" class="me-3 fs-3">
                            <i class="fab fa-facebook-f"></i> </a>
                        <a href="https://zalo.me/1869973955811776365" class="me-3 fs-3">
                            <i class="fab fa-youtube"></i> </a>
                        <a href="https://www.instagram.com/nong_nghiep_pho/" class="fs-3">
                            <i class="fab fa-instagram"></i> </a>
                    </div>

                    <div class="col-12 col-md-8 d-md-flex align-items-center justify-content-center justify-content-md-end">
                        
                        <div class="d-flex align-items-center mb-2 mb-md-0 me-md-3">
                            <i class="fas fa-envelope me-2"></i> <span class="text-nowrap">Bạn muốn nhận không tin đặc biệt hàng ngày.</span>
                        </div>
                        
                        <form class="d-flex w-100 w-md-auto">
                            <input type="email" class="form-control me-0 border-0" placeholder="" aria-label="Địa chỉ Email">
                            <button type="submit" class="btn btn-warning text-dark fw-bold text-nowrap" style="min-width: 80px;">
                                Đăng ký
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <div class="container pt-4"> 
            <div class="row">
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <h3 class="footer-heading">Hệ thống cửa hàng</h3>
                    <div class="contact-info">
                        <p class="footer-col-item"><strong>Khu vực HCM</strong></p>
                        <p class="footer-col-item">Chi Nhánh Tân Bình: 81 Nguyễn Minh Hoàng, P.12, Q. Tân Bình, TP.HCM</p>
                        <p class="footer-col-item">Chi Nhánh Quận 7: 160 Lâm Văn Bền, P. Tân Quy, Q.7, TP. HCM</p>
                        <p class="footer-col-item">Chi Nhánh Bình Thạnh: 220 Phan Văn Hân, P.17, Q. Bình Thạnh, TP.HCM</p>
                        <p class="footer-col-item"><strong>Khu vực Hà Nội</strong></p>
                        <p class="footer-col-item">Chi Nhánh Thanh Xuân: 10A Phố Nguyễn Lân, Phương Liệt, Q. Thanh Xuân, Hà Nội</p>
                        <p class="footer-col-item"><strong>Khu vực Đà Nẵng</strong></p>
                        <p class="footer-col-item">Chi Nhánh Hải Châu: 227A Núi Thành, Phường Hòa Cường Bắc, Quận Hải Châu, Thành phố Đà Nẵng</p>
                    </div>
                    <h3 class="footer-heading mt-4">Giờ mở cửa</h3>
                    <p class="footer-col-item">Thứ 2 - Chủ Nhật: 7:30 - 18:00</p>
                    <p class="footer-col-item"><i class="fas fa-phone-alt me-2"></i> Số điện thoại: 0865588883</p>
                    <p class="footer-col-item"><i class="fas fa-envelope me-2"></i> Email: info@nongnghieppho.vn</p>
                    <p class="mt-3"><img src="https://via.placeholder.com/150x50?text=Logo+Bo+Cong+Thuong" alt="Chứng nhận Bộ Công Thương"></p>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <h3 class="footer-heading">Hỗ trợ khách hàng</h3>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item footer-col-item"><a href="<?php echo BASE_URL; ?>/app/Views/user/chinhsachchietkhau/quydinhchung.php">Chính sách & Quy định chung</a></li>
                        <li class="list-group-item footer-col-item"><a href="<?php echo BASE_URL; ?>/app/Views/user/chinhsachchietkhau/quydinh.php">Quy định và hình thức thanh toán</a></li>
                        <li class="list-group-item footer-col-item"><a href="<?php echo BASE_URL; ?>/app/Views/user/chinhsachchietkhau/baomatthongtin.php">Chính sách bảo mật thông tin</a></li>
                        <li class="list-group-item footer-col-item"><a href="<?php echo BASE_URL; ?>/app/Views/user/chinhsachchietkhau/chinhsachvanchuyen.php">Chính sách vận chuyển, giao nhận</a></li>
                        <li class="list-group-item footer-col-item"><a href="<?php echo BASE_URL; ?>/app/Views/user/chinhsachchietkhau/trahang.php">Chính sách trả hàng - hoàn tiền</a></li>
                        <li class="list-group-item footer-col-item"><a href="<?php echo BASE_URL; ?>/app/Views/user/chinhsachchietkhau/khachhang.php
                        ">Chính sách khách hàng thân thiết</a></li>
                    </ul>
                <h3 class="footer-heading mt-4">Facebook</h3>
<div class="facebook-embed">
    <div class="fb-page" 
         data-href="https://www.facebook.com/NongNghiepPho.vn/" 
         data-tabs="" 
         data-width="340" 
         data-height="" 
         data-small-header="true" 
         data-adapt-container-width="true" 
         data-hide-cover="false" 
         data-show-facepile="false">
        <blockquote cite="https://www.facebook.com/NongNghiepPho.vn/" class="fb-xfbml-parse-ignore">
            <a href="https://www.facebook.com/NongNghiepPho.vn/">Tên Fanpage (Sẽ được thay thế tự động)</a>
        </blockquote>
    </div>
</div>
                    <div class="footer-social-icons">
                        <a href="#" class="footer-social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="footer-social-link"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="footer-social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="footer-social-link"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12 mb-4">
                    <h3 class="footer-heading">Tổng đài hỗ trợ miễn phí</h3>
                    <p class="mb-1"><strong>GỌI MUA HÀNG</strong></p>
                    <p class="fs-4 fw-bold mb-3" style="color: var(--nnp-dark-blue);">0865 588 883</p>
                    <p class="mb-1">Thứ 2 - Chủ Nhật: 8:00 - 17:00</p>
                    <p class="mb-1"><strong>GÓP Ý - KHIẾU NẠI</strong></p>
                    <p class="fs-4 fw-bold mb-3" style="color: #d9534f;">0906 800 386</p>
                    <p class="mb-1">Thứ 2 - Thứ 6: 08:00 - 17:00 | Thứ 7: 08:00 - 12:00</p>
                    
                    <h3 class="footer-heading mt-4">Phương thức thanh toán</h3>
                    <p>
                        <img src="https://theme.hstatic.net/1000269461/1000985512/14/footer_trustbadge.jpg?v=2277" alt="Visa" style="max-height: 30px;">
                                  </p>
                </div>
            </div>
            
            <div class="row pt-3 mt-3 border-top text-center">
                <p class="mb-0 text-muted" style="font-size: 0.8rem;">
                    © Bản quyền thuộc về Nông Nghiệp Phố - Công ty Cổ phần P&T.
                </p>
            </div>
        </div>
    </footer>
    <div class="fab-container">
        
        <div class="fab-wrapper">
            <a href="#" id="scrollTopBtn" class="fab-btn fab-up hidden"><i class="fas fa-arrow-up"></i></a>
            <span class="fab-tooltip">Lên đầu trang</span>
        </div>

        <div class="fab-wrapper">
            <a href="#" class="fab-btn fab-location"><i class="fas fa-map-marker-alt"></i></a>
            <span class="fab-tooltip">Chọn kho giao hàng</span>
        </div>

        <div class="fab-wrapper">
            <a href="tel:0865588883" class="fab-btn fab-phone"><i class="fas fa-phone-alt"></i></a>
            <span class="fab-tooltip">Gọi ngay cho chúng tôi</span>
        </div>

        <div class="fab-wrapper">
            <a href="#" class="fab-btn fab-zalo"><i class="fas fa-comment-dots"></i></a>
            <span class="fab-tooltip">Chat với chúng tôi qua Zalo</span>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>



    <div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v18.0"></script>
    <script>

        
        document.addEventListener("DOMContentLoaded", function() {
            // Chỉ chạy logic FABs nếu nút Lên đầu trang tồn tại
            const scrollTopBtn = document.getElementById('scrollTopBtn');
            if (scrollTopBtn) {
                
                // Hàm xử lý hiển thị/ẩn nút Lên đầu trang
                function handleScroll() {
                    // Kiểm tra xem đã cuộn qua 300px chưa
                    if (window.scrollY > 300) {
                        scrollTopBtn.classList.remove('hidden');
                    } else {
                        scrollTopBtn.classList.add('hidden');
                    }
                }
                
                // Xử lý nút cuộn lên đầu trang
                scrollTopBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
                
                window.addEventListener('scroll', handleScroll);
                handleScroll(); // Chạy lần đầu để kiểm tra vị trí cuộn khi tải trang
            }
        });
    </script>

    <!-- Dynamic Table of Contents (TOC) Script -->
    <script>
    $(document).ready(function() {
        // Only run TOC generation if a toc-sidebar exists on the page
        var tocSidebar = $('.toc-sidebar');
        if (tocSidebar.length) {
            var toc = tocSidebar.find('nav');
            var postContent = $('.post-body'); 
            
            toc.empty(); 
            var tocList = $('<ul class="list-unstyled"></ul>');
            toc.append(tocList);

            // Diagnostics for debugging
            console.log('TOC Script: .post-body HTML:', postContent.html()); // Enable for debugging
            var headings = postContent.find('h2, h3');
            var headingsCount = headings.length;
            console.log('TOC Script: Found ' + headingsCount + ' headings for TOC.'); // Enable for debugging

            if (headingsCount > 0) {
                headings.each(function(index) {
                    var heading = $(this);
                    var text = heading.text();
                    var id = 'toc-' + index + '-' + text.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-*|-*$/g, '');

                    heading.attr('id', id);

                    var li = $('<li></li>');
                    var link = $('<a href="#' + id + '">' + text + '</a>');
                    
                    if (this.tagName.toLowerCase() === 'h3') {
                        li.addClass('ms-3');
                    }
                    li.append(link);
                    tocList.append(li);
                });
            } else {
                toc.append('<p class="small text-muted">Không có mục lục (bài viết chưa có tiêu đề).</p>');
            }
        }
    });
    </script>


<!-- Quick View Modal -->
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickViewModalLabel">Xem nhanh sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">
                        <img src="" id="quickViewImage" class="img-fluid rounded" alt="Product Image">
                    </div>
                    <div class="col-md-7">
                        <h3 id="quickViewName">Product Name</h3>
                        <p class="text-muted small">Mã sản phẩm: <span id="quickViewSku" class="fw-bold">N/A</span></p>
                        <p class="fs-4 fw-bold text-danger" id="quickViewPrice">0₫</p>
                        <form id="quickViewAddToCartForm">
                            <input type="hidden" name="product_id" id="quickViewProductId">
                            <input type="hidden" name="action" value="add">
                            <div class="mb-3">
                                <label for="quickViewQuantity" class="form-label">Số lượng:</label>
                                <div class="input-group" style="width: 150px;">
                                    <button class="btn btn-outline-secondary" type="button" onclick="this.nextElementSibling.stepDown()">-</button>
                                    <input type="number" id="quickViewQuantity" name="quantity" class="form-control text-center" value="1" min="1">
                                    <button class="btn btn-outline-secondary" type="button" onclick="this.previousElementSibling.stepUp()">+</button>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-lg w-100">
                                <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container for Notifications -->
<div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1200">
    <!-- Toasts will be appended here by JavaScript -->
</div>

<script id="main-app-script">
document.addEventListener('DOMContentLoaded', function () {
    const quickViewModalEl = document.getElementById('quickViewModal');
    const quickViewModal = quickViewModalEl ? new bootstrap.Modal(quickViewModalEl) : null;
    const toastContainer = document.getElementById('toast-container');
    const base_url = '<?php echo BASE_URL; ?>';

    // GLOBAL: Function to show a toast notification
    // GLOBAL: Function to show a toast notification (Đã tối ưu để không hiện liên tục)
    window.showToast = function(message, isSuccess = true) {
        if (!toastContainer) return;

        // 1. XÓA SẠCH các toast cũ đang có trong container để tránh chồng chất
        toastContainer.innerHTML = ''; 

        const toastId = 'toast-' + Date.now();
        const toastHTML = `
            <div id="${toastId}" class="toast align-items-center text-white ${isSuccess ? 'bg-success' : 'bg-danger'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas ${isSuccess ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        // 2. Chèn toast mới vào
        toastContainer.insertAdjacentHTML('beforeend', toastHTML);
        
        const toastElement = document.getElementById(toastId);
        
        // Cấu hình Toast: Tự động ẩn sau 2 giây (2000ms)
        const toast = new bootstrap.Toast(toastElement, { 
            delay: 2000,
            autohide: true 
        });
        
        toast.show();

        // 3. Xóa hẳn element khỏi DOM sau khi ẩn để nhẹ trang
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    // GLOBAL: Function to update header cart display
    window.updateHeaderCart = async function() {
        try {
            const response = await fetch(base_url + '/app/api/cart_actions.php?action=get_cart_data');
            if (!response.ok) return;
            const data = await response.json();
            
            document.querySelectorAll('.cart-badge').forEach(badge => {
                if (data.item_count > 0) {
                    badge.innerText = data.item_count;
                    badge.style.display = '';
                } else {
                    badge.style.display = 'none';
                }
            });
        } catch (error) {
            console.error('Error updating header cart:', error);
        }
    }

    // GLOBAL: Function to handle adding item to cart
    window.handleAddToCart = async function(productId, quantity = 1) {
        const formData = new FormData();
        formData.append('action', 'add');
        formData.append('product_id', productId);
        formData.append('quantity', quantity);

        try {
            const response = await fetch(base_url + '/app/api/cart_actions.php', {
                method: 'POST',
                body: formData
            });

            // Kiểm tra response có OK không
            if (!response.ok) {
                showToast('Có lỗi xảy ra, vui lòng thử lại.', false);
                return;
            }

            // Kiểm tra content-type để đảm bảo là JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                showToast('Có lỗi xảy ra, vui lòng thử lại.', false);
                return;
            }

            const result = await response.json();
            
            // Đảm bảo result là object và có message
            if (result && typeof result === 'object') {
                const message = result.message || (result.success ? 'Sản phẩm đã được thêm vào giỏ hàng!' : 'Có lỗi xảy ra, vui lòng thử lại.');
                showToast(message, result.success || false);

                if (result.success) {
                    updateHeaderCart();
                    // Gọi refreshCartDisplay nếu có (từ header.php)
                    if (typeof refreshCartDisplay === 'function') {
                        refreshCartDisplay();
                    }
                }
            } else {
                showToast('Có lỗi xảy ra, vui lòng thử lại.', false);
            }
        } catch (error) {
            showToast('Có lỗi xảy ra, vui lòng thử lại.', false);
            console.error('Add to cart error:', error);
        }
    }

    // GLOBAL: Function to open quick view
     window.openQuickView = function(product) {
        if (!quickViewModalEl) return;
        
        const modal = $(quickViewModalEl);
        modal.find('#quickViewName').text(product.name);
        modal.find('#quickViewSku').text(product.sku || 'N/A');
        modal.find('#quickViewPrice').text(new Intl.NumberFormat('vi-VN').format(product.price) + '₫');
        modal.find('#quickViewImage').attr('src', base_url + '/' + (product.thumbnail || 'public/uploads/default.png'));
        modal.find('#quickViewProductId').val(product.id);
        modal.find('#quickViewQuantity').val(1);
        
        quickViewModal.show();
    }


    // --- GLOBAL EVENT LISTENERS ---

    // 1. Listen for clicks on ANY "add to cart" button
    document.body.addEventListener('click', function(e) {
        const directAddBtn = e.target.closest('.ajax-add-to-cart-btn');
        if (directAddBtn) {
            e.preventDefault();
            e.stopPropagation();
            const productId = directAddBtn.dataset.productId;
            handleAddToCart(productId, 1);
        }
    });

    // 2. Listen for form submission from inside Quick View Modal
    const quickViewForm = document.getElementById('quickViewAddToCartForm');
    if (quickViewForm) {
        quickViewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const productId = document.getElementById('quickViewProductId').value;
            const quantity = document.getElementById('quickViewQuantity').value;
            handleAddToCart(productId, quantity);
            quickViewModal.hide();
        });
    }

    // Initial cart update on page load
    updateHeaderCart();
});
</script>
</body>
</html>