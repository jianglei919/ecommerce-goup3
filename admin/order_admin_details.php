<?php
session_start();
require_once '../config/Database.php';
require_once '../classes/Order.php';

if (!isset($_SESSION['user']) || !$_SESSION['user']['is_admin']) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: orders_admin.php");
    exit();
}

$order_id = (int) $_GET['id'];
$db = (new Database())->getConnection();

// Fetch order & user info
$orderObj = new Order($db);
$order = $orderObj->getOrderById($order_id);

if (!$order) {
    echo "<h2 class='text-center text-danger mt-5'>Order not found.</h2>";
    exit();
}

// Fetch order items
$items = $orderObj->getOrderItems($order_id);
?>
<?php include '../includes/header.php'; ?>
<div class="container mt-4" style="min-height: 80vh;">
    <h3 class="mb-4">Order #<?= $order_id ?> Details</h3>
    <p><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
    <p><strong>Order Date:</strong> <?= $order['created_at'] ?></p>
    <p><strong>Total:</strong> $<?= number_format($order['total'], 2) ?></p>
    <hr>
    <h5>Items</h5>
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $items->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>$<?= number_format($item['price'], 2) ?></td>
                    <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a href="orders_admin.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Orders</a>
</div>
<?php include '../includes/footer.php'; ?>