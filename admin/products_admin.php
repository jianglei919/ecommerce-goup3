<?php
session_start();

if (isset($_GET['delete'])) {
  $deletePayload = json_encode(["id" => (int) $_GET['delete']]);
  $ch = curl_init("http://localhost/ecommerce-goup3/api/products.php");
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $deletePayload);
  curl_exec($ch);
  curl_close($ch);
  header("Location: products_admin.php");
  exit();
}

$limit = 6;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$allProducts = [];
$response = file_get_contents("http://localhost/ecommerce-goup3/api/products.php");
if ($response) {
  $allProducts = json_decode($response, true);
}
$total = count($allProducts);
$products = array_slice($allProducts, $offset, $limit);
$total_pages = ceil($total / $limit);
?>

<?php include '../includes/header.php'; ?>
<div class="container mt-4" style="min-height: 80vh;">
  <h2>Product Management</h2>
  <a href="product_add.php" class="btn btn-primary mb-3"><i class="bi bi-patch-plus"></i>&nbsp; Add Product</a>

  <h4 class="mt-4">All Products</h4>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Image</th>
        <th>Name</th>
        <th>Short description</th>
        <th>Long description</th>
        <th>Price</th>
        <th style="width: 150px;">Edit</th>
        <th style="width: 150px;">Delete</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $p): ?>
        <tr>
        <td><img src="../uploads/<?= htmlspecialchars($p['photo']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="max-width: 100px; max-height: 100px;"></td>
          <td><?= htmlspecialchars($p['name']) ?></td>
          <td><?= htmlspecialchars($p['short_description']) ?></td>
          <td><?= htmlspecialchars($p['long_description']) ?></td>
          <td>$<?= htmlspecialchars($p['price']) ?></td>
          <td>
            <a href="product_edit.php?id=<?= $p['id'] ?>" class="btn btn-success btn-sm"><i class="bi bi-pen"></i>&nbsp;Edit</a>
          </td>
          <td>
    <form action="product_delete.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="id" value="<?= $p['id'] ?>">
        <button type="submit" class="btn btn-danger btn-sm">
            <i class="bi bi-trash3"></i>&nbsp;Delete
        </button>
    </form>
</td>

        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <nav>
    <ul class="pagination justify-content-center mt-4">
      <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page - 1 ?>"><i class="bi bi-arrow-left-circle"></i>&nbsp;Previous</a>
      </li>
      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
      <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page + 1 ?>">Next &nbsp;<i class="bi bi-arrow-right-circle"></i></a>
      </li>
    </ul>
  </nav>
</div>
<?php include '../includes/footer.php'; ?>