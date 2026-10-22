<?php  
    
    require_once '../commonCode.php';
    $id = $_GET['id'];

    $getProductsQuery = "SELECT * FROM `products` WHERE `id` = $id";
    $result = isset($conn) ? $conn->query($getProductsQuery) : null;
    echo json_encode(['success' => true, 'data' => $result ? $result->fetch_assoc() : []]);
?>