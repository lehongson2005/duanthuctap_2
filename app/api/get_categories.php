<?php
include_once '../config/db.php';
include_once '../models/CategoryModel.php';
include_once '../models/CategoryLevel2Model.php';
include_once '../models/CategoryLevel3Model.php';

header('Content-Type: application/json');

$level = isset($_GET['level']) ? (int)$_GET['level'] : 0;
$parent_id = isset($_GET['parent_id']) ? (int)$_GET['parent_id'] : 0;

$categories = [];

if ($parent_id > 0) {
    if ($level == 2) { // Requesting Level 2 categories based on Level 1 parent
        $categoryLevel2Model = new CategoryLevel2Model($conn);
        $result = $categoryLevel2Model->searchAndFilter('', $parent_id, 1); // Only active categories
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $categories[] = ['id' => $row['id'], 'name' => $row['name']];
            }
        }
    } elseif ($level == 3) { // Requesting Level 3 categories based on Level 2 parent
        $categoryLevel3Model = new CategoryLevel3Model($conn);
        $result = $categoryLevel3Model->searchAndFilter('', $parent_id, 1); // Only active categories
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $categories[] = ['id' => $row['id'], 'name' => $row['name']];
            }
        }
    }
}

echo json_encode($categories);
?>