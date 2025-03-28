<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>ElectroShop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-primary text-white mb-4">
    <div class="container">
      <a class="navbar-brand text-white" href="index.php">E-Commerce-Group3</a>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto">
          <?php if (isset($_SESSION['user'])): ?>
            <li class="nav-item">
              <a class="nav-link text-white" href="#">Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?></a>
            </li>
            <?php if ($_SESSION['user']['is_admin']): ?>
              <li class="nav-item">
                <a class="nav-link text-white" href="products_admin.php">Products</a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white" href="orders_admin.php">Orders</a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white" href="users_admin.php">Users</a>
              </li>
            <?php endif; ?>
            <li class="nav-item">
              <a class="nav-link text-white" href="<?= (isset($_SESSION['user']) && $_SESSION['user']['is_admin']) ? '../logout.php' : 'logout.php' ?>">
                <i class="bi bi-box-arrow-right"></i> Logout
              </a>
            </li>
            <?php if (!$_SESSION['user']['is_admin']): ?>
              <li class="nav-item">
                <a class="nav-link text-white" href="cart_view.php">
                  <i class="bi bi-cart"></i> Cart
                  <?php if (!empty($_SESSION['cart'])): ?>
                    <span class="badge bg-warning text-dark"><?= array_sum($_SESSION['cart']) ?></span>
                  <?php endif; ?>
                </a>
              </li>
            <?php endif; ?>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link text-white" href="login.php">Login</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="register.php">Register</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>