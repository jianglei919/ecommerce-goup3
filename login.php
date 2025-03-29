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
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Sanitize and escape inputs
  $username = mysqli_real_escape_string($db, trim($_POST['username']));
  $password = trim($_POST['password']);

  // Use prepared statements to fetch user data
  $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  // fetch user data
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