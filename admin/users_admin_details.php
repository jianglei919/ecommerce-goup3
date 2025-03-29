<?php
session_start();
require_once '../config/Database.php';
require_once '../classes/User.php';

$db = (new Database())->getConnection();
$user = new User($db);

$userId = isset($_GET['Id']) ? filter_var($_GET['Id'], FILTER_SANITIZE_NUMBER_INT) : 0;

if ($userId <= 0) {
    die("Invalid User");
}

if (!isset($_SESSION['user']) || !$_SESSION['user']['is_admin']) {
  header("Location: ../login.php");
  exit();
}

$userInfo = $user->getUser($userId);

if (!$userInfo) {
    echo "<h2 class='text-center mt-5 text-danger'>User not found</h2>";
    exit();
}  

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isadmin = isset($_POST['isadmin']) && $_POST['isadmin'] === '1' ? 1 : 0;

    if ($user->updateAdminStatus($userId, $isadmin)) {
        header("Location: users_admin.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error updating admin status.</div>";
    }
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