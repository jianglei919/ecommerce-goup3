<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'config/Database.php';
$db = (new Database())->getConnection();
if (!$db) {
  echo "Connected to the database.";
}
require_once 'classes/Product.php';
$product = new Product($db);
$limit = 6; // Limit product list count
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

if (isset($_GET['search']) && $_GET['search'] !== '') {
  $products = $product->searchProducts($_GET['search'], $limit, $offset);
  $total = $product->countSearchResults($_GET['search']);
} else {
  $products = $product->getAllProducts($limit, $offset);
  $total = $product->countAllProducts();
}
$total_pages = ceil($total / $limit);
?>

<?php include 'includes/header.php'; ?>
<div class="container mt-4" style="min-height: 80vh;">
  <h1 class="mb-4">Home</h1>
  <?php if (isset($_SESSION['user']) && isset($_SESSION['user']['is_admin']) && $_SESSION['user']['is_admin']): ?>
    <div class="alert alert-info">Welcome, Administrator!</div>
    <div class="row">
      <div class="col-md-4">
        <div class="card text-bg-light mb-3">
          <div class="card-body">
            <h5 class="card-title">Manage Products</h5>
            <p class="card-text">View, add, and update product listings.</p>
            <a href="admin/products_admin.php" class="btn btn-primary">Go</a>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-bg-light mb-3">
          <div class="card-body">
            <h5 class="card-title">Manage Users</h5>
            <p class="card-text">Administer registered user accounts.</p>
            <a href="admin/users_admin.php" class="btn btn-primary">Go</a>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-bg-light mb-3">
          <div class="card-body">
            <h5 class="card-title">Manage Orders</h5>
            <p class="card-text">View and process customer orders.</p>
            <a href="admin/orders_admin.php" class="btn btn-primary">Go</a>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    
    <form method="GET" class="mb-4 d-flex" role="search">
      <input type="text" name="search" class="form-control me-2" placeholder="Search for products..."
        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
      <button class="btn btn-outline-success" type="submit">Search</button>
    </form>

    <div class="row">
      <?php foreach ($products as $prod): ?>
        <div class="col-md-4 mb-3 h-100">
          <div class="card h-100 shadow-sm border-0">
          <?php
            $photoPath = 'uploads/' . htmlspecialchars($prod['photo']);
            $imgSrc = file_exists($photoPath) && !empty($prod['photo']) ? $photoPath : 'uploads/default.jpg';
          ?>
          <div class="position-relative">
            <a href="product.php?id=<?= $prod['id'] ?>">
              <img src="<?= $imgSrc ?>" class="card-img-top" style="height: 300px; object-fit: cover;" alt="<?= htmlspecialchars($prod['name']) ?>">
            </a>
            <span class="badge bg-success position-absolute top-0 end-0 m-2 rounded-pill px-3 py-2 fs-6 shadow">$<?= number_format($prod['price'], 2) ?></span>
          </div>
            <div class="card-body">
              <h5 class="card-title text-primary fw-semibold">
                <a href="product.php?id=<?= $prod['id'] ?>" class="text-decoration-none text-primary"><?= htmlspecialchars($prod['name']) ?></a>
              </h5>
              <p class="card-text">$<?= number_format($prod['price'], 2) ?></p>
              <form method="POST" action="cart_add.php">
                <input type="hidden" name="product_id" value="<?= $prod['id'] ?>">
                <div class="d-flex gap-2">
                  <input type="number" name="quantity" min="1" value="1" class="form-control" style="max-width: 80px;">
                  <button class="btn btn-outline-primary flex-grow-1"><i class="bi bi-cart-plus me-1"></i> Add to Cart</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <nav aria-label="Page navigation">
      <ul class="pagination justify-content-center mt-4">
        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
          <a class="page-link"
            href="?<?= isset($_GET['search']) ? 'search=' . urlencode($_GET['search']) . '&' : '' ?>page=<?= max(1, $page - 1) ?>">Previous</a>
        </li>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
            <a class="page-link"
              href="?<?= isset($_GET['search']) ? 'search=' . urlencode($_GET['search']) . '&' : '' ?>page=<?= $i ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
          <a class="page-link"
            href="?<?= isset($_GET['search']) ? 'search=' . urlencode($_GET['search']) . '&' : '' ?>page=<?= min($total_pages, $page + 1) ?>">Next</a>
        </li>
      </ul>
    </nav>
  <?php endif; ?>  
</div>  
<?php include 'includes/footer.php'; ?>