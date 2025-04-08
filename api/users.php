<?php
require_once '../config/Database.php';
require_once '../classes/User.php';

header("Content-Type: application/json; charset=UTF-8");

$db = (new Database())->getConnection();
$user = new User($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST' && isset($_GET['action']) && $_GET['action'] === 'login') {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['username'], $input['password'])) {
        http_response_code(400);
        echo json_encode(["message" => "Missing username or password"]);
        exit();
    }

    $loginResult = $user->login($input['username'], $input['password']);
    if ($loginResult) {
        unset($loginResult['password']);
        echo json_encode(["success" => true, "user" => $loginResult]);
    } else {
        http_response_code(401);
        echo json_encode(["message" => "Invalid credentials"]);
    }
    exit();
}

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $data = $user->getUser((int) $_GET['id']);
            echo json_encode($data ?: ["message" => "User not found"]);
        } else {
            echo json_encode($user->getAllUsers());
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents("php://input"), true);
        if (isset($input['username'], $input['password'], $input['name'], $input['address'], $input['phone'], $input['email'])) {
            $success = $user->register(
                $input['username'],
                $input['password'],
                $input['name'],
                $input['address'],
                $input['phone'],
                $input['email']
            );
            if ($success === "duplicate") {
                http_response_code(409);
                echo json_encode(["message" => "Username or email already exists"]);
            } else {
                echo json_encode(["success" => $success]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Missing required fields"]);
        }
        break;

    case 'PUT':
        $input = json_decode(file_get_contents("php://input"), true);
        if (isset($input['id'], $input['is_admin'])) {
            $success = $user->updateAdminStatus($input['id'], $input['is_admin']);
            echo json_encode(["success" => $success]);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Missing required fields"]);
        }
        break;

    case 'PATCH':
        $input = json_decode(file_get_contents("php://input"), true);
        if (!isset($input['id'])) {
            http_response_code(400);
            echo json_encode(["message" => "Missing user ID"]);
            break;
        }

        $fields = ['name', 'email', 'address', 'phone'];
        $updates = [];

        foreach ($fields as $field) {
            if (isset($input[$field])) {
                $updates[$field] = $input[$field];
            }
        }

        if (empty($updates)) {
            http_response_code(400);
            echo json_encode(["message" => "No valid fields to update"]);
            break;
        }

        $success = $user->updateById($input['id'], $updates);
        echo json_encode(["success" => $success]);
        break;

    case 'DELETE':
        $input = json_decode(file_get_contents("php://input"), true);
        if (isset($input['id'])) {
            $success = $user->deleteUser($input['id']);
            echo json_encode(["success" => $success]);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Missing ID"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}