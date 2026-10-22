<?php
require_once __DIR__ . '/../commonCode.php';

$status = isset($_GET['status']) ? trim($_GET['status']) : '';

$whereClause = "";
if (!empty($status) && strtolower($status) !== 'all') {
    $safeStatus = mysqli_real_escape_string($conn, $status);
    if (strcasecmp($status, 'pending') === 0) {
        $whereClause = "WHERE orders.status IN ('Pending', 'Processing')";
    } elseif (strcasecmp($status, 'shift') === 0 || strcasecmp($status, 'shifted') === 0 || strcasecmp($status, 'shipped') === 0) {
        $whereClause = "WHERE orders.status IN ('Shift', 'Shifted', 'Shipped')";
    } elseif (strcasecmp($status, 'delivered') === 0) {
        $whereClause = "WHERE orders.status = 'Delivered'";
    } elseif (strcasecmp($status, 'cancel') === 0 || strcasecmp($status, 'cancelled') === 0) {
        $whereClause = "WHERE orders.status IN ('Cancel', 'Cancelled', 'Failed', 'Falied', 'Canceled')";
    } else {
        $whereClause = "WHERE orders.status = '$safeStatus'";
    }
}

$query = "SELECT 
            orders.*, 
            shipping.id as shipping_id,
            shipping.customer_name as ship_customer_name,
            shipping.house_number as ship_house_number,
            shipping.street_address as ship_street_address,
            shipping.cus_city as ship_cus_city,
            shipping.cus_postcode as ship_cus_postcode,
            shipping.customer_mobile as ship_customer_mobile,
            shipping.customer_email as ship_customer_email,
            shipping.shipping_name,
            shipping.shipping_house_number,
            shipping.shipping_street_address,
            shipping.shipping_city,
            shipping.shipping_zip_code,
            shipping.shipping_mobile_number
          FROM `orders`
          LEFT JOIN `shipping` ON `shipping`.`order_id` = `orders`.`id`
          $whereClause
          ORDER BY `orders`.`id` DESC";

$result = isset($conn) ? $conn->query($query) : null;

$orders = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orderId = (int)$row['id'];
        
        // Fetch order items with product details
        $itemsQuery = "SELECT 
                        order_items.*, 
                        products.name as product_name,
                        products.image as product_image,
                        products.discount_price as product_price
                       FROM `order_items`
                       LEFT JOIN `products` ON `products`.`id` = `order_items`.`product_id`
                       WHERE `order_items`.`order_id` = $orderId";
        $itemsResult = $conn->query($itemsQuery);
        $items = [];
        if ($itemsResult && $itemsResult->num_rows > 0) {
            while ($item = $itemsResult->fetch_assoc()) {
                $items[] = $item;
            }
        }
        
        $row['items'] = $items;
        $row['items_count'] = count($items);
        $orders[] = $row;
    }
}

echo json_encode(['success' => true, 'data' => $orders]);
?>
