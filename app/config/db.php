<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'duanthuctap_2';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die('Kết nối thất bại: ' . $conn->connect_error);
}
mysqli_set_charset($conn, 'utf8mb4');
if (!defined('BASE_URL')) {
    define('BASE_URL', '/DuAnThucTap_2');
}
// define('GOOGLE_MAPS_API_KEY', '');
