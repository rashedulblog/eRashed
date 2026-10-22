<?php
$conn = mysqli_connect("localhost", "root", "", "project337");
// accept all requests from any origin
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
