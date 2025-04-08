<?php
session_start();

if (!isset($_SESSION['user']) || !$_SESSION['user']['is_admin']) {
    header("Location: ../login.php");
    exit();
}

$result = [];
$response = file_get_contents("http://localhost/ecommerce-goup3/api/orders.php");
if ($response) {
    $result = json_decode($response, true);
}
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
            <?php foreach ($result as $row): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td>$<?= number_format($row['total'], 2) ?></td>
                    <td><?= $row['created_at'] ?></td>
                    <td><a href="order_admin_details.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">View</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include '../includes/footer.php'; ?>