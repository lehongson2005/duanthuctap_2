<?php
session_start(); // 1. Khởi tạo session để tìm phiên làm việc hiện tại

// 2. Xóa tất cả các biến session đã lưu (user_id, username, role...)
$_SESSION = array();

// 3. Hủy hoàn toàn session trên server
session_destroy();

// 4. Chuyển hướng người dùng về lại trang đăng nhập
header("Location: login.php");
exit;
?>