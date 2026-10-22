<?php
require_once __DIR__ . '/../commonCode.php';

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit;
}

$safeId = (int)$id;
$orderQuery = "SELECT orders.*, shipping.* 
               FROM `orders` 
               LEFT JOIN `shipping` ON `shipping`.`order_id` = `orders`.`id` 
               WHERE `orders`.`id` = $safeId";
$orderRes = isset($conn) ? $conn->query($orderQuery) : null;

if ($orderRes && $orderRes->num_rows > 0) {
    $order = $orderRes->fetch_assoc();
    
    $itemsQuery = "SELECT order_items.*, products.name as product_name, products.image as product_image 
                   FROM `order_items` 
                   LEFT JOIN `products` ON `products`.`id` = `order_items`.`product_id` 
                   WHERE `order_items`.`order_id` = $safeId";
    $itemsRes = $conn->query($itemsQuery);
    $items = [];
    if ($itemsRes && $itemsRes->num_rows > 0) {
        while ($it = $itemsRes->fetch_assoc()) {
            $items[] = $it;
        }
    }
    $order['items'] = $items;
    echo json_encode(['success' => true, 'data' => $order]);
} else {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
}
?>
