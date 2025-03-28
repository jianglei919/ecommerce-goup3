<?php
session_start();
require_once '../config/Database.php';
require_once '../classes/Product.php';

$db = (new Database())->getConnection();
$product = new Product($db);

if (!isset($_SESSION['user']) || !$_SESSION['user']['is_admin']) {
  header("Location: ../login.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
  $name = $_POST['name'];
  $desc = $_POST['description'];
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
  $product->createProduct($name, $desc, $price, $photo);
  header("Location: products_admin.php");
  exit();
}

if (isset($_GET['delete'])) {
  $product->deleteProduct($_GET['delete']);
  header("Location: products_admin.php");
  exit();
}

$products = $product->getAllProducts();
?>

<?php include '../includes/header.php'; ?>
<div class="container mt-4" style="min-height: 80vh;">
  <h2>Product Management</h2>
  <form method="POST" enctype="multipart/form-data">
    <input name="name" required class="form-control mb-2" placeholder="Product Name">
    <textarea name="description" required class="form-control mb-2" placeholder="Description"></textarea>
    <input type="number" step="0.01" name="price" required class="form-control mb-2" placeholder="Price">
    <input type="file" name="photo" class="form-control mb-2">
    <button class="btn btn-primary" name="create">Add Product</button>
  </form>

  <h4 class="mt-4">All Products</h4>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Name</th>
        <th>Price</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $p): ?>
        <tr>
          <td><?= htmlspecialchars($p['name']) ?></td>
          <td>$<?= htmlspecialchars($p['price']) ?></td>
          <td><a href="?delete=<?= $p['id'] ?>" class="btn btn-danger btn-sm"
              onclick="return confirm('Are you sure?')">Delete</a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include '../includes/footer.php'; ?>