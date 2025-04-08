<?php

class Order
{
    private $conn;
    private $table = "orders";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // 获取所有订单及其用户信息
    public function getAllOrdersWithUser()
    {
        $query = "SELECT o.id, u.name AS customer_name, o.total, o.created_at
                  FROM " . $this->table . " o
                  JOIN users u ON o.user_id = u.id
                  ORDER BY o.created_at DESC";

        $result = $this->conn->query($query);
        return $result;
    }

    // 根据订单ID获取详细信息
    public function getOrderById($id)
    {
        $query = "SELECT o.*, u.name AS customer_name
                  FROM " . $this->table . " o
                  JOIN users u ON o.user_id = u.id
                  WHERE o.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // 获取订单的所有商品明细
    public function getOrderItems($order_id)
    {
        $query = "SELECT oi.*, p.name AS product_name
                  FROM order_items oi
                  JOIN products p ON oi.product_id = p.id
                  WHERE oi.order_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    // 保存订单支付信息
    public function savePaymentInfo($userId, $fullname, $phone, $address, $city, $postal, $payment, $card, $expiry, $cvv, $note, $total)
    {
        $query = "INSERT INTO orders_payment (user_id, fullname, phone, address, city, postal, payment, card, expiry, cvv, note, total, created_at)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isssssssssds", $userId, $fullname, $phone, $address, $city, $postal, $payment, $card, $expiry, $cvv, $note, $total);
        return $stmt->execute();
    }

    // 创建订单
    public function createOrder($userId, $total)
    {
        $query = "INSERT INTO " . $this->table . " (user_id, total, created_at)
                  VALUES (?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("id", $userId, $total);
        $stmt->execute();
        return $stmt->insert_id;
    }

    // 保存订单项
    public function saveOrderItems($order_id, $items, $productClass)
    {
        foreach ($items as $item) {
            $product = $productClass->getProduct($item['product_id']);

            if (!$product) {
                continue;
            }

            $price = $product['price'];
            $quantity = $item['quantity'];

            $stmt = $this->conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                continue;
            }

            $stmt->bind_param("iiid", $order_id, $item['product_id'], $quantity, $price);
            $stmt->execute();
        }
    }

}
