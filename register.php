<?php
require_once 'config/Database.php';
require_once 'classes/User.php';

$dbObj = new Database();
$db = $dbObj->getConnection();
if (!$db) {
  die('Database connection failed');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Sanitize inputs
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);
  $name = trim($_POST['name']);
  $address = trim($_POST['address']);
  $phone = trim($_POST['phone']);
  $email = trim($_POST['email']);

  // Validate and sanitize input to protect XSS
  $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
  $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
  $address = htmlspecialchars($address, ENT_QUOTES, 'UTF-8');
  $phone = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
  $email = filter_var($email, FILTER_SANITIZE_EMAIL);

  // Email Validation (Format Check)
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors['email'] = "Invalid email format.";
  }

  // Phone Validation (Canada Standard)
  $phonePattern = "/^(?:\+1\s?)?(?:\(\d{3}\)\s?|\d{3}[-.\s]?)\d{3}[-.\s]?\d{4}$/";
  if (!empty($phone) && !preg_match($phonePattern, $phone)) {
      $errors['phone'] = "Invalid Canadian phone number format.";
  }

  // Check for duplicate username or email
  $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
  $stmt->bind_param("ss", $username, $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
      $errors['duplicate'] = "Username or Email already exists. Please try a different one.";
  }
  $stmt->close();

  // Proceed if no errors
  if (empty($errors)) {
      // Hash the password securely
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);

      // Prepare the statement
      $stmt = $db->prepare("INSERT INTO users (username, password, name, address, phone, email) VALUES (?, ?, ?, ?, ?, ?)");

      //escape inputs to protect SQL Injection
      $username = mysqli_real_escape_string($db, $username);
      $name = mysqli_real_escape_string($db, $name);
      $address = mysqli_real_escape_string($db, $address);
      $phone = mysqli_real_escape_string($db, $phone);
      $email = mysqli_real_escape_string($db, $email);

      $stmt->bind_param("ssssss", $username, $hashed_password, $name, $address, $phone, $email);
      $success = $stmt->execute();

      if ($success) {
          header("Location: login.php");
          exit();
      } else {
          $errors['general'] = "Registration failed. Please try again.";
      }
  }
}
?>
<?php include 'includes/header.php'; ?>
<div class="container mt-4" style="max-width: 600px; min-height: 55vh;">
  <h2 id="registration-title">User Registration</h2>

  <!-- Show validation error message -->
  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger"  role="alert" aria-live="polite">
      <ul>
        <?php foreach ($errors as $error): ?>
          <li><?php echo $error; ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="POST" aria-labelledby="registration-title">
    <label for="username" class="form-label">Username</label>
    <input id="username" name="username" class="form-control mb-2" required placeholder="Enter your username" aria-required="true" tabindex="1">

    <label for="password" class="form-label">Password</label>
    <input id="password" type="password" name="password" class="form-control mb-2" required placeholder="Enter your password" 
            aria-required="true" tabindex="2">

    <label for="name" class="form-label">Full Name</label>
    <input id="name" name="name" class="form-control mb-2" required placeholder="Enter your full name" 
            aria-required="true" tabindex="3">

    <label for="address" class="form-label">Address</label>
    <input id="address" name="address" class="form-control mb-2" required placeholder="Enter your address" 
            aria-required="true" tabindex="4">

    <label for="phone" class="form-label">Phone</label>
    <input id="phone" name="phone" class="form-control mb-2" placeholder="Enter your phone number" 
            aria-describedby="phoneHelp" tabindex="5">

    <label for="email" class="form-label">Email</label>
    <input id="email" name="email" type="email" class="form-control mb-2" required placeholder="Enter your email" 
            aria-required="true" aria-describedby="emailHelp" tabindex="6">

    <button class="btn btn-primary" tabindex="7">Register</button>
    <button type="reset" class="btn btn-secondary" tabindex="8">Reset</button>
  </form>
</div>
<?php include 'includes/footer.php'; ?>
