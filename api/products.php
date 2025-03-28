<?php
require_once '../config/Database.php';
require_once '../classes/Product.php';

header("Content-Type: application/json; charset=UTF-8");
$db = (new Database())->getConnection();
$product = new Product($db);
$result = $product->getAllProducts();
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
echo json_encode($products);
?>