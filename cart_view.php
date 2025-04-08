<?php
// cart_view.php
session_start();
require_once 'config/Database.php';

$cart = $_SESSION['cart'] ?? [];
$total = 0;
$productData = [];

foreach ($cart as $id => $qty) {
    $response = file_get_contents("http://localhost/ecommerce-goup3/api/products.php?id=" . $id);
    $item = json_decode($response, true);
    if ($item) {
        $productData[$id] = $item;
    }
}

?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4 d-flex flex-column justify-content-between" style="min-height: 80vh;">
    <h2>My Cart</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cart as $id => $qty):
                $item = $productData[$id];
                $subtotal = $item['price'] * $qty;
                $total += $subtotal;
                ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td>$<?= $item['price'] ?></td>
                    <td><?= $qty ?></td>
                    <td>$<?= number_format($subtotal, 2) ?></td>
                    <td><a href="remove_from_cart.php?id=<?= $id ?>" class="btn btn-danger btn-sm">Remove</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if (count($cart) > 0): ?>
        <?php
          $hst = $total * 0.13;
          $grand_total = $total + $hst;
        ?>
        <h5>Subtotal: $<?= number_format($total, 2) ?></h5>
        <h5>HST (13%): $<?= number_format($hst, 2) ?></h5>
        <h4>Total: $<?= number_format($grand_total, 2) ?></h4>
        <div class="d-flex justify-content-end mt-auto">
          <div>
            <a href="checkout.php" class="btn btn-success">Checkout</a>
          </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Your cart is empty.</div>
        <div class="d-flex justify-content-end mt-auto">
          <button class="btn btn-success" disabled>Checkout</button>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>