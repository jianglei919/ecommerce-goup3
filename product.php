<?php
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int) $_GET['id'];
$apiUrl = "http://localhost/ecommerce-goup3/api/products.php?id=" . $id;
$response = file_get_contents($apiUrl);
$product = json_decode($response, true);

if (!$product || isset($product['message'])) {
    echo "<h2 class='text-center mt-5 text-danger'>Product not found</h2>";
    exit();
}

$imgPath = 'uploads/' . htmlspecialchars($product['photo']);
if (!file_exists($imgPath) || empty($product['photo'])) {
    $imgPath = 'uploads/default.jpg';
}
?>
<?php include 'includes/header.php'; ?>
<div class="container mt-5" style="min-height: 80vh;">
    <div class="row">
        <div class="col-md-6">
            <img src="<?= $imgPath ?>" class="img-fluid rounded shadow-sm"
                alt="<?= htmlspecialchars($product['name']) ?>">
        </div>
        <div class="col-md-6">
            <h2 class="text-primary fw-bold mb-3"><?= htmlspecialchars($product['name']) ?></h2>
            <h4 class="text-success mb-3">$<?= number_format($product['price'], 2) ?></h4>
            <p class="text-muted"><?= nl2br(htmlspecialchars($product['description'])) ?></p>

            <form method="POST" action="cart_add.php" class="mt-4">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <div class="d-flex align-items-center gap-3">
                    <label for="quantity" class="form-label mb-0">Quantity:</label>
                    <input type="number" name="quantity" id="quantity" value="1" min="1" class="form-control"
                        style="width: 80px;">
                    <button class="btn btn-primary"><i class="bi bi-cart-plus me-1"></i> Add to Cart</button>
                </div>
            </form>

            <a href="index.php" class="btn btn-outline-secondary mt-4"><i class="bi bi-arrow-left"></i> Back to Home</a>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>