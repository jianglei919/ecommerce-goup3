<?php
require_once 'config/Database.php';
require_once 'classes/User.php';

$dbObj = new Database();
$db = $dbObj->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $name = $_POST['name'];
  $address = $_POST['address'];
  $phone = $_POST['phone'];
  $email = $_POST['email'];

  $stmt = $db->prepare("INSERT INTO users (username, password, name, address, phone, email) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssss", $username, $password, $name, $address, $phone, $email);
  $success = $stmt->execute();

  if ($success) {
    header("Location: login.php");
    exit();
  }
}
?>
<?php include 'includes/header.php'; ?>
<div class="container mt-4" style="max-width: 600px; min-height: 80vh;">
  <h2>User Registration</h2>
  <form method="POST">
    <input name="username" class="form-control mb-2" required placeholder="Username">
    <input type="password" name="password" class="form-control mb-2" required placeholder="Password">
    <input name="name" class="form-control mb-2" required placeholder="Full Name">
    <input name="address" class="form-control mb-2" required placeholder="Address">
    <input name="phone" class="form-control mb-2" placeholder="Phone">
    <input name="email" type="email" class="form-control mb-2" required placeholder="Email">
    <button class="btn btn-success">Register</button>
  </form>
</div>
<?php include 'includes/footer.php'; ?>
