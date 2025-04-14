<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);
  $confirm_password = trim($_POST['confirm_password']);
  $name = trim($_POST['name']);
  $address = trim($_POST['address']);
  $phone = trim($_POST['phone']);
  $email = trim($_POST['email']);

  if ($password !== $confirm_password) {
    $error = "Passwords do not match.";
  } else {
    $payload = json_encode([
      'username' => $username,
      'password' => $password,
      'name' => $name,
      'address' => $address,
      'phone' => $phone,
      'email' => $email
    ]);

    $ch = curl_init('http://localhost/ecommerce-goup3/api/users.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
      header("Location: login.php");
      exit();
    } else {
      $error = "Registration failed. Please check your inputs.";
    }
  }
}
?>
<?php include 'includes/header.php'; ?>
<div class="container mt-4" style="max-width: 600px; min-height: 55vh;">
  <h2 id="registration-title">User Registration</h2>

  <!-- Show validation error message -->
  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"  role="alert" aria-live="polite">
      <ul>
        <li><?php echo $error; ?></li>
      </ul>
    </div>
  <?php endif; ?>

  <form method="POST" aria-labelledby="registration-title">
    <label for="username" class="form-label">Username</label>
    <input id="username" name="username" class="form-control mb-2" required placeholder="Enter your username" aria-required="true" tabindex="1">

    <label for="password" class="form-label">Password</label>
    <input id="password" type="password" name="password" class="form-control mb-2" required placeholder="Enter your password" 
            aria-required="true" tabindex="2">

    <label for="confirm_password" class="form-label">Confirm Password</label>
    <input id="confirm_password" type="password" name="confirm_password" class="form-control mb-2" required placeholder="Confirm your password"
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

    <button class="btn btn-primary" tabindex="7"><i class="bi bi-person-add"></i> Register</button>
    <button type="reset" class="btn btn-secondary" tabindex="8">Reset</button>
  </form>
</div>
<?php include 'includes/footer.php'; ?>
