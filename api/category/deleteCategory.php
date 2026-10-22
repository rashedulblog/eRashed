<?php  
    require_once '../commonCode.php';

    $data = json_decode(file_get_contents('php://input'), true);

    if(isset($data['id'])){
        $categoryId = $data['id'];

        $deleteQuery = "DELETE FROM `categories` WHERE `id` = $categoryId";
        $result = isset($conn) ? $conn->query($deleteQuery) : null;

        if($result){
            echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete category']);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Category ID not provided']);
    }
?>