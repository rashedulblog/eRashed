<?php
require_once '../commonCode.php';
// fields: `id`, `name`, `regular_price`, `discount_price`, `category_id`, `image`, `created_at`

// $data = json_decode(file_get_contents('php://input'), true);

// print_r($_POST);
// echo "<br><br>";
// print_r($_FILES);

/*
Array
(
    [name] => Laptop
    [regular_price] => 60000
    [discount_price] => 50000
    [category_id] => 1
)
<br><br>Array
(
    [image] => Array
        (
            [name] => laptop2.webp
            [full_path] => laptop2.webp
            [type] => image/webp
            [tmp_name] => C:\xampp\tmp\php5100.tmp
            [error] => 0
            [size] => 23286
        )

)
*/

$name = $_POST['name'];
$regular_price = $_POST['regular_price'];
$discount_price = $_POST['discount_price'];
$category_id = $_POST['category_id'];
$image = $_FILES['image'];

// upload image to server
$target_dir = "../../assets/images/products";
$new_file_name = time() . "_" . uniqid() . "_" . $image['name'];
$target_file = $target_dir . "/" . $new_file_name;

if (move_uploaded_file($image['tmp_name'], $target_file)) {
    // insert product into database
    $sql = "INSERT INTO products (name, regular_price, discount_price, category_id, image) VALUES ('$name', '$regular_price', '$discount_price', '$category_id', '$new_file_name')";
    if (isset($conn) && $conn->query($sql)) {
        echo json_encode(["success" => true, "message" => "Product added successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: Product not added. " . (isset($conn) ? $conn->error : "Database connection error")]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Sorry, there was an error uploading your file."]);
}
