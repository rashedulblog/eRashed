<?php
require_once '../commonCode.php';
$id = $_POST['id'];
$name = $_POST['name'];
$regular_price = $_POST['regular_price'];
$discount_price = $_POST['discount_price'];
$category_id = $_POST['category_id'];



// update product into database
$sql = "UPDATE `products` SET `name` = '$name', `regular_price` = $regular_price, `discount_price` = $discount_price, `category_id` = $category_id WHERE `id` = $id";
if (isset($conn) && $conn->query($sql)) {
    echo json_encode(["success" => true, "message" => "Product updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Error: Product not updated. " . (isset($conn) ? $conn->error : "Database connection error")]);
}
