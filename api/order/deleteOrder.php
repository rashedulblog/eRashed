<?php
require_once __DIR__ . '/../commonCode.php';

$payload = json_decode(file_get_contents('php://input'), true);
$id = $payload['id'] ?? $_POST['id'] ?? $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit;
}

$safeId = (int)$id;

if (isset($conn)) {
    $conn->query("DELETE FROM `order_items` WHERE `order_id` = $safeId");
    $conn->query("DELETE FROM `shipping` WHERE `order_id` = $safeId");
    $result = $conn->query("DELETE FROM `orders` WHERE `id` = $safeId");

    if ($result) {
        echo json_encode(['success' => true, 'message' => "Order #$safeId deleted successfully"]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
}
?>
