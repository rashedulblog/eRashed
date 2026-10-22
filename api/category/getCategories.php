<?php  
    require_once '../commonCode.php';
    
    $selectQuery = "SELECT * FROM `categories`";
    $result = isset($conn) ? $conn->query($selectQuery) : null;
    
    if($result && $result->num_rows > 0){
        $categories = [];
        while($row = $result->fetch_assoc()){
            $categories[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $categories]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No categories found']);
    }
?>