<?php
require_once __DIR__ . '/../commonCode.php';

$payload = json_decode(file_get_contents('php://input'), true);
$id = $payload['id'] ?? $_POST['id'] ?? null;
$status = $payload['status'] ?? $_POST['status'] ?? null;

if (!$id || !$status) {
    echo json_encode(['success' => false, 'message' => 'Order ID and status are required']);
    exit;
}

$safeId = (int)$id;
$safeStatus = mysqli_real_escape_string($conn, trim($status));

$sql = "UPDATE `orders` SET `status` = '$safeStatus' WHERE `id` = $safeId";
if (isset($conn) && $conn->query($sql)) {
    echo json_encode(['success' => true, 'message' => "Order #$safeId updated to $status"]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . ($conn ? $conn->error : 'DB error')]);
}
?>
