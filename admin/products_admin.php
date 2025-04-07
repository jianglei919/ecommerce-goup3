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

if (isset($_GET['delete'])) {
  $product->deleteProduct($_GET['delete']);
  header("Location: products_admin.php");
  exit();
}

$limit = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$products = $product->getAllProducts($limit, $offset);
$total = $product->countAllProducts();
$total_pages = ceil($total / $limit);
?>

<?php include '../includes/header.php'; ?>
<div class="container mt-4" style="min-height: 80vh;">
  <h2>Product Management</h2>
  <a href="product_add.php" class="btn btn-success mb-3">+ Add Product</a>

  <h4 class="mt-4">All Products</h4>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Name</th>
        <th>description</th>
        <th>Price</th>
        <th style="width: 150px;">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $p): ?>
        <tr>
          <td><?= htmlspecialchars($p['name']) ?></td>
          <td><?= htmlspecialchars($p['description']) ?></td>
          <td>$<?= htmlspecialchars($p['price']) ?></td>
          <td>
            <a href="product_edit.php?id=<?= $p['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
            <a href="?delete=<?= $p['id'] ?>" class="btn btn-danger btn-sm"
              onclick="return confirm('Are you sure?')">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <nav>
    <ul class="pagination justify-content-center mt-4">
      <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
      </li>
      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
      <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
      </li>
    </ul>
  </nav>
</div>
<?php include '../includes/footer.php'; ?>