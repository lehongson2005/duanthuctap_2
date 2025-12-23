<?php
// Set headers to return JSON
header('Content-Type: application/json');

// Include necessary files
include_once '../config/db.php';

// Get parameters from the request
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Validate parameters
if ($id <= 0 || !in_array($type, ['post', 'cn_post'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid or missing parameters.']);
    exit;
}

$content = null;

// Fetch content based on the type
if ($type === 'post') {
    include_once '../models/PostModel.php';
    $model = new PostModel($conn);
    $data = $model->getById($id);
    if ($data) {
        $content = $data['content'];
    }
} elseif ($type === 'cn_post') {
    include_once '../models/CamNangPostModel.php';
    $model = new CamNangPostModel($conn);
    $data = $model->getById($id);
    if ($data) {
        $content = $data['content'];
    }
}

// If content is null, it means the post was not found
if ($content === null) {
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'Post not found.']);
    exit;
}

// Ensure the content is UTF-8 before encoding to JSON
// This prevents json_encode from failing on non-UTF8 characters
if (!mb_check_encoding($content, 'UTF-8')) {
    $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
}

// Return the content as a JSON object
echo json_encode(['content' => $content]);
?>
