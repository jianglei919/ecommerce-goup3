<?php
session_start();

$userId = isset($_GET['Id']) ? filter_var($_GET['Id'], FILTER_SANITIZE_NUMBER_INT) : 0;

if (!isset($_SESSION['user']) || !$_SESSION['user']['is_admin']) {
    header("Location: ../login.php");
    exit();
}

if ($userId <= 0) {
    die("Invalid User");
}

$userInfo = [];
$response = file_get_contents("http://localhost/ecommerce-goup3/api/users.php?id=$userId");
if ($response) {
    $userInfo = json_decode($response, true);
}

if (!$userInfo || isset($userInfo['message'])) {
    echo "<h2 class='text-center mt-5 text-danger'>User not found</h2>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isadmin = isset($_POST['isadmin']) && $_POST['isadmin'] === '1' ? 1 : 0;
    $payload = json_encode([
        "id" => $userId,
        "is_admin" => $isadmin
    ]);

    $ch = curl_init("http://localhost/ecommerce-goup3/api/users.php");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    $result = curl_exec($ch);
    curl_close($ch);

    header("Location: users_admin.php");
    exit();
}
?>

<?php include '../includes/header.php'; ?>
<div class="container mt-4" style="max-width: 600px; min-height: 55vh;">
  <h2>Update Admin Status</h2>
  <form method="POST" aria-labelledby="Update-Admin-Status">
    
    <label class="form-label">Username</label>
    <input type="text" class="form-control mb-2" value="<?= htmlspecialchars($userInfo['username'], ENT_QUOTES, 'UTF-8') ?>" disabled>
    
    <label class="form-label">Full Name</label>
    <input type="text" class="form-control mb-2" value="<?= htmlspecialchars($userInfo['name'], ENT_QUOTES, 'UTF-8') ?>" disabled>
    
    <label class="form-label">Email</label>
    <input type="email" class="form-control mb-2" value="<?= htmlspecialchars($userInfo['email'], ENT_QUOTES, 'UTF-8') ?>" disabled>

    <label class="form-label">Admin Status</label>
    <select name="isadmin" class="form-control mb-2">
        <option value="0" <?= $userInfo['is_admin'] == 0 ? 'selected' : '' ?>>No</option>
        <option value="1" <?= $userInfo['is_admin'] == 1 ? 'selected' : '' ?>>Yes</option>
    </select>

    <button type="submit" class="btn btn-primary">Update</button>
    <a href="users_admin.php" class="btn btn-secondary">Back</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>