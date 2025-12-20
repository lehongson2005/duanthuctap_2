<?php
session_start();
include '../../../config/db.php';
include '../../../../app/models/UserModel.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) { 
    header("Location: ../../login.php"); 
    exit; 
}

$id = $_GET['id'] ?? null;

if ($id) {
    $model = new UserModel($conn);
    $model->delete($id);
    header("Location: list_users.php?msg=deleted");
    exit;
}

header("Location: list_users.php");
exit;
?>
