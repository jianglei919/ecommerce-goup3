<?php
session_start();
require_once '../config/Database.php';

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
$orderQuery = $db->prepare("SELECT o.*, u.name AS user_name FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$orderQuery->bind_param("i", $order_id);
$orderQuery->execute();
$orderResult = $orderQuery->get_result();
$order = $orderResult->fetch_assoc();

if (!$order) {
    echo "<h2 class='text-center text-danger mt-5'>Order not found.</h2>";
    exit();
}

// Fetch order items
$itemQuery = $db->prepare("SELECT oi.*, p.name AS product_name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$itemQuery->bind_param("i", $order_id);
$itemQuery->execute();
$items = $itemQuery->get_result();
?>
<?php include '../includes/header.php'; ?>
<div class="container mt-4" style="min-height: 80vh;">
    <h3 class="mb-4">Order #<?= $order_id ?> Details</h3>
    <p><strong>Customer:</strong> <?= htmlspecialchars($order['user_name']) ?></p>
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