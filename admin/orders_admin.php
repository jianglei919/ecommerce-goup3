<?php
session_start();
require_once '../config/Database.php';

if (!isset($_SESSION['user']) || !$_SESSION['user']['is_admin']) {
    header("Location: ../login.php");
    exit();
}

$db = (new Database())->getConnection();

// 获取所有订单及其用户信息
$sql = "
  SELECT orders.id, users.name AS customer_name, orders.total, orders.created_at 
  FROM orders
  JOIN users ON orders.user_id = users.id
  ORDER BY orders.created_at DESC
";

$result = $db->query($sql);
?>
<?php include '../includes/header.php'; ?>
<div class="container mt-4" style="min-height: 80vh;">
    <h2 class="mb-4">Order Management</h2>
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Total Amount</th>
                <th>Date</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td>$<?= number_format($row['total'], 2) ?></td>
                    <td><?= $row['created_at'] ?></td>
                    <td><a href="order_details.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">View</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include '../includes/footer.php'; ?>