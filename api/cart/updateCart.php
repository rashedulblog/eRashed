<?php
session_start();
$id = $_GET['id'];
if (!isset($_SESSION['cart'][$id])) {
    echo "error";
    exit();
}
$count = $_GET['count'];
$_SESSION['cart'][$id] = $count;
echo "Success";
