<?php
header('Content-Type: application/json');
require_once '../config/db.php';
require_once '../models/ProvinceModel.php';
require_once '../models/WardModel.php';
require_once '../models/StoreModel.php';

$provinceModel = new ProvinceModel($conn);
$wardModel = new WardModel($conn);
$storeModel = new StoreModel($conn);

$response = ['success' => false, 'data' => [], 'message' => ''];

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'get_provinces':
            $provinces = $provinceModel->getAll();
            if ($provinces->num_rows > 0) {
                $response['success'] = true;
                while ($row = $provinces->fetch_assoc()) {
                    $response['data'][] = $row;
                }
            } else {
                $response['message'] = 'Không tìm thấy Tỉnh/Thành phố nào.';
            }
            break;

        case 'get_wards_by_province':
            if (isset($_GET['province_id']) && is_numeric($_GET['province_id'])) {
                $province_id = (int)$_GET['province_id'];
                $wards = $wardModel->getByProvinceId($province_id);
                if ($wards->num_rows > 0) {
                    $response['success'] = true;
                    while ($row = $wards->fetch_assoc()) {
                        $response['data'][] = $row;
                    }
                } else {
                    $response['message'] = 'Không tìm thấy Xã/Phường nào cho Tỉnh/Thành phố này.';
                }
            } else {
                $response['message'] = 'ID Tỉnh/Thành phố không hợp lệ.';
            }
            break;

        case 'get_stores_by_location':
            $province_id = isset($_GET['province_id']) ? (int)$_GET['province_id'] : '';
            $ward_id = isset($_GET['ward_id']) ? (int)$_GET['ward_id'] : '';
            
            $stores = $storeModel->searchAndFilter('', $province_id, $ward_id, '1'); // Only active stores
            
            if ($stores->num_rows > 0) {
                $response['success'] = true;
                while ($row = $stores->fetch_assoc()) {
                    $response['data'][] = $row;
                }
            } else {
                $response['message'] = 'Không tìm thấy Cửa hàng nào.';
            }
            break;

        default:
            $response['message'] = 'Hành động không hợp lệ.';
            break;
    }
} else {
    $response['message'] = 'Không có hành động được chỉ định.';
}

echo json_encode($response);
?>