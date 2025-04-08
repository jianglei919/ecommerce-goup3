<?php
session_start();

if (!isset($_SESSION['user']) || !$_SESSION['user']['is_admin']) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $photo = '';

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
        'name' => $name,
        'description' => $description,
        'price' => $price,
        'photo' => $photo
    ]);

    $ch = curl_init('http://localhost/ecommerce-goup3/api/products.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 || $httpCode === 201) {
        header("Location: products_admin.php");
        exit();
    } else {
        echo "<div class='alert alert-danger text-center mt-4'>Failed to add product. Server responded with HTTP $httpCode</div>";
        echo "<pre>$response</pre>";
    }
}
?>

<?php include '../includes/header.php'; ?>
<div class="container mt-4" style="max-width: 600px; min-height: 80vh;">
    <h2>Add Product</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="name" class="form-label">Product Name</label>
            <input name="name" required class="form-control" placeholder="Product Name">
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" required class="form-control" placeholder="Description"></textarea>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="number" step="0.01" name="price" required class="form-control" placeholder="Price">
        </div>
        <div class="mb-3">
            <label for="photo" class="form-label">Photo</label>
            <input type="file" name="photo" class="form-control">
        </div>
        <button class="btn btn-primary" name="create">Add Product</button>
        <a href="products_admin.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php include '../includes/footer.php'; ?>