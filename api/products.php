<?php
require_once '../config/Database.php';
require_once '../classes/Product.php';

header("Content-Type: application/json; charset=UTF-8");
$db = (new Database())->getConnection();
$product = new Product($db);
$products = $product->getAllProducts();
echo json_encode($products);
?>