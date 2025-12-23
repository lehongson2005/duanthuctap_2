<?php
// This script handles image uploads from the Summernote editor.

header('Content-Type: application/json');

// Include the database config to get BASE_URL, even though we don't use the DB itself.
include_once '../config/db.php';

// Check if a file was uploaded
if (empty($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No file was uploaded.']);
    exit;
}

$file = $_FILES['file'];

// Check for upload errors
if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred during file upload.']);
    exit;
}

// Define the upload directory
// We need to go up from 'app/api/' to the project root.
$upload_dir_server_path = dirname(__DIR__, 2) . '/public/uploads/post_images/';

// Create the directory if it doesn't exist
if (!is_dir($upload_dir_server_path)) {
    if (!mkdir($upload_dir_server_path, 0777, true)) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create upload directory.']);
        exit;
    }
}

// Validate the file is an image
$mime_type = mime_content_type($file['tmp_name']);
$allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($mime_type, $allowed_mime_types)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.']);
    exit;
}

// Generate a unique filename to prevent overwriting
$file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$unique_filename = uniqid('img_', true) . '.' . $file_extension;
$target_path = $upload_dir_server_path . $unique_filename;

// Move the uploaded file to the target directory
if (move_uploaded_file($file['tmp_name'], $target_path)) {
    // Construct the public URL for the image
    // rtrim(BASE_URL, '/') ensures no double slashes
    $image_url = rtrim(BASE_URL, '/') . '/public/uploads/post_images/' . $unique_filename;

    // Return the URL in the format Summernote expects
    echo json_encode(['url' => $image_url]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to move uploaded file.']);
    exit;
}
?>
