<?php
session_start();
require_once 'config/Database.php';
require_once 'classes/User.php';

$dbObj = new Database();
$db = $dbObj->getConnection();
if (!$db) {
  die('Database connection failed');
}
$user = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($userData = $result->fetch_assoc()) {
    if (password_verify($password, $userData['password'])) {
      $_SESSION['user'] = $userData;
      header("Location: index.php");
      exit();
    }
  }
  $error = "Invalid credentials";
}
?>
<?php include 'includes/header.php'; ?>
<div class="container mt-4" style="max-width: 600px; min-height: 80vh;">
    <div class="w-100" style="max-width: 400px;">
  <h2>User Login</h2>
  <?php if (isset($error))
    echo "<p class='text-danger'>$error</p>"; ?>
  <form method="POST">
    <input name="username" class="form-control mb-2" required placeholder="Username">
    <input type="password" name="password" class="form-control mb-2" required placeholder="Password">
    <button class="btn btn-primary">Login</button>
  </form>
  <p class="mt-3">Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>
</div>
<?php include 'includes/footer.php'; ?>