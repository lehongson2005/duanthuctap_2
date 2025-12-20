<?php
// hethongcuahang.php
session_start(); // Start session to get success/error messages if any

require_once __DIR__ . '/../../../../app/config/db.php';
require_once __DIR__ . '/../../../../app/models/ProvinceModel.php';
require_once __DIR__ . '/../../../../app/models/WardModel.php';
require_once __DIR__ . '/../../../../app/models/StoreModel.php';

$provinceModel = new ProvinceModel($conn);
$wardModel = new WardModel($conn);
$storeModel = new StoreModel($conn);

$page_title = "Hệ Thống Cửa Hàng";

// Get all provinces to initially populate the dropdown
$allProvinces = $provinceModel->getAll();



?>

<?php include '../header.php'; ?>

<div class="container my-5">
    <h2 class="text-center mb-4">Hệ Thống Cửa Hàng</h2>

    <div class="row my-5">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bộ lọc Cửa hàng</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="province-select">Chọn Tỉnh/Thành phố:</label>
                        <select class="form-control" id="province-select">
                            <option value="">Tất cả Tỉnh/Thành phố</option>
                            <?php $allProvinces->data_seek(0); // Reset pointer for reuse ?>
                            <?php while($province = $allProvinces->fetch_assoc()): ?>
                                <option value="<?= $province['id'] ?>"><?= htmlspecialchars($province['name']) ?> (<?= htmlspecialchars($province['type']) ?>)</option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group mt-3">
                        <label for="ward-select">Chọn Xã/Phường:</label>
                        <select class="form-control" id="ward-select" disabled>
                            <option value="">Tất cả Xã/Phường</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow mb-4">
                 <div class="card-body p-0">
                    <div id="map" style="height: 600px; width: 100%;"></div>
                 </div>
            </div>
        </div>
    </div>
</div>

<!-- Assuming jQuery is available in your header.php or footer.php, if not, it needs to be added -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
let map;
let markers = [];
let infoWindow;

// This function is called by the Google Maps script callback
function initMap() {
    const vietnamCenter = { lat: 16.047079, lng: 108.206230 }; // Default to Da Nang
    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 6,
        center: vietnamCenter,
    });
    infoWindow = new google.maps.InfoWindow();

    // Initial load of stores
    loadStores('', '');
}

$(document).ready(function() {
    const provinceSelect = $('#province-select');
    const wardSelect = $('#ward-select');

    function clearMarkers() {
        for (let i = 0; i < markers.length; i++) {
            markers[i].setMap(null);
        }
        markers = [];
    }

    function loadWards(provinceId) {
        if (!provinceId) {
            wardSelect.html('<option value="">Tất cả Xã/Phường</option>').prop('disabled', true);
            return;
        }
        $.ajax({
            url: '../../../api/get_location_data.php',
            type: 'GET',
            dataType: 'json',
            data: { action: 'get_wards_by_province', province_id: provinceId },
            success: function(response) {
                wardSelect.html('<option value="">Tất cả Xã/Phường</option>');
                if (response.success && response.data.length > 0) {
                    $.each(response.data, function(index, ward) {
                        wardSelect.append($('<option>', {
                            value: ward.id,
                            text: ward.name + ' (' + ward.type + ')'
                        }));
                    });
                    wardSelect.prop('disabled', false);
                } else {
                    wardSelect.prop('disabled', true);
                }
            },
            error: function() {
                console.error('Lỗi khi tải danh sách Xã/Phường.');
                wardSelect.html('<option value="">Tất cả Xã/Phường</option>').prop('disabled', true);
            }
        });
    }

    function loadStores(provinceId, wardId) {
        $.ajax({
            url: '../../../api/get_location_data.php',
            type: 'GET',
            dataType: 'json',
            data: { 
                action: 'get_stores_by_location', 
                province_id: provinceId, 
                ward_id: wardId 
            },
            success: function(response) {
                clearMarkers();
                const bounds = new google.maps.LatLngBounds();
                let hasValidStores = false;

                if (response.success && response.data.length > 0) {
                    response.data.forEach(function(store) {
                        if (store.latitude && store.longitude) {
                            hasValidStores = true;
                            const latLng = new google.maps.LatLng(parseFloat(store.latitude), parseFloat(store.longitude));
                            
                            const marker = new google.maps.Marker({
                                position: latLng,
                                map: map,
                                title: store.name
                            });

                            const contentString = `
                                <div>
                                    <h5>${store.name}</h5>
                                    <p><strong>Địa chỉ:</strong> ${store.address}, ${store.ward_name}, ${store.province_name}</p>
                                    <p><strong>Điện thoại:</strong> ${store.phone_number || 'N/A'}</p>
                                    ${store.map_link ? `<p><a href="${store.map_link}" target="_blank">Xem trên Google Maps</a></p>` : ''}
                                </div>
                            `;

                            marker.addListener("click", () => {
                                infoWindow.setContent(contentString);
                                infoWindow.open(map, marker);
                            });

                            markers.push(marker);
                            bounds.extend(marker.getPosition());
                        }
                    });
                }
                
                if (hasValidStores) {
                    map.fitBounds(bounds);
                }
            },
            error: function() {
                console.error('Lỗi khi tải danh sách Cửa hàng.');
            }
        });
    }

    // Event Listeners
    provinceSelect.on('change', function() {
        const selectedProvinceId = $(this).val();
        loadWards(selectedProvinceId);
        loadStores(selectedProvinceId, ''); // Load stores for the whole province
    });

    wardSelect.on('change', function() {
        const selectedProvinceId = provinceSelect.val();
        const selectedWardId = $(this).val();
        loadStores(selectedProvinceId, selectedWardId);
    });
});
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo defined('GOOGLE_MAPS_API_KEY') ? GOOGLE_MAPS_API_KEY : ''; ?>&callback=initMap"></script>

<?php include '../footer.php'; ?>
