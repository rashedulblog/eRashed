<?php
session_start();
header("Content-Type: application/json");

if (!isset($_GET['id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Product ID is required"
    ]);
    exit();
}

$id = (int)$_GET['id'];

if (isset($_SESSION['cart'][$id])) {
    unset($_SESSION['cart'][$id]);
    echo json_encode([
        "success" => true,
        "message" => "Product removed from cart"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Product not found in cart"
    ]);
}
