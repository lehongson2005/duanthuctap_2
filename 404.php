<?php
http_response_code(404);
include 'app/Views/user/header.php';
?>

<div class="container" style="padding: 80px 0; text-align: center;">
    <h1 style="font-size: 6rem; font-weight: bold;">404</h1>
    <h2 style="font-size: 2rem; margin-bottom: 20px;">Không tìm thấy trang</h2>
    <p style="font-size: 1.2rem; margin-bottom: 30px;">Xin lỗi, trang bạn đang tìm kiếm không tồn tại.</p>
    <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-success btn-lg">
        <i class="fas fa-home me-2"></i> Quay về trang chủ
    </a>
</div>

<?php
include 'app/Views/user/footer.php';
?>
