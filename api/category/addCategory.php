<?php  
require_once '../commonCode.php';

// get json data from payload
$data = json_decode(file_get_contents('php://input'), true);

    if($data){
        $name = $data['data']['name'];
        $insertQuery = "INSERT INTO `categories` (`name`) VALUES ('$name')";
        if(isset($conn) && $conn->query($insertQuery)){
            echo json_encode(['success' => true, 'message' => 'Category added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Something went wrong']);
        }
    }
?>