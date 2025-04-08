<?php
require_once '../config/Database.php';
require_once '../classes/Order.php';

header("Content-Type: application/json; charset=UTF-8");

$db = (new Database())->getConnection();
$order = new Order($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $data = $order->getOrderById((int) $_GET['id']);
            if ($data) {
                $data['items'] = [];
                $items = $order->getOrderItems($data['id']);
                while ($item = $items->fetch_assoc()) {
                    $data['items'][] = $item;
                }
                echo json_encode($data);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Order not found"]);
            }
        } else {
            $result = $order->getAllOrdersWithUser();
            $orders = [];
            while ($row = $result->fetch_assoc()) {
                $orders[] = $row;
            }
            echo json_encode($orders);
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents("php://input"), true);
        if (!isset($input['user_id'], $input['total'], $input['items']) || !is_array($input['items'])) {
            http_response_code(400);
            echo json_encode(["message" => "Missing required fields"]);
            break;
        }

        // Optionally extract payment info if needed
        $userId = $input['user_id'];
        $total = $input['total'];
        $items = $input['items'];

        $fullname = $input['fullname'] ?? '';
        $phone = $input['phone'] ?? '';
        $address = $input['address'] ?? '';
        $city = $input['city'] ?? '';
        $postal = $input['postal'] ?? '';
        $payment = $input['payment'] ?? '';
        $card = $input['card'] ?? '';
        $expiry = $input['expiry'] ?? '';
        $cvv = $input['cvv'] ?? '';
        $note = $input['note'] ?? '';

        $order->savePaymentInfo($userId, $fullname, $phone, $address, $city, $postal, $payment, $card, $expiry, $cvv, $note, $total);

        $order_id = $order->createOrder($userId, $total);
        if (!$order_id) {
            http_response_code(500);
            echo json_encode(["message" => "Failed to create order"]);
            break;
        }

        if (!empty($items) && $order_id) {
            require_once '../classes/Product.php';
            $product = new Product($db);
            $order->saveOrderItems($order_id, $items, $product);
        }

        echo json_encode(["message" => "Order created", "order_id" => $order_id]);
        break;

    case 'PUT':
        $input = json_decode(file_get_contents("php://input"), true);
        if (!isset($input['id'], $input['total'])) {
            http_response_code(400);
            echo json_encode(["message" => "Missing order ID or total"]);
            break;
        }
        $stmt = $db->prepare("UPDATE orders SET total = ? WHERE id = ?");
        $stmt->bind_param("di", $input['total'], $input['id']);
        $success = $stmt->execute();
        echo json_encode(["success" => $success]);
        break;

    case 'DELETE':
        $input = json_decode(file_get_contents("php://input"), true);
        if (!isset($input['id'])) {
            http_response_code(400);
            echo json_encode(["message" => "Missing order ID"]);
            break;
        }
        $stmt = $db->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->bind_param("i", $input['id']);
        $success = $stmt->execute();
        echo json_encode(["success" => $success]);
        break;

    case 'PATCH':
        $input = json_decode(file_get_contents("php://input"), true);
        if (!isset($input['id'])) {
            http_response_code(400);
            echo json_encode(["message" => "Missing order ID"]);
            break;
        }

        $fields = [];
        $values = [];
        $types = '';

        if (isset($input['note'])) {
            $fields[] = 'note = ?';
            $values[] = $input['note'];
            $types .= 's';
        }

        if (isset($input['status'])) {
            $fields[] = 'status = ?';
            $values[] = $input['status'];
            $types .= 's';
        }

        if (empty($fields)) {
            http_response_code(400);
            echo json_encode(["message" => "No valid fields to update"]);
            break;
        }

        $values[] = $input['id'];
        $types .= 'i';

        $sql = "UPDATE orders SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param($types, ...$values);
        $success = $stmt->execute();
        echo json_encode(["success" => $success]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}