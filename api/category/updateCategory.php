<?php  
    require_once '../commonCode.php';

    $data = json_decode(file_get_contents('php://input'), true);
    
    if(isset($data['id'])){
        $categoryId = $data['id'];
        $name = $data['data']['name'];

        $updateQuery = "UPDATE `categories` SET `name` = '$name' WHERE `id` = $categoryId";
        $result = isset($conn) ? $conn->query($updateQuery) : null;

        if($result){
            echo json_encode(['success' => true, 'message' => 'Category updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update category']);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Category ID not provided']);
    }
?>