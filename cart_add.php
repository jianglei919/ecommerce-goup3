<?php
// cart_add.php
session_start();
$id = $_POST['product_id'];
$qty = $_POST['quantity'];

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id] += $qty;
} else {
    $_SESSION['cart'][$id] = $qty;
}

header("Location: cart_view.php");
exit();

?>