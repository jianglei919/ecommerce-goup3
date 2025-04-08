<?php
session_start();

if (!isset($_SESSION['user']) || !$_SESSION['user']['is_admin']) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: orders_admin.php");
    exit();
}

$order_id = (int) $_GET['id'];

// Fetch order & user info
$order = [];
$items = [];

$apiUrl = "http://localhost/ecommerce-goup3/api/orders.php?id=" . $order_id;
$response = file_get_contents($apiUrl);
if ($response) {
    $data = json_decode($response, true);
    if (isset($data['id'])) {
        $order = $data;
        $items = $data['items'] ?? [];
    }
}

if (!$order) {
    echo "<h2 class='text-center text-danger mt-5'>Order not found.</h2>";
    exit();
}
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
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>$<?= number_format($item['price'], 2) ?></td>
                    <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="orders_admin.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Orders</a>
</div>
<?php include '../includes/footer.php'; ?>