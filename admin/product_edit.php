<?php
session_start();

if (!isset($_SESSION['user']) || !$_SESSION['user']['is_admin']) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: products_admin.php");
    exit();
}

$id = (int) $_GET['id'];
$p = [];
$response = file_get_contents(filename:"http://localhost/ecommerce-goup3/api/products.php?id=$id");

if ($response) {
    $p = json_decode($response, true);
}

if (!$p || isset($p['message'])) {
    echo "<h3 class='text-danger text-center mt-5'>Product not found.</h3>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $name = $_POST['name'];
    $short_description = $_POST['short_description'];
    $long_description = $_POST['long_description'];
    $price = $_POST['price'];
    $photo = $p['photo'];

    if (!empty($_FILES['photo']['name'])) {
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $photoName = basename($_FILES['photo']['name']);
        $targetPath = $uploadDir . $photoName;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
            $photo = $photoName;
        }
    }

    $payload = json_encode([
        'id' => $id,
        'name' => $name,
        'short_description' => $short_description,
        'long_description' => $long_description,
        'price' => $price,
        'photo' => $photo
    ]);

    $ch = curl_init("http://localhost/ecommerce-goup3/api/products.php");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 || $httpCode === 204) {
        header("Location: products_admin.php");
        exit();
    } else {
        echo "<div class='alert alert-danger text-center mt-4'>Failed to update product. Server responded with HTTP $httpCode</div>";
        echo "<pre>$response</pre>";
    }
}
?>

<?php include '../includes/header.php'; ?>
<div class="container mt-4" style="max-width: 600px; min-height: 80vh;">
    <h2>Edit Product</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="name" class="form-label">Product Name</label>
            <input name="name" required class="form-control" value="<?= htmlspecialchars($p['name']) ?>">
        </div>
        <div class="mb-3">
            <label for="short_description" class="form-label">Short description</label>
            <textarea name="short_description" required
                class="form-control"><?= htmlspecialchars($p['short_description']) ?></textarea>
        </div>
        <div class="mb-3">
            <label for="long_description" class="form-label">Long description</label>
            <textarea name="long_description" required
                class="form-control"><?= htmlspecialchars($p['long_description']) ?></textarea>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="number" step="0.01" name="price" required class="form-control"
                value="<?= htmlspecialchars($p['price']) ?>">
        </div>
        <div class="mb-3">
            <label for="photo" class="form-label">Photo (leave blank to keep current)</label>
            <input type="file" name="photo" class="form-control">
            <?php if ($p['photo']): ?>
                <div class="mt-2">
                    <img src="../uploads/<?= htmlspecialchars($p['photo']) ?>" alt="" style="height: 120px;">
                </div>
            <?php endif; ?>
        </div>
        <button class="btn btn-primary" name="update"><i class="bi bi-arrow-clockwise"></i> &nbsp; Update Product</button>
        <a href="products_admin.php" class="btn btn-secondary">Cancel &nbsp; <i class="bi bi-x-circle"></i></a>
    </form>
</div>
<?php include '../includes/footer.php'; ?>