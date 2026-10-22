<?php  
    require_once '../commonCode.php';
    
    if(isset($_GET['id'])){
        $categoryId = $_GET['id'];
        $selectQuery = "SELECT * FROM `categories` WHERE `id` = $categoryId";
        $result = isset($conn) ? $conn->query($selectQuery) : null;
        
        if($result && $result->num_rows > 0){
            $category = $result->fetch_assoc();
            echo json_encode(['success' => true, 'data' => $category]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Category not found']);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Category ID not provided']);
    }
?>