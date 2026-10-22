<?php
session_start();
require_once("./db.php");
if (!isset($_GET['id'])) {
    header("location: ./");
    exit();
}
$id = $_GET['id'];
$getPro = db::conn()->query("SELECT * FROM `products` WHERE `id` = $id");

if ($getPro->num_rows === 0) {
    header("location: ./");
    exit();
}

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

if (isset($_SESSION["cart"][$id])) {
    $_SESSION["cart"][$id] += 1;
} else {
    $_SESSION["cart"][$id] = 1;
}

header("location: ./?addCart=1");