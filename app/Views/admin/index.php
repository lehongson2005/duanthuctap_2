<?php
// session_start(); // Session đã được start trong header
include_once '../../config/db.php';
include_once '../../../app/models/UserModel.php';


$page_title = "Dashboard Tổng Quan";
include 'templates/header.php';


// Instantiate models
$userModel = new UserModel($conn);



?>

<style>
    /* Card thống kê hiệu ứng hover */
    .stats-card {
        transition: all 0.3s;
        border: none;
        border-radius: 15px;
    }
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .icon-box {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 24px;
    }
</style>




            <div class="card-footer bg-white border-0 text-center py-3">
                <a href="../admin/cruduser/list_users.php" class="text-decoration-none fw-bold">Xem tất cả thành viên</a>
            </div>
        </div>
    </div>

  
</div> <!-- End Row -->

<?php include 'templates/footer.php'; ?>