<?php
// C:\xampp1\htdocs\DuAnThucTap_2\app\Views\user\taikhoan\capnhat.php

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /DuAnThucTap_2/login.php");
    exit;
}

require_once "../../../config/db.php";
require_once "../../../models/UserModel.php";

$userModel = new UserModel($conn);
$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: taikhoan.php");
    exit;
}

$full_name = trim($_POST['full_name'] ?? '');
$phone     = trim($_POST['phone'] ?? '');
$address   = trim($_POST['address'] ?? '');
$gender    = isset($_POST['gender']) ? (int) $_POST['gender'] : null;

/* VALIDATION CƠ BẢN */
if ($full_name === '' || $phone === '' || $address === '' || !in_array($gender, [0,1], true)) {
    die("DỮ LIỆU KHÔNG HỢP LỆ");
}

/* CẬP NHẬT */
$result = $userModel->updateProfile(
    $userId,
    $full_name,
    $gender,
    $phone,
    $address
);

if ($result) {
    header("Location: taikhoan.php?success=1");
    exit;
}

die("KHÔNG CÓ DỮ LIỆU");
