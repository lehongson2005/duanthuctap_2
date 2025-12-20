<?php
    date_default_timezone_set('Asia/Ho_Chi_Minh');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// 1. TỰ ĐỘNG TÍNH ĐƯỜNG DẪN (PATH)
if (file_exists('sidebar.php')) {
    $path = ''; 
} else {
    // If not in the admin root, assume one level up for crud folders
    $path = '../'; 
}

// 2. TỰ ĐỘNG BẮT TRẠNG THÁI ACTIVE
$current_dir = basename(dirname($_SERVER['PHP_SELF']));


// Kiểm tra đăng nhập và quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    // Xác định đường dẫn tương đối chính xác đến trang login
    // Nếu đang ở trong thư mục con (VD: /crud...)
    $login_path = strpos($_SERVER['REQUEST_URI'], '/crud') !== false ? '../Auth/login.php' : 'Auth/login.php';
    header("Location: $login_path");
    exit;
}

include_once __DIR__ . '/../../../config/db.php';

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Admin Dashboard'; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Admin Sidebar CSS -->
    <link rel="stylesheet" href="<?php echo $path; ?>../assets/css/admin_sidebar.css">
</head>
<body>

    <div class="d-flex">
        
        <?php include __DIR__ . '/../sidebar.php'; ?>

        <div class="flex-grow-1 bg-light" style="min-height: 100vh;">
            
            <nav class="navbar navbar-expand-lg bg-white shadow-sm px-4 py-3 mb-4">
                <div class="d-flex align-items-center w-100">
                    <button class="btn btn-light d-lg-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                        <i class="fa-solid fa-bars"></i>
                    </button>

                    <h4 class="mb-0 fw-bold text-secondary"><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h4>
                    
                    <div class="ms-auto d-flex align-items-center">
                        <span class="text-muted d-none d-sm-block me-2">Hôm nay: <?php echo date('d/m/Y'); ?></span>
                    </div>
                </div>
            </nav>

            <div class="container-fluid px-4">
