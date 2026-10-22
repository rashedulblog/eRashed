<?php  
    
    require_once '../commonCode.php';

    $getProductsQuery = "SELECT * FROM `products` ORDER BY `id` DESC";
    $result = isset($conn) ? $conn->query($getProductsQuery) : null;
    echo json_encode(['success' => true, 'data' => $result ? $result->fetch_all(MYSQLI_ASSOC) : []]);
?>