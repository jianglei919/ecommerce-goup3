<?php
// cart_view.php
session_start();
require_once 'config/Database.php';
require_once 'classes/Product.php';

$db = (new Database())->getConnection();
$product = new Product($db);
$cart = $_SESSION['cart'] ?? [];
$total = 0;

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
                $item = $product->getProduct($id);
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
    <h4>Total: $<?= number_format($total, 2) ?></h4>
    <div class="d-flex justify-content-end mt-auto">
      <div>
        <a href="checkout.php" class="btn btn-success">Checkout</a>
      </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>