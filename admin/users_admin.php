<?php
session_start();
require_once '../config/Database.php';
require_once '../classes/User.php';

$db = (new Database())->getConnection();
$user = new User($db);

if (!isset($_SESSION['user']) || !$_SESSION['user']['is_admin']) {
  header("Location: ../login.php");
  exit();
}

if (isset($_GET['delete'])) {
  $user->deleteUser($_GET['delete']);
  header("Location: users_admin.php");
  exit();
}

$users = $user->getAllUsers();
?>

<?php include '../includes/header.php'; ?>
<div class="container mt-4" style="min-height: 80vh;">
  <h2>User Management</h2>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Username</th>
        <th>Name</th>
        <th>Email</th>
        <th>Admin</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u): ?>
        <tr>
          <td><?= htmlspecialchars($u['username']) ?></td>
          <td><?= htmlspecialchars($u['name']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= $u['is_admin'] ? 'Yes' : 'No' ?></td>
          <td>
            <a href="?delete=<?= $u['id'] ?>" class="btn btn-danger btn-sm"
              onclick="return confirm('Delete this user?')">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include '../includes/footer.php'; ?>