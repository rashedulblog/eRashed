<?php
require_once '../commonCode.php';

header('Content-Type: application/json');

$categoryId = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($categoryId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Category ID not provided or invalid']);
    exit;
}

if (!isset($_FILES['image']) || !is_uploaded_file($_FILES['image']['tmp_name'])) {
    echo json_encode(['success' => false, 'message' => 'Image file not provided']);
    exit;
}

$image = $_FILES['image'];

if ($image['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Image upload error: ' . $image['error']]);
    exit;
}

if (!getimagesize($image['tmp_name'])) {
    echo json_encode(['success' => false, 'message' => 'Uploaded file is not a valid image']);
    exit;
}

$targetDir = __DIR__ . '/../../assets/images/categories';
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

$originalName = basename($image['name']);
$extension = pathinfo($originalName, PATHINFO_EXTENSION);
$safeBaseName = preg_replace('/[^A-Za-z0-9_-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
$newFileName = time() . '_' . uniqid() . '_' . $safeBaseName;
if ($extension !== '') {
    $newFileName .= '.' . $extension;
}

$targetFile = $targetDir . '/' . $newFileName;

if (!move_uploaded_file($image['tmp_name'], $targetFile)) {
    echo json_encode(['success' => false, 'message' => 'Failed to move uploaded image']);
    exit;
}

$escapedFileName = isset($conn) ? mysqli_real_escape_string($conn, $newFileName) : $newFileName;
$updateQuery = "UPDATE `categories` SET `image` = '$escapedFileName' WHERE `id` = $categoryId";
$result = isset($conn) ? $conn->query($updateQuery) : null;

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Category image updated successfully', 'image' => $newFileName]);
} else {
    if (file_exists($targetFile)) {
        unlink($targetFile);
    }
    echo json_encode(['success' => false, 'message' => 'Failed to update category image']);
}

