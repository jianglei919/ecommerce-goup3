<?php
session_start();
require_once 'config/Database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);

  $payload = json_encode([
    'username' => $username,
    'password' => $password
  ]);

  $ch = curl_init('http://localhost/ecommerce-goup3/api/users.php?action=login');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($httpCode === 200) {
    $user = json_decode($response, true);
    $_SESSION['user'] = $user['user'];
    header("Location: index.php");
    exit();
  } else {
    $error = "Invalid credentials";
  }
}
?>
<?php include 'includes/header.php'; ?>
<div class="container mt-4" style="max-width: 600px; min-height: 55vh;">
    <div class="w-100" style="max-width: 400px;">
  <h2>User Login</h2>
  <?php if (!empty($error)): ?>
    <p class='text-danger'><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
  <?php endif; ?>
  <form method="POST" aria-labelledby="login-title">
    <label for="username" class="form-label">Username</label>
    <input name="username" class="form-control mb-2" placeholder="Enter your username" aria-required="true" tabindex="1" required>

    <label for="password" class="form-label">Password</label>
    <input type="password" name="password" class="form-control mb-2" placeholder="Enter password" aria-required="true" tabindex="2" required>
    <button class="btn btn-primary" tabindex="3">Login</button>
  </form>
  <p class="mt-3">Don't have an account? <a href="register.php" tabindex="4">Register here</a>.</p>
    </div>
</div>
<?php include 'includes/footer.php'; ?>