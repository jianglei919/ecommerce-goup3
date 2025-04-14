<?php
require_once '../config/Database.php';
require_once '../classes/Product.php';

header("Content-Type: application/json; charset=UTF-8");

$db = (new Database())->getConnection();
$product = new Product($db);

// Determine request method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
  case 'GET':
    if (isset($_GET['id'])) {
      $data = $product->getProduct((int) $_GET['id']);
      echo json_encode($data ?: ["message" => "Product not found"]);
    } elseif (isset($_GET['search'])) {
      $data = $product->searchProducts($_GET['search']);
      echo json_encode($data);
    } else {
      $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 20;
      $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;
      echo json_encode($product->getAllProducts($limit, $offset));
    }
    break;

  case 'POST':
    $input = json_decode(file_get_contents("php://input"), true);
    if (isset($input['name'], $input['short_description'], $input['long_description'], $input['price'])) {
      $created = $product->createProduct($input['name'], $input['short_description'], $input['long_description'], $input['price'], $input['photo'] ?? '');
      echo json_encode(["success" => $created]);
    } else {
      http_response_code(400);
      echo json_encode(["message" => "Missing fields"]);
    }
    break;

  case 'PUT':
    $input = json_decode(file_get_contents("php://input"), true);
    if (!empty($input['id']) && !empty($input['name']) && isset($input['short_description'], $input['long_description'], $input['price'])) {
      $updated = $product->updateProduct(
        id: (int) $input['id'],
        name: $input['name'],
        short_description: $input['short_description'],
        long_description: $input['long_description'],
        price: $input['price'],
        photo: $input['photo'] ?? ''
      );
      echo json_encode(value: ["success" => $updated]);
    } else {
      http_response_code(400);
      echo json_encode(["message" => "Missing fields for update"]);
    }
    break;

  default:
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
}